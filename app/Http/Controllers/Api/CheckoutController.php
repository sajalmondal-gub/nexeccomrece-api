<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function intent(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'shipping_address' => 'required|string',
            'guest_email' => 'nullable|email|required_without:user_id',
            'guest_phone' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $user = auth('sanctum')->user();
        $subtotal = 0;
        $orderItems = [];

        foreach ($request->items as $item) {
            $product = Product::findOrFail($item['product_id']);
            $price = $product->sale_price ?? $product->base_price;
            
            $subtotal += ($price * $item['quantity']);

            $orderItems[] = [
                'product_id' => $product->id,
                'price' => $price,
                'quantity' => $item['quantity']
            ];
        }

        $tax = $subtotal * 0.05; // 5% tax example
        $shipping_fee = 10.00; // Flat shipping rate
        $total = $subtotal + $tax + $shipping_fee;

        DB::beginTransaction();
        try {
            $orderNumber = 'ORD-' . strtoupper(Str::random(10));

            $order = Order::create([
                'order_number' => $orderNumber,
                'user_id' => $user ? $user->id : null,
                'guest_email' => $user ? null : $request->guest_email,
                'guest_phone' => $user ? null : $request->guest_phone,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'shipping_fee' => $shipping_fee,
                'total' => $total,
                'payment_method' => 'stripe',
                'payment_status' => 'pending',
                'order_status' => 'pending',
                'shipping_address' => $request->shipping_address,
                'notes' => $request->notes,
            ]);

            foreach ($orderItems as $item) {
                OrderItem::create(array_merge($item, ['order_id' => $order->id]));
            }

            // Initialize Stripe API
            Stripe::setApiKey(env('STRIPE_SECRET', 'sk_test_mock_for_dev'));

            // Create a PaymentIntent with amount and currency
            $paymentIntent = PaymentIntent::create([
                'amount' => (int)($total * 100), // Stripe requires cents
                'currency' => 'usd',
                'metadata' => [
                    'order_number' => $orderNumber,
                    'guest_email' => $request->guest_email ?? ($user->email ?? '')
                ],
            ]);

            $order->update(['transaction_reference' => $paymentIntent->id]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'clientSecret' => $paymentIntent->client_secret,
                'order_number' => $orderNumber,
                'total' => $total
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Checkout failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
