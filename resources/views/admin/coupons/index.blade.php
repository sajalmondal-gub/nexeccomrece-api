@extends('layouts.admin')

@section('title', 'NexCommerce Promotional Campaigns')
@section('page_title', 'Promo Coupons Registry')

@section('content')
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
        
        <!-- Left Section: Coupons List (2/3 size) -->
        <div class="lg:col-span-2 rounded-2xl bg-slate-900/40 border border-indigo-950/40 backdrop-blur-md p-6">
            <h3 class="text-lg font-bold text-white flex items-center gap-2 mb-5 border-b border-indigo-950/40 pb-4">
                <svg class="h-5 w-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                </svg>
                Promotional Campaigns List
            </h3>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-300">
                    <thead class="bg-indigo-950/30 text-xs font-bold uppercase tracking-wider text-slate-400">
                        <tr>
                            <th class="px-4 py-3 rounded-l-xl">Coupon Code</th>
                            <th class="px-4 py-3">Discount Value</th>
                            <th class="px-4 py-3">Min Order</th>
                            <th class="px-4 py-3">Max Discount</th>
                            <th class="px-4 py-3">Usage Used / Limit</th>
                            <th class="px-4 py-3">Expiry Date</th>
                            <th class="px-4 py-3 rounded-r-xl text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-indigo-950/20">
                        @forelse($coupons as $coupon)
                            <tr class="hover:bg-slate-800/20 transition-colors">
                                <td class="px-4 py-3.5 font-bold text-slate-200">
                                    <span class="inline-block rounded bg-purple-500/10 px-2.5 py-1 text-xs font-mono font-bold tracking-wider text-purple-400 ring-1 ring-inset ring-purple-500/20">
                                        {{ $coupon->code }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-slate-200 font-semibold">
                                    @if($coupon->type === 'percentage')
                                        {{ number_format($coupon->value, 0) }}% Off
                                    @else
                                        ${{ number_format($coupon->value, 2) }} Off
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 text-slate-300 font-medium">
                                    ${{ number_format($coupon->min_order, 2) }}
                                </td>
                                <td class="px-4 py-3.5 text-slate-300 font-medium">
                                    {{ $coupon->max_discount ? '$' . number_format($coupon->max_discount, 2) : 'No Limit' }}
                                </td>
                                <td class="px-4 py-3.5 text-slate-300 font-medium font-mono">
                                    {{ $coupon->used_count }} / {{ $coupon->usage_limit ?? '&infin;' }}
                                </td>
                                <td class="px-4 py-3.5 text-xs font-medium text-slate-400">
                                    {{ $coupon->expires_at->format('Y-m-d H:i') }}
                                    @if($coupon->expires_at->isPast())
                                        <span class="inline-block rounded-full bg-red-500/15 px-1.5 py-0.5 text-[9px] font-bold uppercase tracking-wider text-red-400 ml-1">Expired</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 text-right">
                                    <form method="POST" action="{{ route('admin.coupons.destroy', $coupon->id) }}" onsubmit="return confirm('Are you sure you want to delete this coupon?')" class="m-0 inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1 rounded text-slate-500 hover:text-red-400 hover:bg-slate-800 transition-colors" title="Delete">
                                            <svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-slate-500 font-medium">
                                    No promotional coupons configured inside this system.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right Section: Add Coupon Form (1/3 size) -->
        <div class="rounded-2xl bg-slate-900/40 border border-indigo-950/40 backdrop-blur-md p-6 h-fit">
            <h3 class="text-base font-bold text-white mb-5 border-b border-indigo-950/40 pb-3 flex items-center gap-2">
                <svg class="h-4.5 w-4.5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Generate Promo Code
            </h3>
            
            <form method="POST" action="{{ route('admin.coupons.store') }}" class="space-y-4">
                @csrf
                
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Coupon Code *</label>
                    <input type="text" name="code" required class="w-full rounded-xl border border-indigo-950/50 bg-slate-950 px-4 py-2.5 text-sm text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500 placeholder-slate-700" placeholder="e.g. PURPLE20">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Discount Type</label>
                        <select name="type" required class="w-full rounded-xl border border-indigo-950/50 bg-slate-950 px-4 py-2.5 text-sm text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500">
                            <option value="percentage">Percentage (%)</option>
                            <option value="fixed">Fixed Cash ($)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Value *</label>
                        <input type="number" step="0.01" name="value" required class="w-full rounded-xl border border-indigo-950/50 bg-slate-950 px-4 py-2.5 text-sm text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500" placeholder="e.g. 20">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Min Order ($)</label>
                        <input type="number" step="0.01" name="min_order" class="w-full rounded-xl border border-indigo-950/50 bg-slate-950 px-4 py-2.5 text-sm text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500" placeholder="0.00">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Max discount ($)</label>
                        <input type="number" step="0.01" name="max_discount" class="w-full rounded-xl border border-indigo-950/50 bg-slate-950 px-4 py-2.5 text-sm text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500" placeholder="e.g. 50">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Expiry Date *</label>
                        <input type="date" name="expires_at" required class="w-full rounded-xl border border-indigo-950/50 bg-slate-950 px-4 py-2.5 text-sm text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Usage Limit</label>
                        <input type="number" name="usage_limit" class="w-full rounded-xl border border-indigo-950/50 bg-slate-950 px-4 py-2.5 text-sm text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500" placeholder="e.g. 100">
                    </div>
                </div>

                <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 py-3 text-sm font-bold text-white shadow-lg shadow-purple-500/20 hover:from-purple-500 hover:to-indigo-500 transition-all duration-200 mt-2">
                    Activate Promo Coupon
                </button>
            </form>
        </div>

    </div>
@endsection
