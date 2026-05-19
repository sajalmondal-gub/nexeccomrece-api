<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\StripeWebhookController;
use App\Http\Controllers\Api\CouponController;
use App\Http\Controllers\Api\NotificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public API Routes
|--------------------------------------------------------------------------
*/

// Authentication
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);

// Catalog Exploration
Route::get('/home', [\App\Http\Controllers\Api\HomeController::class, 'index']);
Route::get('/banners', [\App\Http\Controllers\Api\BannerController::class, 'index']);
Route::get('/combo-offers', [\App\Http\Controllers\Api\ComboOfferController::class, 'index']);
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{slug}', [ProductController::class, 'show']);
Route::get('/categories', [ProductController::class, 'categories']);
Route::get('/brands', [ProductController::class, 'brands']);

// Secure Stripe Webhook Callback
Route::post('/payment/stripe-webhook', [StripeWebhookController::class, 'handleWebhook']);

// Coupon Validation for Guest & Authenticated Bags
Route::post('/coupons/validate', [CouponController::class, 'validateCoupon']);

// Public Checkout & Mock Payment (Auth is optional for these routes)
Route::post('/checkout', [OrderController::class, 'checkout']);
Route::post('/payment/confirm-mock-payment', [StripeWebhookController::class, 'confirmMockPayment']);
Route::post('/orders/track', [OrderController::class, 'track']);

/*
|--------------------------------------------------------------------------
| Authenticated API Routes (via Laravel Sanctum token)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    
    // User Profile
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/profile/update', [AuthController::class, 'updateProfile']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::delete('/delete-account', [AuthController::class, 'deleteAccount']);

    // Notifications Feed
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);

    // Shopping Cart Management
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart', [CartController::class, 'store']);
    Route::put('/cart/{id}', [CartController::class, 'update']);
    Route::delete('/cart/{id}', [CartController::class, 'destroy']);
    Route::post('/cart/sync', [CartController::class, 'sync']);

    // Order History
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);

    // Review Submission
    Route::post('/reviews', [ReviewController::class, 'store']);
});
