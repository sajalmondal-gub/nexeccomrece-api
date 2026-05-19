<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\RolePermissionController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// --- Web Application Front / Welcome Redirect ---
Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

// --- Local Testing Auto-Login Helper ---
// Automatically logs in the Super Admin seeded user in local development environment
Route::get('/admin/login-as-admin', function () {
    if (app()->environment('local')) {
        $admin = User::where('email', 'admin@nexcommerce.com')->first();
        if ($admin) {
            Auth::login($admin);
            return redirect()->route('admin.dashboard')->with('success', 'Logged in automatically as Super Admin Mercer!');
        }
    }
    return redirect()->route('admin.dashboard')->with('error', 'Auto-login user not found.');
})->name('admin.autologin');

// --- Admin Guest Authentication Routes ---
Route::middleware(['web'])->prefix('admin')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/login', [AuthController::class, 'login'])->name('admin.login.submit');
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('admin.forgot-password');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('admin.forgot-password.submit');
    Route::get('/reset-password', [AuthController::class, 'showResetPassword'])->name('admin.reset-password');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('admin.reset-password.submit');
});

// --- Main Admin Panel Routes Group ---
Route::middleware(['admin.auth'])->prefix('admin')->group(function () {
    
    // 1. Dashboard
    Route::get('/', function () {
        return redirect()->route('admin.dashboard');
    });
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // 2. Categories & Brands
    Route::get('/categories', [App\Http\Controllers\Admin\CategoryController::class, 'index'])->name('admin.categories.index');
    Route::post('/categories/store', [App\Http\Controllers\Admin\CategoryController::class, 'storeCategory'])->name('admin.categories.store');
    Route::delete('/categories/{id}', [App\Http\Controllers\Admin\CategoryController::class, 'destroyCategory'])->name('admin.categories.destroy');
    
    Route::post('/brands/store', [App\Http\Controllers\Admin\CategoryController::class, 'storeBrand'])->name('admin.brands.store');
    Route::delete('/brands/{id}', [App\Http\Controllers\Admin\CategoryController::class, 'destroyBrand'])->name('admin.brands.destroy');

    // 3. Products & Variants CRUD
    Route::get('/products', [App\Http\Controllers\Admin\ProductController::class, 'index'])->name('admin.products.index');
    Route::get('/products/create', [App\Http\Controllers\Admin\ProductController::class, 'create'])->name('admin.products.create');
    Route::post('/products/store', [App\Http\Controllers\Admin\ProductController::class, 'store'])->name('admin.products.store');
    Route::get('/products/{id}/edit', [App\Http\Controllers\Admin\ProductController::class, 'edit'])->name('admin.products.edit');
    Route::post('/products/{id}/update', [App\Http\Controllers\Admin\ProductController::class, 'update'])->name('admin.products.update');
    Route::delete('/products/{id}', [App\Http\Controllers\Admin\ProductController::class, 'destroy'])->name('admin.products.destroy');

    // 3.5 Combo Offers & Bundles CRUD
    Route::get('/combo-offers', [App\Http\Controllers\Admin\ComboOfferController::class, 'index'])->name('admin.combo-offers.index');
    Route::get('/combo-offers/create', [App\Http\Controllers\Admin\ComboOfferController::class, 'create'])->name('admin.combo-offers.create');
    Route::post('/combo-offers/store', [App\Http\Controllers\Admin\ComboOfferController::class, 'store'])->name('admin.combo-offers.store');
    Route::get('/combo-offers/{id}/edit', [App\Http\Controllers\Admin\ComboOfferController::class, 'edit'])->name('admin.combo-offers.edit');
    Route::put('/combo-offers/{id}/update', [App\Http\Controllers\Admin\ComboOfferController::class, 'update'])->name('admin.combo-offers.update');
    Route::delete('/combo-offers/{id}', [App\Http\Controllers\Admin\ComboOfferController::class, 'destroy'])->name('admin.combo-offers.destroy');

    // 4. Order & Transaction Management
    Route::get('/orders', [App\Http\Controllers\Admin\OrderController::class, 'index'])->name('admin.orders.index');
    Route::get('/orders/{id}', [App\Http\Controllers\Admin\OrderController::class, 'show'])->name('admin.orders.show');
    Route::post('/orders/{id}/update-status', [App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('admin.orders.update-status');

    // 5. Coupons Discount Management
    Route::get('/coupons', [App\Http\Controllers\Admin\CouponController::class, 'index'])->name('admin.coupons.index');
    Route::post('/coupons/store', [App\Http\Controllers\Admin\CouponController::class, 'store'])->name('admin.coupons.store');
    Route::delete('/coupons/{id}', [App\Http\Controllers\Admin\CouponController::class, 'destroy'])->name('admin.coupons.destroy');

    // 6. Review & Approval System
    Route::get('/reviews', [App\Http\Controllers\Admin\ReviewController::class, 'index'])->name('admin.reviews.index');
    Route::post('/reviews/{id}/approve', [App\Http\Controllers\Admin\ReviewController::class, 'approve'])->name('admin.reviews.approve');
    Route::delete('/reviews/{id}', [App\Http\Controllers\Admin\ReviewController::class, 'destroy'])->name('admin.reviews.destroy');

    // 7. Site Settings & Banners CRUD
    Route::get('/settings', [SettingController::class, 'index'])->name('admin.settings.index');
    Route::post('/settings/update', [SettingController::class, 'update'])->name('admin.settings.update');
    
    Route::get('/banners', [App\Http\Controllers\Admin\BannerController::class, 'index'])->name('admin.banners.index');
    Route::get('/banners/create', [App\Http\Controllers\Admin\BannerController::class, 'create'])->name('admin.banners.create');
    Route::post('/banners', [App\Http\Controllers\Admin\BannerController::class, 'store'])->name('admin.banners.store');
    Route::get('/banners/{id}/edit', [App\Http\Controllers\Admin\BannerController::class, 'edit'])->name('admin.banners.edit');
    Route::post('/banners/{id}', [App\Http\Controllers\Admin\BannerController::class, 'update'])->name('admin.banners.update');
    Route::delete('/banners/{id}', [App\Http\Controllers\Admin\BannerController::class, 'destroy'])->name('admin.banners.destroy');

    // 8. Roles & Spatie Permissions Matrix
    Route::get('/roles', [RolePermissionController::class, 'index'])->name('admin.roles.index');
    Route::post('/roles/store', [RolePermissionController::class, 'storeRole'])->name('admin.roles.store');
    Route::post('/roles/{id}/update', [RolePermissionController::class, 'updateRole'])->name('admin.roles.update');
    Route::delete('/roles/{id}', [RolePermissionController::class, 'destroyRole'])->name('admin.roles.destroy');

    // 9. Users Directory & Role Assignments
    Route::get('/users', [RolePermissionController::class, 'users'])->name('admin.users.index');
    Route::post('/users/store', [RolePermissionController::class, 'storeUser'])->name('admin.users.store');
    Route::post('/users/{id}/update-role', [RolePermissionController::class, 'updateUserRole'])->name('admin.users.update-role');
    Route::post('/profile/update', [App\Http\Controllers\Admin\AuthController::class, 'updateProfile'])->name('admin.profile.update');

    // 10. Logout Endpoint
    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->to('/');
    })->name('logout');
});
