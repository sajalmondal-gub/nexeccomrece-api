@extends('layouts.admin')

@section('title', 'Create Combo Offer — NexCommerce')
@section('page_title', 'Create Promotional Combo Offer')

@section('content')
    <form method="POST" action="{{ route('admin.combo-offers.store') }}" enctype="multipart/form-data" class="space-y-8">
        @csrf
        
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
            
            <!-- Left 2 Columns: Combo Details & Product Assignment -->
            <div class="lg:col-span-2 space-y-8">
                
                <!-- Core Info -->
                <div class="rounded-2xl bg-slate-900/40 border border-indigo-950/40 backdrop-blur-md p-6">
                    <h3 class="text-base font-bold text-white mb-5 border-b border-indigo-950/40 pb-3 flex items-center gap-2">
                        <svg class="h-4.5 w-4.5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-5 3l4-4L19 7l-3-3-4 4m0 0l-3 3v3h3z"/></svg>
                        Combo Package Details
                    </h3>
                    
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Combo Bundle Name *</label>
                                <input type="text" name="name" required class="w-full rounded-xl border border-indigo-950/50 bg-slate-950 px-4 py-2.5 text-sm text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500" placeholder="e.g. Gamer Starter Pack">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Combo Bundle Name (Bangla)</label>
                                <input type="text" name="name_bn" class="w-full rounded-xl border border-indigo-950/50 bg-slate-950 px-4 py-2.5 text-sm text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500" placeholder="e.g. গেমার স্টার্টার প্যাক">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">URL Slug (Optional)</label>
                            <input type="text" name="slug" class="w-full rounded-xl border border-indigo-950/50 bg-slate-950 px-4 py-2.5 text-sm text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500" placeholder="e.g. gamer-starter-pack">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Package Description</label>
                            <textarea name="description" rows="4" class="w-full rounded-xl border border-indigo-950/50 bg-slate-950 px-4 py-2.5 text-sm text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500" placeholder="Describe what's included in this premium combo and what the savings are..."></textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Package Description (Bangla)</label>
                            <textarea name="description_bn" rows="4" class="w-full rounded-xl border border-indigo-950/50 bg-slate-950 px-4 py-2.5 text-sm text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500" placeholder="প্যাকেজের বিবরণ বাংলায় লিখুন..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- Product Assignment -->
                <div class="rounded-2xl bg-slate-900/40 border border-indigo-950/40 backdrop-blur-md p-6">
                    <h3 class="text-base font-bold text-white mb-5 border-b border-indigo-950/40 pb-3 flex items-center gap-2">
                        <svg class="h-4.5 w-4.5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                        Bundle Product Allocation (Choose at least 2)
                    </h3>
                    
                    <p class="text-xs text-slate-400 mb-4">
                        Search and select the products to combine. Assign the quantity for each selected item.
                    </p>

                    <!-- Interactive Product Selector -->
                    <div class="space-y-4 max-h-[400px] overflow-y-auto pr-2 divide-y divide-indigo-950/20">
                        @foreach($products as $idx => $product)
                            <div class="flex items-center justify-between py-3 first:pt-0 gap-4">
                                <div class="flex items-center gap-3">
                                    <input type="checkbox" name="products[{{ $idx }}][selected]" value="1" id="prod_check_{{ $product->id }}" onchange="toggleProductQty({{ $product->id }})" class="h-4.5 w-4.5 rounded border-indigo-950 bg-slate-950 text-purple-600 focus:ring-purple-500">
                                    <input type="hidden" name="products[{{ $idx }}][product_id]" value="{{ $product->id }}">
                                    
                                    <div class="h-8 w-8 rounded bg-slate-950 border border-indigo-950/40 overflow-hidden flex items-center justify-center">
                                        <img class="w-full h-full object-cover" src="{{ str_starts_with($product->image, 'http') ? $product->image : asset('uploads/products/' . $product->image) }}" alt="Thumbnail">
                                    </div>
                                    
                                    <div>
                                        <label for="prod_check_{{ $product->id }}" class="text-sm font-semibold text-slate-200 cursor-pointer block hover:text-white">{{ $product->name }}</label>
                                        <span class="text-[10px] text-slate-500 block">Unit: ${{ number_format($product->final_price, 2) }} | Stock: {{ $product->stock_qty }}</span>
                                    </div>
                                </div>

                                <div class="w-24" id="qty_box_{{ $product->id }}" style="display: none;">
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Bundle Qty</label>
                                    <input type="number" name="products[{{ $idx }}][quantity]" value="1" min="1" class="w-full rounded-lg border border-indigo-950/60 bg-slate-950 px-2.5 py-1 text-xs text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500">
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>

            <!-- Right Column: Settings & Pricing -->
            <div class="space-y-8">
                
                <!-- Pricing & Visibility -->
                <div class="rounded-2xl bg-slate-900/40 border border-indigo-950/40 backdrop-blur-md p-6 space-y-4">
                    <h3 class="text-base font-bold text-white border-b border-indigo-950/40 pb-3 mb-4">Pricing & Promotion</h3>
                    
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Combo Promo Price ($) *</label>
                        <input type="number" step="0.01" name="price" required class="w-full rounded-xl border border-indigo-950/50 bg-slate-950 px-4 py-2.5 text-sm text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500" placeholder="e.g. 149.99">
                        <span class="text-[10px] text-slate-500 mt-1 block">The aggregate promotional price for the entire pack.</span>
                    </div>

                    <div class="flex items-center gap-3 pt-2">
                        <input type="checkbox" name="is_active" id="combo_is_active" checked value="1" class="h-4.5 w-4.5 rounded border-indigo-950 bg-slate-950 text-purple-600 focus:ring-purple-500">
                        <label for="combo_is_active" class="text-sm font-semibold text-slate-300">Set active immediately</label>
                    </div>
                </div>

                <!-- Package Image -->
                <div class="rounded-2xl bg-slate-900/40 border border-indigo-950/40 backdrop-blur-md p-6">
                    <h3 class="text-base font-bold text-white mb-5 border-b border-indigo-950/40 pb-3 flex items-center gap-2">
                        <svg class="h-4.5 w-4.5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Bundle Thumbnail Image
                    </h3>
                    
                    <div class="space-y-4">
                        <div class="border border-dashed border-indigo-950/60 rounded-2xl bg-slate-950/40 p-6 text-center cursor-pointer relative group">
                            <input type="file" name="image_file" id="image-selector" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer z-10">
                            <div class="space-y-2 pointer-events-none">
                                <div class="h-10 w-10 mx-auto rounded-xl bg-purple-500/10 flex items-center justify-center text-purple-400 border border-purple-500/20 group-hover:scale-110 transition-transform">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                </div>
                                <p class="text-xs font-bold text-slate-300" id="filename-preview">Drag & drop or tap to browse</p>
                                <p class="text-[10px] text-slate-500 font-medium">JPEG, PNG, WEBP, GIF up to 5MB</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Action -->
                <div class="pt-2">
                    <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-purple-600 via-purple-500 to-indigo-500 py-4 text-base font-bold text-white shadow-xl shadow-purple-500/20 hover:from-purple-500 hover:to-indigo-500 transition-all duration-200">
                        Create Combo Offer
                    </button>
                    <a href="{{ route('admin.combo-offers.index') }}" class="block text-center mt-3 text-sm font-semibold text-slate-400 hover:text-slate-300 transition-colors">
                        Cancel & Return
                    </a>
                </div>

            </div>
        </div>
    </form>

    <script>
        function toggleProductQty(productId) {
            const checkBox = document.getElementById(`prod_check_${productId}`);
            const qtyBox = document.getElementById(`qty_box_${productId}`);
            if (checkBox.checked) {
                qtyBox.style.display = 'block';
            } else {
                qtyBox.style.display = 'none';
            }
        }

        const selector = document.getElementById('image-selector');
        if (selector) {
            selector.addEventListener('change', function(e) {
                if (e.target.files.length > 0) {
                    document.getElementById('filename-preview').innerText = e.target.files[0].name;
                }
            });
        }
    </script>
@endsection
