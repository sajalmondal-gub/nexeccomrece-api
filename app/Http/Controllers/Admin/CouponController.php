<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CouponController extends Controller
{
    /**
     * Enforce product management authorization.
     */
    private function checkPermission(): void
    {
        if (!auth()->user()->hasPermissionTo('manage_products') && !auth()->user()->hasRole('Super Admin')) {
            abort(403, 'Unauthorized access.');
        }
    }

    /**
     * Display a listing of the coupons.
     */
    public function index(): View
    {
        $this->checkPermission();
        $coupons = Coupon::orderBy('created_at', 'desc')->get();

        return view('admin.coupons.index', compact('coupons'));
    }

    /**
     * Store a newly created coupon in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->checkPermission();
        $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code',
            'type' => 'required|string|in:fixed,percentage',
            'value' => 'required|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'min_order' => 'nullable|numeric|min:0',
            'expires_at' => 'required|date|after:today',
            'usage_limit' => 'nullable|integer|min:1',
        ]);

        Coupon::create([
            'code' => strtoupper($request->code),
            'type' => $request->type,
            'value' => $request->value,
            'max_discount' => $request->max_discount,
            'min_order' => $request->min_order ?? 0.00,
            'expires_at' => $request->expires_at,
            'usage_limit' => $request->usage_limit,
            'used_count' => 0,
        ]);

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon code generated successfully!');
    }

    /**
     * Remove the specified coupon from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $this->checkPermission();
        $coupon = Coupon::findOrFail($id);
        $coupon->delete();

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon code removed successfully!');
    }
}
