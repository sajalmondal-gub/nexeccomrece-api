<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use App\Models\ComboOffer;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Get aggregated data for the mobile app home screen.
     */
    public function index()
    {
        // 1. Fetch Banners
        $banners = Banner::where('status', true)
            ->orderBy('order', 'asc')
            ->get()
            ->map(function($banner) {
                $banner->image_url = asset('storage/' . $banner->image);
                return $banner;
            });

        // 2. Fetch Featured Categories
        $featuredCategories = Category::where('is_active', true)
            ->where('is_featured', true)
            ->get();

        // 3. Fetch Deals
        // We eager load 'brand' and 'category' since products might need them for display
        $flashDeals = Product::with(['brand', 'category'])
            ->where('is_active', true)
            ->where('deal_type', 'flash')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($p) => $this->formatProduct($p));

        $weeklyDeals = Product::with(['brand', 'category'])
            ->where('is_active', true)
            ->where('deal_type', 'weekly')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($p) => $this->formatProduct($p));

        $monthlyDeals = Product::with(['brand', 'category'])
            ->where('is_active', true)
            ->where('deal_type', 'monthly')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($p) => $this->formatProduct($p));

        // 4. Fetch Featured Products (Standard carousel)
        $featuredProducts = Product::with(['brand', 'category'])
            ->where('is_active', true)
            ->where('is_featured', true)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($p) => $this->formatProduct($p));

        // 5. Fetch Combo Offers
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
            'data' => [
                'banners' => $banners,
                'featured_categories' => $featuredCategories,
                'flash_deals' => $flashDeals,
                'weekly_deals' => $weeklyDeals,
                'monthly_deals' => $monthlyDeals,
                'featured_products' => $featuredProducts,
                'combo_offers' => $comboOffers,
            ]
        ]);
    }

    /**
     * Helper to format product data for mobile consumption
     */
    private function formatProduct($product)
    {
        // Add full URL to primary image
        $product->image_url = str_starts_with($product->image, 'http') 
            ? $product->image 
            : asset('uploads/products/' . $product->image);
            
        // Final calculated price
        $product->final_price = $product->final_price;
        
        return $product;
    }
}
