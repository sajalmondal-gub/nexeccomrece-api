<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ReviewController extends Controller
{
    /**
     * Enforce product/review management authorization.
     */
    private function checkPermission(): void
    {
        if (!auth()->user()->hasPermissionTo('manage_products') && !auth()->user()->hasRole('Super Admin')) {
            abort(403, 'Unauthorized access.');
        }
    }

    /**
     * Display a listing of product reviews.
     */
    public function index(): View
    {
        $this->checkPermission();
        $reviews = Review::with(['user', 'product'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.reviews.index', compact('reviews'));
    }

    /**
     * Approve the specified review.
     */
    public function approve(int $id): RedirectResponse
    {
        $this->checkPermission();
        $review = Review::findOrFail($id);
        $review->update(['approved' => true]);

        return redirect()->route('admin.reviews.index')->with('success', 'Review approved and published successfully!');
    }

    /**
     * Delete the specified review.
     */
    public function destroy(int $id): RedirectResponse
    {
        $this->checkPermission();
        $review = Review::findOrFail($id);
        $review->delete();

        return redirect()->route('admin.reviews.index')->with('success', 'Review deleted successfully!');
    }
}
