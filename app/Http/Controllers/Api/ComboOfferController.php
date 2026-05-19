<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ComboOffer;
use Illuminate\Http\JsonResponse;

class ComboOfferController extends Controller
{
    /**
     * Get all active promotional combo offers.
     */
    public function index(): JsonResponse
    {
        $comboOffers = ComboOffer::with(['products' => function($query) {
            $query->where('is_active', true);
        }])
        ->where('is_active', true)
        ->get()
        ->map(function ($combo) {
            return [
                'id' => $combo->id,
                'name' => $combo->name,
                'slug' => $combo->slug,
                'description' => $combo->description,
                'price' => (float) $combo->price,
                'original_price' => $combo->original_price,
                'savings_amount' => $combo->savings_amount,
                'image_url' => asset($combo->image),
                'products' => $combo->products->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'slug' => $product->slug,
                        'base_price' => (float) $product->base_price,
                        'sale_price' => $product->sale_price ? (float) $product->sale_price : null,
                        'final_price' => (float) $product->final_price,
                        'quantity' => $product->pivot->quantity,
                        'image_url' => str_starts_with($product->image, 'http') ? $product->image : asset('uploads/products/' . $product->image),
                    ];
                }),
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $comboOffers
        ]);
    }
}
