<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ReviewController extends Controller
{
    /**
     * Submit a new customer review for moderation.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:5|max:1000',
        ]);

        $user = $request->user();

        // Optional: Check if the user already reviewed this product
        $existing = Review::where('user_id', $user->id)
            ->where('product_id', $request->product_id)
            ->first();

        if ($existing) {
            return response()->json([
                'status' => 'error',
                'message' => 'You have already submitted a review for this product.'
            ], 422);
        }

        $review = Review::create([
            'user_id' => $user->id,
            'product_id' => $request->product_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'approved' => false, // Set to false by default, requires admin desk approval!
            'images' => json_encode([]),
        ]);

        // Create a dynamic notification for review submission
        \App\Models\Notification::create([
            'user_id' => $user->id,
            'title' => '📝 Review Submitted for Moderation',
            'message' => 'Your review for product rating ' . $request->rating . '/5 stars has been received. Our moderator desk will review it shortly!',
            'is_read' => false,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Thank you! Your product review has been submitted and is awaiting administrator approval.',
            'data' => $review
        ], 201);
    }
}
