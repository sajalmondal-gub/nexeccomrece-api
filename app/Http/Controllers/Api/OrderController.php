<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * Display the customer's purchase history.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $orders = Order::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'total' => (float) $order->total,
                    'payment_method' => $order->payment_method,
                    'payment_status' => $order->payment_status,
                    'order_status' => $order->order_status,
                    'created_at' => $order->created_at->format('Y-m-d H:i'),
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => $orders
        ]);
    }

    /**
     * Show detailed receipt breakdown for a single customer order.
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $order = Order::with(['items.product', 'items.variant'])
            ->where('user_id', $user->id)
            ->findOrFail($id);

        $formattedItems = $order->items->map(function ($item) {
            return [
                'id' => $item->id,
                'product_name' => $item->product->name ?? 'Deleted Product',
                'variant_value' => $item->variant ? $item->variant->attribute_value : null,
                'price' => (float) $item->price,
                'quantity' => (int) $item->quantity,
                'subtotal' => $item->price * $item->quantity,
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'subtotal' => (float) $order->subtotal,
                'tax' => (float) $order->tax,
                'shipping_fee' => (float) $order->shipping_fee,
                'discount' => (float) $order->discount,
                'total' => (float) $order->total,
                'payment_method' => $order->payment_method,
                'payment_status' => $order->payment_status,
                'order_status' => $order->order_status,
                'shipping_address' => $order->shipping_address,
                'notes' => $order->notes,
                'created_at' => $order->created_at->format('Y-m-d H:i'),
                'items' => $formattedItems,
            ]
        ]);
    }

    /**
     * Perform cart checkout, compute coupons, and initiate the payment flow.
     */
    public function checkout(Request $request): JsonResponse
    {
        $request->validate([
            'shipping_address' => 'required|string',
            'payment_method' => 'required|string|in:stripe,cod',
            'coupon_code' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $user = $request->user();

        // 1. Fetch current cart items
        $cartItems = CartItem::with(['product', 'variant'])
            ->where('user_id', $user->id)
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Your shopping cart is empty.'
            ], 420);
        }

        // 2. Perform Stock Validation
        foreach ($cartItems as $item) {
            if ($item->variant_id) {
                if ($item->variant->stock_qty < $item->quantity) {
                    return response()->json([
                        'status' => 'error',
                        'message' => "Stock issue: Option '{$item->variant->attribute_value}' for '{$item->product->name}' only has {$item->variant->stock_qty} left."
                    ], 422);
                }
            } else {
                if ($item->product->stock_qty < $item->quantity) {
                    return response()->json([
                        'status' => 'error',
                        'message' => "Stock issue: '{$item->product->name}' only has {$item->product->stock_qty} units left."
                    ], 422);
                }
            }
        }

        // 3. Compute Billing Totals
        $subtotal = 0.0;
        foreach ($cartItems as $item) {
            $itemPrice = (float) $item->product->final_price;
            if ($item->variant_id) {
                $itemPrice += (float) $item->variant->price_modifier;
            }
            $subtotal += $itemPrice * $item->quantity;
        }

        // Apply Coupon if valid
        $discount = 0.0;
        $coupon = null;
        if ($request->filled('coupon_code')) {
            $coupon = Coupon::where('code', strtoupper($request->coupon_code))->first();
            if ($coupon && $coupon->isValidForOrder($subtotal)) {
                $discount = (float) $coupon->calculateDiscount($subtotal);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Coupon code is invalid, expired, or has reached its usage limit.'
                ], 422);
            }
        }

        // Calculate Shipping Fee ($10.00 flat fee, free for orders above $100)
        $shippingFee = $subtotal >= 100.00 ? 0.00 : 10.00;
        $tax = 0.00; // 0% tax for simplicity, expandable
        $total = ($subtotal - $discount) + $shippingFee + $tax;
        if ($total < 0) $total = 0.00;

        // Generate Unique Order Number
        $orderNumber = 'NEX-' . date('Ymd') . '-' . strtoupper(Str::random(6));

        // 4. Create the Order inside Database Transaction
        $order = DB::transaction(function () use ($user, $orderNumber, $subtotal, $tax, $shippingFee, $discount, $total, $request, $cartItems, $coupon) {
            $order = Order::create([
                'order_number' => $orderNumber,
                'user_id' => $user->id,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'shipping_fee' => $shippingFee,
                'discount' => $discount,
                'total' => $total,
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending',
                'order_status' => 'pending',
                'shipping_address' => $request->shipping_address,
                'notes' => $request->notes,
            ]);

            // Save order items
            foreach ($cartItems as $item) {
                $itemPrice = (float) $item->product->final_price;
                if ($item->variant_id) {
                    $itemPrice += (float) $item->variant->price_modifier;
                }

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'variant_id' => $item->variant_id,
                    'price' => $itemPrice,
                    'quantity' => $item->quantity,
                ]);

                // For COD, deduct stock immediately. For Stripe, stock is deducted upon webhook/success confirmation
                if ($request->payment_method === 'cod') {
                    if ($item->variant_id) {
                        $item->variant->decrement('stock_qty', $item->quantity);
                    } else {
                        $item->product->decrement('stock_qty', $item->quantity);
                    }
                }
            }

            // If coupon applied, increment usage count
            if ($coupon) {
                $coupon->increment('used_count');
            }

            // If COD, empty the cart
            if ($request->payment_method === 'cod') {
                CartItem::where('user_id', $user->id)->delete();
                $order->update([
                    'order_status' => 'processing',
                ]);
            }

            return $order;
        });

        // Create a dynamic notification for order placement
        \App\Models\Notification::create([
            'user_id' => $user->id,
            'title' => '🛍️ Order Placed successfully',
            'message' => 'Thank you for shopping with NEX! Your order #' . $order->order_number . ' for $' . number_format($order->total, 2) . ' has been registered successfully and is now in processing state.',
            'is_read' => false,
        ]);

        // 5. Initiate Payment Intent response
        if ($request->payment_method === 'stripe') {
            // Communicate with Stripe API or generate mock secret
            $stripeSecret = config('services.stripe.secret') ?? env('STRIPE_SECRET');
            
            if (!empty($stripeSecret)) {
                try {
                    \Stripe\Stripe::setApiKey($stripeSecret);
                    
                    // Create payment intent with amount in cents
                    $intent = \Stripe\PaymentIntent::create([
                        'amount' => round($total * 100),
                        'currency' => 'usd',
                        'metadata' => [
                            'order_id' => $order->id,
                            'order_number' => $order->order_number,
                            'user_id' => $user->id
                        ]
                    ]);
                    
                    $order->update([
                        'transaction_reference' => $intent->id
                    ]);

                    return response()->json([
                        'status' => 'success',
                        'message' => 'Stripe checkout initialized.',
                        'data' => [
                            'order_id' => $order->id,
                            'order_number' => $order->order_number,
                            'payment_method' => 'stripe',
                            'client_secret' => $intent->client_secret,
                            'total_amount' => $total,
                        ]
                    ]);
                } catch (\Exception $e) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Failed to initialize payment gateway: ' . $e->getMessage()
                    ], 500);
                }
            } else {
                // Fallback elegant mock flow for out-of-the-box local sandbox testing
                $mockClientSecret = 'pi_' . Str::random(24) . '_secret_' . Str::random(24);
                $order->update([
                    'transaction_reference' => 'ch_mock_' . Str::random(20)
                ]);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Stripe Mock Mode initialized successfully (No API keys provided).',
                    'data' => [
                        'order_id' => $order->id,
                        'order_number' => $order->order_number,
                        'payment_method' => 'stripe',
                        'client_secret' => $mockClientSecret,
                        'total_amount' => $total,
                        'mock_mode' => true
                    ]
                ]);
            }
        }

        // Cash on delivery response
        return response()->json([
            'status' => 'success',
            'message' => 'Order placed successfully (Cash on Delivery)!',
            'data' => [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'payment_method' => 'cod',
                'total_amount' => $total,
            ]
        ], 201);
    }
}
