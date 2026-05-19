@extends('layouts.admin')

@section('title', 'NexCommerce Products Registry')
@section('page_title', 'Products Registry')

@section('content')
    <div class="rounded-2xl bg-slate-900/40 border border-indigo-950/40 backdrop-blur-md p-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 border-b border-indigo-950/40 pb-4 gap-4">
            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                <svg class="h-5 w-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                Products Catalog
            </h3>
            
            <a href="{{ route('admin.products.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-purple-500/20 hover:from-purple-500 hover:to-indigo-500 transition-all duration-200">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Add New Product
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-300">
                <thead class="bg-indigo-950/30 text-xs font-bold uppercase tracking-wider text-slate-400">
                    <tr>
                        <th class="px-4 py-3 rounded-l-xl">Product</th>
                        <th class="px-4 py-3">Category / Brand</th>
                        <th class="px-4 py-3">Price</th>
                        <th class="px-4 py-3">Stock status</th>
                        <th class="px-4 py-3">Variants</th>
                        <th class="px-4 py-3">Visibility</th>
                        <th class="px-4 py-3 rounded-r-xl text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-indigo-950/20">
                    @forelse($products as $product)
                        <tr class="hover:bg-slate-800/20 transition-colors">
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="h-12 w-12 rounded-xl bg-indigo-950/40 border border-indigo-950/40 flex items-center justify-center text-purple-400 font-bold overflow-hidden shadow-inner shrink-0">
                                        <!-- Displays placeholder text if no image exists -->
                                        @if($product->image)
                                            <span class="text-xs uppercase font-bold">{{ substr($product->name, 0, 2) }}</span>
                                        @else
                                            <svg class="h-6 w-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-200">{{ $product->name }}</p>
                                        <p class="text-xs text-slate-400 font-medium font-mono uppercase tracking-wider">SKU: {{ $product->sku }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <p class="font-semibold text-slate-300">{{ $product->category->name ?? 'Uncategorized' }}</p>
                                <p class="text-xs text-purple-400 font-semibold">{{ $product->brand->name ?? 'Generic' }}</p>
                            </td>
                            <td class="px-4 py-4">
                                @if($product->sale_price)
                                    <span class="font-bold text-emerald-400">${{ number_format($product->sale_price, 2) }}</span>
                                    <span class="text-xs text-slate-500 line-through ml-1.5">${{ number_format($product->base_price, 2) }}</span>
                                @else
                                    <span class="font-bold text-white">${{ number_format($product->base_price, 2) }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-4">
                                @if($product->stock_qty > 0)
                                    <span class="inline-flex items-center rounded-full bg-emerald-500/10 px-2.5 py-0.5 text-xs font-semibold text-emerald-400 ring-1 ring-inset ring-emerald-500/20">
                                        In Stock ({{ $product->stock_qty }})
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-red-500/10 px-2.5 py-0.5 text-xs font-semibold text-red-400 ring-1 ring-inset ring-red-500/20">
                                        Out of Stock
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-xs font-semibold">
                                @if($product->variants->count() > 0)
                                    <div class="flex flex-wrap gap-1 max-w-xs">
                                        @foreach($product->variants as $var)
                                            <span class="inline-flex items-center rounded bg-slate-800 px-1.5 py-0.5 text-slate-300 font-mono ring-1 ring-inset ring-slate-700">
                                                {{ $var->attribute_value }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-slate-500 font-medium">No variants</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 space-y-1">
                                @if($product->is_featured)
                                    <span class="inline-block rounded-full bg-purple-500/10 px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider text-purple-400 ring-1 ring-inset ring-purple-500/20">Featured</span>
                                @endif
                                
                                @if($product->is_active)
                                    <span class="inline-block rounded-full bg-emerald-500/10 px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider text-emerald-400 ring-1 ring-inset ring-emerald-500/20">Active</span>
                                @else
                                    <span class="inline-block rounded-full bg-slate-500/10 px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider text-slate-400 ring-1 ring-inset ring-slate-500/20">Inactive</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <!-- Edit -->
                                    <a href="{{ route('admin.products.edit', $product->id) }}" class="p-1 rounded text-slate-400 hover:text-purple-400 hover:bg-slate-800 transition-colors" title="Edit">
                                        <svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-5 3l4-4L19 7l-3-3-4 4m0 0l-3 3v3h3z"/></svg>
                                    </a>
                                    
                                    <!-- Delete -->
                                    <form method="POST" action="{{ route('admin.products.destroy', $product->id) }}" onsubmit="return confirm('Are you sure you want to delete this product?')" class="m-0 inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1 rounded text-slate-500 hover:text-red-400 hover:bg-slate-800 transition-colors" title="Delete">
                                            <svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-slate-500 font-medium">
                                No products registered in this catalog yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Section -->
        <div class="mt-6 border-t border-indigo-950/30 pt-4">
            {{ $products->links() }}
        </div>
    </div>
@endsection
