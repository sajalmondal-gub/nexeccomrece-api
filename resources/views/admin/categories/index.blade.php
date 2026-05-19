@extends('layouts.admin')

@section('title', 'NexCommerce Catalog Structure')
@section('page_title', 'Categories & Brands Structure')

@section('content')
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
        
        <!-- ==================== LEFT COLUMN: CATEGORIES ==================== -->
        <div class="space-y-8">
            <!-- Categories List Card -->
            <div class="rounded-2xl bg-slate-900/40 border border-indigo-950/40 backdrop-blur-md p-6">
                <h3 class="text-lg font-bold text-white flex items-center gap-2 mb-5 border-b border-indigo-950/40 pb-4">
                    <svg class="h-5 w-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    Categories Registry
                </h3>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-300">
                        <thead class="bg-indigo-950/30 text-xs font-bold uppercase tracking-wider text-slate-400">
                            <tr>
                                <th class="px-4 py-3 rounded-l-xl">Name</th>
                                <th class="px-4 py-3">Slug</th>
                                <th class="px-4 py-3">Parent</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Featured</th>
                                <th class="px-4 py-3 rounded-r-xl text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-indigo-950/20">
                            @forelse($categories as $category)
                                <tr class="hover:bg-slate-800/20 transition-colors">
                                    <td class="px-4 py-3.5 font-bold text-slate-200 flex items-center gap-2">
                                        <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-purple-500/10 text-purple-400 ring-1 ring-inset ring-purple-500/20">
                                            <span class="text-xs uppercase font-bold">{{ substr($category->icon ?? 'T', 0, 2) }}</span>
                                        </span>
                                        {{ $category->name }}
                                    </td>
                                    <td class="px-4 py-3.5 text-xs font-medium text-slate-400">{{ $category->slug }}</td>
                                    <td class="px-4 py-3.5 text-slate-300">
                                        {{ $category->parent->name ?? '—' }}
                                    </td>
                                    <td class="px-4 py-3.5">
                                        @if($category->is_active)
                                            <span class="inline-flex items-center rounded-full bg-emerald-500/10 px-2 py-0.5 text-xs font-semibold text-emerald-400 ring-1 ring-inset ring-emerald-500/20">Active</span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-slate-500/10 px-2 py-0.5 text-xs font-semibold text-slate-400 ring-1 ring-inset ring-slate-500/20">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3.5">
                                        @if($category->is_featured)
                                            <span class="inline-flex items-center rounded-full bg-purple-500/10 px-2 py-0.5 text-[10px] font-bold text-purple-400 border border-purple-500/20">★ Featured</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3.5 text-right">
                                        <form method="POST" action="{{ route('admin.categories.destroy', $category->id) }}" onsubmit="return confirm('Are you sure you want to delete this category?')" class="m-0 inline-block">
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
                                    <td colspan="6" class="px-4 py-6 text-center text-slate-500 font-medium">
                                        No categories registered.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Create Category Card -->
            <div class="rounded-2xl bg-slate-900/40 border border-indigo-950/40 backdrop-blur-md p-6">
                <h4 class="text-base font-bold text-white mb-5">Create New Category</h4>
                
                <form method="POST" action="{{ route('admin.categories.store') }}" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Category Name *</label>
                            <input type="text" name="name" required class="w-full rounded-xl border border-indigo-950/50 bg-slate-950 px-4 py-2.5 text-sm text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500" placeholder="e.g. Headphones">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">URL Slug (Optional)</label>
                            <input type="text" name="slug" class="w-full rounded-xl border border-indigo-950/50 bg-slate-950 px-4 py-2.5 text-sm text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500" placeholder="e.g. high-end-audio">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Parent Category (Optional)</label>
                            <select name="parent_id" class="w-full rounded-xl border border-indigo-950/50 bg-slate-950 px-4 py-2.5 text-sm text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500">
                                <option value="">None (Top Level Category)</option>
                                @foreach($parentCategories as $parent)
                                    <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">SVG Icon Name</label>
                            <input type="text" name="icon" class="w-full rounded-xl border border-indigo-950/50 bg-slate-950 px-4 py-2.5 text-sm text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500" placeholder="e.g. music, watch, bolt">
                        </div>
                    </div>

                    <div class="flex items-center gap-2 pt-2">
                        <input type="checkbox" name="is_active" id="cat_is_active" checked value="1" class="h-4 w-4 rounded border-indigo-950 bg-slate-950 text-purple-600 focus:ring-purple-500">
                        <label for="cat_is_active" class="text-sm font-medium text-slate-300">Set active immediately</label>
                    </div>

                    <div class="flex items-center gap-2 pb-2">
                        <input type="checkbox" name="is_featured" id="cat_is_featured" value="1" class="h-4 w-4 rounded border-indigo-950 bg-slate-950 text-purple-600 focus:ring-purple-500">
                        <label for="cat_is_featured" class="text-sm font-medium text-purple-300">Feature this category on the Home Screen</label>
                    </div>

                    <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 py-3 text-sm font-bold text-white shadow-lg shadow-purple-500/20 hover:from-purple-500 hover:to-indigo-500 transition-all duration-200">
                        Save Category
                    </button>
                </form>
            </div>
        </div>

        <!-- ==================== RIGHT COLUMN: BRANDS ==================== -->
        <div class="space-y-8">
            <!-- Brands List Card -->
            <div class="rounded-2xl bg-slate-900/40 border border-indigo-950/40 backdrop-blur-md p-6">
                <h3 class="text-lg font-bold text-white flex items-center gap-2 mb-5 border-b border-indigo-950/40 pb-4">
                    <svg class="h-5 w-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                    </svg>
                    Brands Registry
                </h3>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-300">
                        <thead class="bg-indigo-950/30 text-xs font-bold uppercase tracking-wider text-slate-400">
                            <tr>
                                <th class="px-4 py-3 rounded-l-xl">Brand Name</th>
                                <th class="px-4 py-3">Slug</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3 rounded-r-xl text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-indigo-950/20">
                            @forelse($brands as $brand)
                                <tr class="hover:bg-slate-800/20 transition-colors">
                                    <td class="px-4 py-3.5 font-bold text-slate-200 flex items-center gap-2.5">
                                        <div class="h-6 w-6 rounded-md bg-white/5 border border-slate-700 flex items-center justify-center text-[10px] text-purple-400 font-bold uppercase">
                                            {{ substr($brand->name, 0, 2) }}
                                        </div>
                                        {{ $brand->name }}
                                    </td>
                                    <td class="px-4 py-3.5 text-xs font-medium text-slate-400">{{ $brand->slug }}</td>
                                    <td class="px-4 py-3.5">
                                        @if($brand->is_active)
                                            <span class="inline-flex items-center rounded-full bg-emerald-500/10 px-2 py-0.5 text-xs font-semibold text-emerald-400 ring-1 ring-inset ring-emerald-500/20">Active</span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-slate-500/10 px-2 py-0.5 text-xs font-semibold text-slate-400 ring-1 ring-inset ring-slate-500/20">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3.5 text-right">
                                        <form method="POST" action="{{ route('admin.brands.destroy', $brand->id) }}" onsubmit="return confirm('Are you sure you want to delete this brand?')" class="m-0 inline-block">
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
                                    <td colspan="4" class="px-4 py-6 text-center text-slate-500 font-medium">
                                        No brands registered.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Create Brand Card -->
            <div class="rounded-2xl bg-slate-900/40 border border-indigo-950/40 backdrop-blur-md p-6">
                <h4 class="text-base font-bold text-white mb-5">Create New Brand</h4>
                
                <form method="POST" action="{{ route('admin.brands.store') }}" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Brand Name *</label>
                            <input type="text" name="name" required class="w-full rounded-xl border border-indigo-950/50 bg-slate-950 px-4 py-2.5 text-sm text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500" placeholder="e.g. AuraTech">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">URL Slug (Optional)</label>
                            <input type="text" name="slug" class="w-full rounded-xl border border-indigo-950/50 bg-slate-950 px-4 py-2.5 text-sm text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500" placeholder="e.g. auratech">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Logo Image Filename</label>
                        <input type="text" name="logo" class="w-full rounded-xl border border-indigo-950/50 bg-slate-950 px-4 py-2.5 text-sm text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500" placeholder="e.g. auratech_logo.png">
                    </div>

                    <div class="flex items-center gap-2 pt-2">
                        <input type="checkbox" name="is_active" id="brand_is_active" checked value="1" class="h-4 w-4 rounded border-indigo-950 bg-slate-950 text-purple-600 focus:ring-purple-500">
                        <label for="brand_is_active" class="text-sm font-medium text-slate-300">Set as active brand immediately</label>
                    </div>

                    <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 py-3 text-sm font-bold text-white shadow-lg shadow-purple-500/20 hover:from-purple-500 hover:to-indigo-500 transition-all duration-200">
                        Save Brand
                    </button>
                </form>
            </div>
        </div>

    </div>
@endsection
