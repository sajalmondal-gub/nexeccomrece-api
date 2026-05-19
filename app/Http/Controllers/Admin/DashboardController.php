<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Review;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the admin panel dashboard with rich stats.
     */
    public function index(): View
    {
        // 1. Core financial and counts stats
        $totalSales = Order::where('payment_status', 'paid')->sum('total');
        $totalOrders = Order::count();
        $totalProducts = Product::count();
        $totalCustomers = User::role('Customer')->count();
        if ($totalCustomers === 0) {
            // Fallback in case Spatie roles aren't loaded properly
            $totalCustomers = User::count();
        }

        // 2. Low stock alert products (stock_qty <= 5)
        $lowStockProducts = Product::where('stock_qty', '<=', 5)
            ->with(['brand', 'category'])
            ->get();

        // 3. Recent 5 orders
        $recentOrders = Order::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // 4. Pending reviews approval count
        $pendingReviewsCount = Review::where('approved', false)->count();

        // 5. Recent reviews
        $recentReviews = Review::with(['user', 'product'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalSales',
            'totalOrders',
            'totalProducts',
            'totalCustomers',
            'lowStockProducts',
            'recentOrders',
            'pendingReviewsCount',
            'recentReviews'
        ));
    }
}
