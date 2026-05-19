<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Enforce order management authorization.
     */
    private function checkPermission(): void
    {
        if (!auth()->user()->hasPermissionTo('manage_orders') && !auth()->user()->hasRole('Super Admin')) {
            abort(403, 'Unauthorized access.');
        }
    }

    /**
     * Display a listing of orders.
     */
    public function index(): View
    {
        $this->checkPermission();
        $orders = Order::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Display the detailed order invoice page.
     */
    public function show(int $id): View
    {
        $this->checkPermission();
        $order = Order::with(['user', 'items.product', 'items.variant'])->findOrFail($id);

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update order and/or payment status.
     */
    public function updateStatus(Request $request, int $id): RedirectResponse
    {
        $this->checkPermission();
        $order = Order::findOrFail($id);

        $request->validate([
            'order_status' => 'required|string|in:pending,processing,shipped,delivered,cancelled',
            'payment_status' => 'required|string|in:pending,paid,failed,refunded',
        ]);

        $order->update([
            'order_status' => $request->order_status,
            'payment_status' => $request->payment_status,
        ]);

        return redirect()->route('admin.orders.show', $order->id)->with('success', 'Order status updated successfully!');
    }
}
