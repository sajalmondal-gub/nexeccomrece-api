<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    /**
     * Get all active banners for the mobile app home screen.
     */
    public function index()
    {
        $banners = Banner::where('status', true)
            ->orderBy('order', 'asc')
            ->get();
            
        // Map image paths to absolute URLs for mobile consumption
        $banners->transform(function($banner) {
            $banner->image_url = asset('storage/' . $banner->image);
            return $banner;
        });

        return response()->json([
            'status' => 'success',
            'data' => $banners
        ]);
    }
}
