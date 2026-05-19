@extends('layouts.admin')

@section('title', 'NexCommerce Sales & Orders')
@section('page_title', 'Sales & Orders Registry')

@section('content')
    <div class="rounded-2xl bg-slate-900/40 border border-indigo-950/40 backdrop-blur-md p-6">
        <h3 class="text-lg font-bold text-white flex items-center gap-2 mb-6 border-b border-indigo-950/40 pb-4">
            <svg class="h-5 w-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2" />
            </svg>
            Processed Transactions
        </h3>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-300">
                <thead class="bg-indigo-950/30 text-xs font-bold uppercase tracking-wider text-slate-400">
                    <tr>
                        <th class="px-4 py-3 rounded-l-xl">Order Number</th>
                        <th class="px-4 py-3">Customer Details</th>
                        <th class="px-4 py-3">Total Purchase</th>
                        <th class="px-4 py-3">Payment status</th>
                        <th class="px-4 py-3">Order status</th>
                        <th class="px-4 py-3">Purchased At</th>
                        <th class="px-4 py-3 rounded-r-xl text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-indigo-950/20">
                    @forelse($orders as $order)
                        <tr class="hover:bg-slate-800/20 transition-colors">
                            <td class="px-4 py-4 font-bold text-slate-200">
                                {{ $order->order_number }}
                            </td>
                            <td class="px-4 py-4">
                                <p class="font-bold text-slate-200">{{ $order->user->name ?? 'Guest User' }}</p>
                                <p class="text-xs text-slate-400">{{ $order->user->email ?? '—' }}</p>
                            </td>
                            <td class="px-4 py-4 font-bold text-white">
                                ${{ number_format($order->total, 2) }}
                            </td>
                            <td class="px-4 py-4">
                                @if($order->payment_status === 'paid')
                                    <span class="inline-flex items-center rounded-full bg-emerald-500/10 px-2.5 py-0.5 text-xs font-semibold text-emerald-400 ring-1 ring-inset ring-emerald-500/20">Paid</span>
                                @elseif($order->payment_status === 'refunded')
                                    <span class="inline-flex items-center rounded-full bg-purple-500/10 px-2.5 py-0.5 text-xs font-semibold text-purple-400 ring-1 ring-inset ring-purple-500/20">Refunded</span>
                                @elseif($order->payment_status === 'failed')
                                    <span class="inline-flex items-center rounded-full bg-red-500/10 px-2.5 py-0.5 text-xs font-semibold text-red-400 ring-1 ring-inset ring-red-500/20">Failed</span>
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
                            <td class="px-4 py-4 text-right">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="inline-flex items-center gap-1.5 rounded-lg bg-slate-800 px-3.5 py-2 text-xs font-bold text-slate-300 hover:bg-slate-700 hover:text-white transition-colors" title="Invoice">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    Invoice
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-slate-500 font-medium">
                                No checkouts have been registered inside this system yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Section -->
        <div class="mt-6 border-t border-indigo-950/30 pt-4">
            {{ $orders->links() }}
        </div>
    </div>
@endsection
