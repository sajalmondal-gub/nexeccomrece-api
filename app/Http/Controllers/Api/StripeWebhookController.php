<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class StripeWebhookController extends Controller
{
    /**
     * Handle incoming official webhooks from Stripe with signature validation.
     */
    public function handleWebhook(Request $request): JsonResponse
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = env('STRIPE_WEBHOOK_SECRET');

        if (empty($endpointSecret)) {
            Log::warning('Stripe webhook received but STRIPE_WEBHOOK_SECRET is not configured.');
            return response()->json(['status' => 'ignored', 'message' => 'Webhook secret not set'], 200);
        }

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sigHeader, $endpointSecret
            );
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            return response()->json(['status' => 'error', 'message' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            return response()->json(['status' => 'error', 'message' => 'Invalid signature'], 400);
        }

        // Handle the payment_intent.succeeded event
        if ($event->type === 'payment_intent.succeeded') {
            $paymentIntent = $event->data->object;
            $orderId = $paymentIntent->metadata->order_id ?? null;

            if ($orderId) {
                $this->fulfillOrder($orderId, $paymentIntent->id);
            }
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Dev testing helper endpoint: Instantly fulfill a mock payment.
     * Accessible by the React Native client app for easy out-of-the-box checkouts.
     */
    public function confirmMockPayment(Request $request): JsonResponse
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
        ]);

        $order = Order::findOrFail($request->order_id);

        if ($order->payment_status === 'paid') {
            return response()->json([
                'status' => 'success',
                'message' => 'Order is already captured & paid.'
            ]);
        }

        $transactionId = 'ch_mock_' . Str::random(20);
        $this->fulfillOrder($order->id, $transactionId);

        return response()->json([
            'status' => 'success',
            'message' => 'Mock payment captured successfully. Inventory stock updated!'
        ]);
    }

    /**
     * Transition order status, deduct inventory, and flush user shopping cart.
     */
    private function fulfillOrder(int $orderId, string $transactionReference): void
    {
        DB::transaction(function () use ($orderId, $transactionReference) {
            $order = Order::with('items')->lockForUpdate()->find($orderId);

            if (!$order || $order->payment_status === 'paid') {
                return;
            }

            // 1. Update order payment & fulfillment stages
            $order->update([
                'payment_status' => 'paid',
                'order_status' => 'processing',
                'transaction_reference' => $transactionReference,
            ]);

            // 2. Deduct inventory stocks
            foreach ($order->items as $item) {
                if ($item->variant_id) {
                    $variant = ProductVariant::find($item->variant_id);
                    if ($variant) {
                        $variant->decrement('stock_qty', $item->quantity);
                    }
                } else {
                    $product = Product::find($item->product_id);
                    if ($product) {
                        $product->decrement('stock_qty', $item->quantity);
                    }
                }
            }

            // 3. Clear user cart
            CartItem::where('user_id', $order->user_id)->delete();
        });
    }
}
