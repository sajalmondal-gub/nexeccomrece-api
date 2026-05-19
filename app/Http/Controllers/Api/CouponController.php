<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CouponController extends Controller
{
    /**
     * Validate coupon eligibility and return discount specifications.
     */
    public function validateCoupon(Request $request): JsonResponse
    {
        $request->validate([
            'coupon_code' => 'required|string',
            'subtotal' => 'required|numeric|min:0',
        ]);

        $coupon = Coupon::where('code', strtoupper($request->coupon_code))->first();

        if (!$coupon) {
            return response()->json([
                'status' => 'error',
                'message' => 'The coupon code you entered is invalid.'
            ], 404);
        }

        if (!$coupon->isValidForOrder((float) $request->subtotal)) {
            // Provide specific helpful validation messaging
            if ($coupon->expires_at->isPast()) {
                $msg = 'This coupon has already expired.';
            } elseif ($coupon->usage_limit !== null && $coupon->used_count >= $coupon->usage_limit) {
                $msg = 'This coupon has reached its maximum usage limit.';
            } elseif ((float) $request->subtotal < (float) $coupon->min_order) {
                $msg = "A minimum purchase of $" . number_format($coupon->min_order, 2) . " is required to apply this coupon.";
            } else {
                $msg = 'This coupon is not valid for this order.';
            }

            return response()->json([
                'status' => 'error',
                'message' => $msg
            ], 422);
        }

        $discount = (float) $coupon->calculateDiscount((float) $request->subtotal);

        return response()->json([
            'status' => 'success',
            'message' => 'Coupon code applied successfully!',
            'data' => [
                'code' => $coupon->code,
                'type' => $coupon->type,
                'value' => (float) $coupon->value,
                'min_order' => (float) $coupon->min_order,
                'max_discount' => $coupon->max_discount ? (float) $coupon->max_discount : null,
                'discount_amount' => $discount,
            ]
        ]);
    }
}
