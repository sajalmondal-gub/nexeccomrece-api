@extends('layouts.admin')

@section('title', 'NexCommerce Dashboard')
@section('page_title', 'Analytics Dashboard')

@section('content')
    <!-- Statistics Grid -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-8">
        <!-- 1. Total Revenue Card -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-purple-600 to-indigo-800 p-6 shadow-xl shadow-purple-500/10 ring-1 ring-white/10">
            <div class="absolute -right-6 -bottom-6 text-white/5">
                <svg class="h-32 w-32" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17h-2v-2h2v2zm2.07-7.75l-.9.92C13.45 12.9 13 13.5 13 15h-2v-.5c0-1.1.45-2.1 1.17-2.83l1.24-1.26c.37-.36.59-.86.59-1.41 0-1.1-.9-2-2-2s-2 .9-2 2H7c0-2.76 2.24-5 5-5s5 2.24 5 5c0 1.04-.42 1.99-1.07 2.75z"/>
                </svg>
            </div>
            <div class="flex items-center justify-between">
                <p class="text-sm font-semibold text-purple-100 uppercase tracking-wider">Total Sales</p>
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/15 backdrop-blur-md">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-3xl font-bold tracking-tight text-white">${{ number_format($totalSales, 2) }}</h3>
                <p class="mt-1 text-xs text-purple-200">Reflected from successful transactions</p>
            </div>
        </div>

        <!-- 2. Total Orders Card -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-800 to-indigo-950 p-6 border border-indigo-950/60 shadow-lg shadow-indigo-950/20 ring-1 ring-white/5">
            <div class="flex items-center justify-between">
                <p class="text-sm font-semibold text-slate-400 uppercase tracking-wider">Total Orders</p>
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-purple-500/10 border border-purple-500/20">
                    <svg class="h-6 w-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-3xl font-bold tracking-tight text-white">{{ $totalOrders }}</h3>
                <p class="mt-1 text-xs text-slate-400">Total checkouts processed</p>
            </div>
        </div>

        <!-- 3. Total Products Card -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-800 to-indigo-950 p-6 border border-indigo-950/60 shadow-lg shadow-indigo-950/20 ring-1 ring-white/5">
            <div class="flex items-center justify-between">
                <p class="text-sm font-semibold text-slate-400 uppercase tracking-wider">Products Catalog</p>
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-500/10 border border-indigo-500/20">
                    <svg class="h-6 w-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-3xl font-bold tracking-tight text-white">{{ $totalProducts }}</h3>
                <p class="mt-1 text-xs text-slate-400">Active catalog items</p>
            </div>
        </div>

        <!-- 4. Total Customers Card -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-800 to-indigo-950 p-6 border border-indigo-950/60 shadow-lg shadow-indigo-950/20 ring-1 ring-white/5">
            <div class="flex items-center justify-between">
                <p class="text-sm font-semibold text-slate-400 uppercase tracking-wider">Registered Users</p>
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-500/10 border border-emerald-500/20">
                    <svg class="h-6 w-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-3xl font-bold tracking-tight text-white">{{ $totalCustomers }}</h3>
                <p class="mt-1 text-xs text-slate-400">Total customer database size</p>
            </div>
        </div>
    </div>

    <!-- Low Stock Warning Alert banner -->
    @if($lowStockProducts->count() > 0)
        <div class="mb-8 rounded-2xl border border-red-500/20 bg-gradient-to-br from-red-950/30 to-slate-900/90 p-5 shadow-lg">
            <div class="flex items-start gap-4">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-red-500/10 text-red-400 border border-red-500/20">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <h4 class="text-base font-semibold text-red-400">Inventory Alert — Low Stock Detected</h4>
                    <p class="mt-1 text-sm text-slate-400">The following products are running extremely low (5 units or less) and require immediate restocking:</p>
                    <div class="mt-3 flex flex-wrap gap-2.5">
                        @foreach($lowStockProducts as $p)
                            <span class="inline-flex items-center rounded-lg bg-red-500/5 px-3 py-1.5 text-xs font-semibold text-red-300 ring-1 ring-inset ring-red-500/20">
                                {{ $p->name }} ({{ $p->stock_qty }} left)
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Split columns for orders & reviews -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        
        <!-- Left Section: Recent Orders (Take 2/3 space) -->
        <div class="lg:col-span-2 rounded-2xl bg-slate-900/40 border border-indigo-950/40 backdrop-blur-md p-6">
            <div class="flex items-center justify-between mb-5 border-b border-indigo-950/40 pb-4">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <svg class="h-5 w-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    Recent Orders
                </h3>
                <a href="{{ route('admin.orders.index') }}" class="text-xs font-semibold text-purple-400 hover:text-purple-300 transition-colors uppercase tracking-wider">
                    View All Orders &rarr;
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-300">
                    <thead class="bg-indigo-950/30 text-xs font-bold uppercase tracking-wider text-slate-400">
                        <tr>
                            <th class="px-4 py-3 rounded-l-xl">Order Number</th>
                            <th class="px-4 py-3">Customer</th>
                            <th class="px-4 py-3">Total</th>
                            <th class="px-4 py-3">Payment</th>
                            <th class="px-4 py-3">Shipment</th>
                            <th class="px-4 py-3 rounded-r-xl">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-indigo-950/20">
                        @forelse($recentOrders as $order)
                            <tr class="hover:bg-slate-800/20 transition-colors">
                                <td class="px-4 py-4 font-bold text-slate-200">
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="hover:text-purple-400 transition-colors">
                                        {{ $order->order_number }}
                                    </a>
                                </td>
                                <td class="px-4 py-4 text-slate-300 font-medium">
                                    {{ $order->user->name ?? 'Guest User' }}
                                </td>
                                <td class="px-4 py-4 font-semibold text-white">
                                    ${{ number_format($order->total, 2) }}
                                </td>
                                <td class="px-4 py-4">
                                    @if($order->payment_status === 'paid')
                                        <span class="inline-flex items-center rounded-full bg-emerald-500/10 px-2.5 py-0.5 text-xs font-semibold text-emerald-400 ring-1 ring-inset ring-emerald-500/20">Paid</span>
                                    @elseif($order->payment_status === 'refunded')
                                        <span class="inline-flex items-center rounded-full bg-purple-500/10 px-2.5 py-0.5 text-xs font-semibold text-purple-400 ring-1 ring-inset ring-purple-500/20">Refunded</span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-amber-500/10 px-2.5 py-0.5 text-xs font-semibold text-amber-400 ring-1 ring-inset ring-amber-500/20">Pending</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4">
                                    @if($order->order_status === 'delivered')
                                        <span class="inline-flex items-center rounded-full bg-emerald-500/10 px-2.5 py-0.5 text-xs font-semibold text-emerald-400 ring-1 ring-inset ring-emerald-500/20">Delivered</span>
                                    @elseif($order->order_status === 'shipped')
                                        <span class="inline-flex items-center rounded-full bg-sky-500/10 px-2.5 py-0.5 text-xs font-semibold text-sky-400 ring-1 ring-inset ring-sky-500/20">Shipped</span>
                                    @elseif($order->order_status === 'processing')
                                        <span class="inline-flex items-center rounded-full bg-indigo-500/10 px-2.5 py-0.5 text-xs font-semibold text-indigo-400 ring-1 ring-inset ring-indigo-500/20">Processing</span>
                                    @elseif($order->order_status === 'cancelled')
                                        <span class="inline-flex items-center rounded-full bg-red-500/10 px-2.5 py-0.5 text-xs font-semibold text-red-400 ring-1 ring-inset ring-red-500/20">Cancelled</span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-slate-500/10 px-2.5 py-0.5 text-xs font-semibold text-slate-400 ring-1 ring-inset ring-slate-500/20">Pending</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-xs font-medium text-slate-400">
                                    {{ $order->created_at->format('M d, Y H:i') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-slate-500 font-medium">
                                    No orders have been placed yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right Section: Recent Reviews & Alerts (Take 1/3 space) -->
        <div class="rounded-2xl bg-slate-900/40 border border-indigo-950/40 backdrop-blur-md p-6">
            <div class="flex items-center justify-between mb-5 border-b border-indigo-950/40 pb-4">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <svg class="h-5 w-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.907c.961 0 1.371 1.24.588 1.81l-3.97 2.883a1 1 0 00-.364 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.971-2.883a1 1 0 00-1.17 0l-3.97 2.883c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.364-1.118L2.49 10.118c-.783-.57-.373-1.81.588-1.81h4.906a1 1 0 00.951-.69l1.519-4.674z" />
                    </svg>
                    Recent Reviews
                    @if($pendingReviewsCount > 0)
                        <span class="inline-flex items-center rounded-full bg-purple-500/10 px-2 py-0.5 text-xs font-semibold text-purple-400 ring-1 ring-inset ring-purple-500/20 animate-pulse">
                            {{ $pendingReviewsCount }} new
                        </span>
                    @endif
                </h3>
                <a href="{{ route('admin.reviews.index') }}" class="text-xs font-semibold text-purple-400 hover:text-purple-300 transition-colors uppercase tracking-wider">
                    Approve List
                </a>
            </div>

            <div class="space-y-4">
                @forelse($recentReviews as $review)
                    <div class="rounded-xl border border-indigo-950/20 bg-indigo-950/5 p-4 transition-all duration-200 hover:border-purple-500/30">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-semibold text-slate-200">{{ $review->user->name ?? 'Verified Buyer' }}</span>
                            <div class="flex items-center gap-0.5">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="h-4.5 w-4.5 {{ $i <= $review->rating ? 'text-amber-400' : 'text-slate-700' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                            </div>
                        </div>
                        <p class="mt-1.5 text-xs font-semibold text-purple-300 truncate">on {{ $review->product->name ?? 'Product' }}</p>
                        <p class="mt-2 text-sm text-slate-400 italic font-medium leading-relaxed">
                            "{{ Str::limit($review->comment, 80) }}"
                        </p>
                        
                        <div class="mt-3 flex items-center justify-between text-xs">
                            <span class="text-slate-500 font-medium">{{ $review->created_at->diffForHumans() }}</span>
                            
                            @if(!$review->approved)
                                <form method="POST" action="{{ route('admin.reviews.approve', $review->id) }}" class="m-0">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center gap-1 rounded bg-purple-600 px-2 py-1 font-bold text-white hover:bg-purple-500 transition-colors">
                                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Approve
                                    </button>
                                </form>
                            @else
                                <span class="inline-flex items-center text-emerald-400 font-semibold gap-1">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    Live
                                </span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center text-slate-500 py-8 font-medium">
                        No reviews submitted yet.
                    </div>
                @endforelse
            </div>
        </div>

    </div>
@endsection
