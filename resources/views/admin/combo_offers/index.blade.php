@extends('layouts.admin')

@section('title', 'Combo Offers — NexCommerce')
@section('page_title', 'Promotional Bundle Offers')

@section('content')
    <div class="space-y-8">
        
        <!-- Header Actions -->
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-semibold text-slate-400">Bundle products together at a special promotional rate for mobile users.</p>
            </div>
            
            <a href="{{ route('admin.combo-offers.create') }}" class="inline-flex items-center gap-1.5 rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 px-4 py-2.5 text-xs font-bold text-white shadow-lg shadow-purple-500/20 hover:from-purple-500 hover:to-indigo-500 transition-all duration-200">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Create Combo Bundle
            </a>
        </div>

        <!-- Combo Offers List Table -->
        <div class="rounded-2xl bg-slate-900/40 border border-indigo-950/40 backdrop-blur-md p-6">
            <h3 class="text-base font-bold text-white mb-5 border-b border-indigo-950/40 pb-4 flex items-center gap-2">
                <svg class="h-5 w-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                Bundles & Combo Offers Registry
            </h3>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-300">
                    <thead class="bg-indigo-950/30 text-xs font-bold uppercase tracking-wider text-slate-400">
                        <tr>
                            <th class="px-4 py-3 rounded-l-xl">Combo Offer Name</th>
                            <th class="px-4 py-3">Included Products</th>
                            <th class="px-4 py-3">Combo Price</th>
                            <th class="px-4 py-3">Original Combined</th>
                            <th class="px-4 py-3">Savings</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 rounded-r-xl text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-indigo-950/20">
                        @forelse($comboOffers as $combo)
                            <tr class="hover:bg-slate-800/20 transition-colors">
                                <td class="px-4 py-3.5 font-bold text-slate-200 flex items-center gap-3">
                                    <div class="h-10 w-10 rounded-lg overflow-hidden bg-slate-950 border border-indigo-950/40 flex items-center justify-center">
                                        <img class="w-full h-full object-cover" src="{{ asset($combo->image) }}" alt="Combo Thumbnail">
                                    </div>
                                    <div>
                                        <span class="block text-slate-200">{{ $combo->name }}</span>
                                        <span class="block text-[10px] font-semibold uppercase tracking-wider text-slate-500">{{ $combo->slug }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 text-xs text-slate-300 max-w-[200px] truncate">
                                    <ul class="list-disc pl-4 space-y-0.5">
                                        @foreach($combo->products as $prod)
                                            <li>{{ $prod->name }} <span class="text-[10px] text-slate-500">(x{{ $prod->pivot->quantity }})</span></li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td class="px-4 py-3.5 font-bold text-purple-400">${{ number_format($combo->price, 2) }}</td>
                                <td class="px-4 py-3.5 text-slate-400 text-xs line-through">${{ number_format($combo->original_price, 2) }}</td>
                                <td class="px-4 py-3.5">
                                    <span class="inline-flex items-center rounded bg-purple-500/10 px-2 py-0.5 text-xs font-bold text-purple-400 ring-1 ring-inset ring-purple-500/20">
                                        Save ${{ number_format($combo->savings_amount, 2) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5">
                                    @if($combo->is_active)
                                        <span class="inline-flex items-center rounded-full bg-emerald-500/10 px-2 py-0.5 text-xs font-semibold text-emerald-400 ring-1 ring-inset ring-emerald-500/20">Active</span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-slate-500/10 px-2 py-0.5 text-xs font-semibold text-slate-400 ring-1 ring-inset ring-slate-500/20">Disabled</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 text-right space-x-2">
                                    <a href="{{ route('admin.combo-offers.edit', $combo->id) }}" class="p-1.5 rounded bg-slate-800/60 hover:bg-purple-600/20 text-slate-400 hover:text-purple-300 transition-colors inline-block" title="Edit">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    </a>
                                    <form method="POST" action="{{ route('admin.combo-offers.destroy', $combo->id) }}" onsubmit="return confirm('Are you sure you want to delete this combo bundle?')" class="m-0 inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded bg-slate-800/60 hover:bg-red-600/20 text-slate-400 hover:text-red-400 transition-colors" title="Delete">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-slate-500 font-medium">
                                    No promotional combo offers currently available. Click "Create Combo Bundle" to get started!
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $comboOffers->links() }}
            </div>
        </div>
    </div>
@endsection
