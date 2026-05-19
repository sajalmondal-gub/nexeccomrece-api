@extends('layouts.admin')

@section('title', 'Order Invoice ' . $order->order_number . ' — NexCommerce')
@section('page_title', 'Order Invoice #' . $order->order_number)

@section('content')
    <!-- Back to registry link -->
    <div class="mb-6">
        <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-purple-400 hover:text-purple-300 transition-colors">
            &larr; Return to Sales Registry
        </a>
    </div>

    <!-- State controllers form panel -->
    <div class="rounded-2xl bg-slate-900/40 border border-indigo-950/40 backdrop-blur-md p-6 mb-8">
        <h4 class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-4">Adjust Status Parameters</h4>
        <form method="POST" action="{{ route('admin.orders.update-status', $order->id) }}" class="flex flex-col sm:flex-row items-end gap-4">
            @csrf
            
            <div class="w-full sm:w-64">
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Order Stage</label>
                <select name="order_status" class="w-full rounded-xl border border-indigo-950/50 bg-slate-950 px-4 py-2.5 text-sm text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500">
                    <option value="pending" {{ $order->order_status === 'pending' ? 'selected' : '' }}>Pending Processing</option>
                    <option value="processing" {{ $order->order_status === 'processing' ? 'selected' : '' }}>Processing</option>
                    <option value="shipped" {{ $order->order_status === 'shipped' ? 'selected' : '' }}>Shipped (Out for Delivery)</option>
                    <option value="delivered" {{ $order->order_status === 'delivered' ? 'selected' : '' }}>Delivered Successfully</option>
                    <option value="cancelled" {{ $order->order_status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <div class="w-full sm:w-64">
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Payment State</label>
                <select name="payment_status" class="w-full rounded-xl border border-indigo-950/50 bg-slate-950 px-4 py-2.5 text-sm text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500">
                    <option value="pending" {{ $order->payment_status === 'pending' ? 'selected' : '' }}>Pending Capture</option>
                    <option value="paid" {{ $order->payment_status === 'paid' ? 'selected' : '' }}>Captured / Paid</option>
                    <option value="failed" {{ $order->payment_status === 'failed' ? 'selected' : '' }}>Failed Capture</option>
                    <option value="refunded" {{ $order->payment_status === 'refunded' ? 'selected' : '' }}>Refunded</option>
                </select>
            </div>

            <button type="submit" class="w-full sm:w-auto rounded-xl bg-purple-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-purple-500/20 hover:bg-purple-500 transition-all">
                Commit State Changes
            </button>
        </form>
    </div>

    <!-- Invoice Columns split -->
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
        
        <!-- Left Section: Ordered Items breakdown (2/3 size) -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Items Card -->
            <div class="rounded-2xl bg-slate-900/40 border border-indigo-950/40 backdrop-blur-md p-6">
                <h3 class="text-base font-bold text-white mb-5 border-b border-indigo-950/40 pb-3 flex items-center gap-2">
                    <svg class="h-4.5 w-4.5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    Items Summary
                </h3>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-300">
                        <thead class="bg-indigo-950/30 text-xs font-bold uppercase tracking-wider text-slate-400">
                            <tr>
                                <th class="px-4 py-3 rounded-l-xl">Product Item</th>
                                <th class="px-4 py-3">Price</th>
                                <th class="px-4 py-3 text-center">Quantity</th>
                                <th class="px-4 py-3 rounded-r-xl text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-indigo-950/20">
                            @foreach($order->items as $item)
                                <tr class="hover:bg-slate-800/10 transition-colors">
                                    <td class="px-4 py-4">
                                        <p class="font-bold text-slate-200">{{ $item->product->name ?? 'Deleted Item' }}</p>
                                        @if($item->variant)
                                            <p class="text-xs text-purple-400 font-semibold mt-0.5">
                                                Selected Option: {{ $item->variant->attribute_name }}: {{ $item->variant->attribute_value }}
                                            </p>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 font-semibold text-slate-300">
                                        ${{ number_format($item->price, 2) }}
                                    </td>
                                    <td class="px-4 py-4 text-center font-bold text-slate-200">
                                        {{ $item->quantity }}
                                    </td>
                                    <td class="px-4 py-4 text-right font-bold text-white">
                                        ${{ number_format($item->price * $item->quantity, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Billing Summary panel -->
                <div class="mt-8 border-t border-indigo-950/40 pt-6">
                    <div class="w-full sm:w-80 ml-auto space-y-3.5 text-sm font-semibold">
                        <div class="flex justify-between text-slate-400">
                            <span>Subtotal</span>
                            <span>${{ number_format($order->subtotal, 2) }}</span>
                        </div>
                        @if($order->discount > 0)
                            <div class="flex justify-between text-purple-400">
                                <span>Coupon Code discount</span>
                                <span>-${{ number_format($order->discount, 2) }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between text-slate-400">
                            <span>Sales Tax (0%)</span>
                            <span>${{ number_format($order->tax, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-slate-400">
                            <span>Shipping & Handling</span>
                            <span>${{ number_format($order->shipping_fee, 2) }}</span>
                        </div>
                        <div class="h-px bg-indigo-950/40 my-3"></div>
                        <div class="flex justify-between text-white text-lg font-bold">
                            <span>Grand Total</span>
                            <span class="bg-gradient-to-r from-purple-400 to-indigo-400 bg-clip-text text-transparent">${{ number_format($order->total, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Section: Details & Shipping (1/3 size) -->
        <div class="space-y-8">
            
            <!-- Customer / User Details -->
            <div class="rounded-2xl bg-slate-900/40 border border-indigo-950/40 backdrop-blur-md p-6">
                <h3 class="text-base font-bold text-white mb-5 border-b border-indigo-950/40 pb-3">Buyer Profile</h3>
                <div class="space-y-3 text-sm">
                    <div>
                        <p class="text-xs text-slate-500 uppercase font-semibold">Customer Name</p>
                        <p class="font-bold text-slate-200 mt-0.5">{{ $order->user->name ?? 'Anonymous Guest' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 uppercase font-semibold">Contact Email</p>
                        <p class="font-semibold text-slate-300 mt-0.5">{{ $order->user->email ?? '—' }}</p>
                    </div>
                </div>
            </div>

            <!-- Shipping Address Details -->
            <div class="rounded-2xl bg-slate-900/40 border border-indigo-950/40 backdrop-blur-md p-6">
                <h3 class="text-base font-bold text-white mb-5 border-b border-indigo-950/40 pb-3">Delivery Logistics</h3>
                <div class="space-y-3 text-sm">
                    <div>
                        <p class="text-xs text-slate-500 uppercase font-semibold">Shipping Address</p>
                        <p class="font-medium text-slate-200 mt-1 leading-relaxed whitespace-pre-line">{{ $order->shipping_address }}</p>
                    </div>
                    @if($order->notes)
                        <div class="mt-4 border-t border-indigo-950/30 pt-3">
                            <p class="text-xs text-slate-500 uppercase font-semibold">Special Instructions</p>
                            <p class="font-medium text-slate-400 mt-1 leading-relaxed italic">"{{ $order->notes }}"</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Payment & Transaction details -->
            <div class="rounded-2xl bg-slate-900/40 border border-indigo-950/40 backdrop-blur-md p-6">
                <h3 class="text-base font-bold text-white mb-5 border-b border-indigo-950/40 pb-3">Payment Reference</h3>
                <div class="space-y-3 text-sm">
                    <div>
                        <p class="text-xs text-slate-500 uppercase font-semibold">Method Used</p>
                        <p class="font-bold text-slate-200 mt-0.5 uppercase">{{ $order->payment_method }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 uppercase font-semibold">Transaction Reference</p>
                        <p class="font-mono text-xs font-semibold text-purple-300 mt-1 truncate" title="{{ $order->transaction_reference ?? 'Pending webhook' }}">
                            {{ $order->transaction_reference ?? 'N/A' }}
                        </p>
                    </div>
                </div>
            </div>

        </div>
        
    </div>
@endsection
