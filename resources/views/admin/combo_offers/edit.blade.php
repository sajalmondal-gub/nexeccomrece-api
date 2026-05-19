@extends('layouts.admin')

@section('title', 'Edit Combo Offer — NexCommerce')
@section('page_title', 'Edit Promotional Combo Offer')

@section('content')
    <form method="POST" action="{{ route('admin.combo-offers.update', $comboOffer->id) }}" enctype="multipart/form-data" class="space-y-8">
        @csrf
        @method('PUT')
        
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
                                <input type="text" name="name" value="{{ $comboOffer->name }}" required class="w-full rounded-xl border border-indigo-950/50 bg-slate-950 px-4 py-2.5 text-sm text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500" placeholder="e.g. Gamer Starter Pack">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Combo Bundle Name (Bangla)</label>
                                <input type="text" name="name_bn" value="{{ $comboOffer->name_bn }}" class="w-full rounded-xl border border-indigo-950/50 bg-slate-950 px-4 py-2.5 text-sm text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500" placeholder="e.g. গেমার স্টার্টার প্যাক">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">URL Slug *</label>
                            <input type="text" name="slug" value="{{ $comboOffer->slug }}" required class="w-full rounded-xl border border-indigo-950/50 bg-slate-950 px-4 py-2.5 text-sm text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500" placeholder="e.g. gamer-starter-pack">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Package Description</label>
                            <textarea name="description" rows="4" class="w-full rounded-xl border border-indigo-950/50 bg-slate-950 px-4 py-2.5 text-sm text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500" placeholder="Describe what's included...">{{ $comboOffer->description }}</textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Package Description (Bangla)</label>
                            <textarea name="description_bn" rows="4" class="w-full rounded-xl border border-indigo-950/50 bg-slate-950 px-4 py-2.5 text-sm text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500" placeholder="প্যাকেজের বিবরণ বাংলায় লিখুন...">{{ $comboOffer->description_bn }}</textarea>
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
                        Manage products inside this bundle. Checking/unchecking products attaches/detaches them dynamically.
                    </p>

                    <!-- Interactive Product Selector -->
                    <div class="space-y-4 max-h-[400px] overflow-y-auto pr-2 divide-y divide-indigo-950/20">
                        @php
                            $comboProductIds = $comboOffer->products->pluck('id')->toArray();
                            $comboProductPivots = $comboOffer->products->keyBy('id');
                        @endphp
                        @foreach($products as $idx => $product)
                            @php
                                $isChecked = in_array($product->id, $comboProductIds);
                                $qty = $isChecked ? $comboProductPivots[$product->id]->pivot->quantity : 1;
                            @endphp
                            <div class="flex items-center justify-between py-3 first:pt-0 gap-4">
                                <div class="flex items-center gap-3">
                                    <input type="checkbox" name="products[{{ $idx }}][selected]" value="1" id="prod_check_{{ $product->id }}" @checked($isChecked) onchange="toggleProductQty({{ $product->id }})" class="h-4.5 w-4.5 rounded border-indigo-950 bg-slate-950 text-purple-600 focus:ring-purple-500">
                                    <input type="hidden" name="products[{{ $idx }}][product_id]" value="{{ $product->id }}">
                                    
                                    <div class="h-8 w-8 rounded bg-slate-950 border border-indigo-950/40 overflow-hidden flex items-center justify-center">
                                        <img class="w-full h-full object-cover" src="{{ str_starts_with($product->image, 'http') ? $product->image : asset('uploads/products/' . $product->image) }}" alt="Thumbnail">
                                    </div>
                                    
                                    <div>
                                        <label for="prod_check_{{ $product->id }}" class="text-sm font-semibold text-slate-200 cursor-pointer block hover:text-white">{{ $product->name }}</label>
                                        <span class="text-[10px] text-slate-500 block">Unit: ${{ number_format($product->final_price, 2) }} | Stock: {{ $product->stock_qty }}</span>
                                    </div>
                                </div>

                                <div class="w-24" id="qty_box_{{ $product->id }}" style="display: {{ $isChecked ? 'block' : 'none' }};">
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Bundle Qty</label>
                                    <input type="number" name="products[{{ $idx }}][quantity]" value="{{ $qty }}" min="1" class="w-full rounded-lg border border-indigo-950/60 bg-slate-950 px-2.5 py-1 text-xs text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500">
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
                        <input type="number" step="0.01" name="price" value="{{ $comboOffer->price }}" required class="w-full rounded-xl border border-indigo-950/50 bg-slate-950 px-4 py-2.5 text-sm text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500" placeholder="e.g. 149.99">
                        <span class="text-[10px] text-slate-500 mt-1 block">Original Combined Value: ${{ number_format($comboOffer->original_price, 2) }}</span>
                    </div>

                    <div class="flex items-center gap-3 pt-2">
                        <input type="checkbox" name="is_active" id="combo_is_active" value="1" @checked($comboOffer->is_active) class="h-4.5 w-4.5 rounded border-indigo-950 bg-slate-950 text-purple-600 focus:ring-purple-500">
                        <label for="combo_is_active" class="text-sm font-semibold text-slate-300">Set active immediately</label>
                    </div>
                </div>

                <!-- Package Image -->
                <div class="rounded-2xl bg-slate-900/40 border border-indigo-950/40 backdrop-blur-md p-6 space-y-4">
                    <h3 class="text-base font-bold text-white mb-3 border-b border-indigo-950/40 pb-3 flex items-center gap-2">
                        <svg class="h-4.5 w-4.5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Bundle Thumbnail Image
                    </h3>

                    <!-- Current Image Preview -->
                    <div class="h-40 rounded-xl overflow-hidden bg-slate-950 border border-indigo-950/40 relative">
                        <img class="w-full h-full object-cover" src="{{ asset($comboOffer->image) }}" alt="Current Combo Image">
                        <span class="absolute bottom-2 left-2 rounded bg-black/60 px-2 py-0.5 text-[10px] font-semibold text-slate-300 backdrop-blur-sm">Current image</span>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="border border-dashed border-indigo-950/60 rounded-2xl bg-slate-950/40 p-5 text-center cursor-pointer relative group">
                            <input type="file" name="image_file" id="image-selector" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer z-10">
                            <div class="space-y-2 pointer-events-none">
                                <p class="text-xs font-bold text-slate-300" id="filename-preview">Tap to replace image</p>
                                <p class="text-[10px] text-slate-500 font-medium">JPEG, PNG, WEBP, GIF up to 5MB</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Action -->
                <div class="pt-2">
                    <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-purple-600 via-purple-500 to-indigo-500 py-4 text-base font-bold text-white shadow-xl shadow-purple-500/20 hover:from-purple-500 hover:to-indigo-500 transition-all duration-200">
                        Update Combo Offer
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
