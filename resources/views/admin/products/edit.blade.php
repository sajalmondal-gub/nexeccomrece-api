@extends('layouts.admin')

@section('title', 'Edit Product — NexCommerce')
@section('page_title', 'Edit Catalog Product')

@section('content')
    <form method="POST" action="{{ route('admin.products.update', $product->id) }}" enctype="multipart/form-data" class="space-y-8">
        @csrf
        
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
            
            <!-- Left 2 Columns: Product Info Form -->
            <div class="lg:col-span-2 space-y-8">
                
                <!-- Core Product Information -->
                <div class="rounded-2xl bg-slate-900/40 border border-indigo-950/40 backdrop-blur-md p-6">
                    <h3 class="text-base font-bold text-white mb-5 border-b border-indigo-950/40 pb-3 flex items-center gap-2">
                        <svg class="h-4.5 w-4.5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-5 3l4-4L19 7l-3-3-4 4m0 0l-3 3v3h3z"/></svg>
                        Product Fundamentals
                    </h3>
                    
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Product Title *</label>
                                <input type="text" name="name" required value="{{ old('name', $product->name) }}" class="w-full rounded-xl border border-indigo-950/50 bg-slate-950 px-4 py-2.5 text-sm text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">URL Slug *</label>
                                <input type="text" name="slug" required value="{{ old('slug', $product->slug) }}" class="w-full rounded-xl border border-indigo-950/50 bg-slate-950 px-4 py-2.5 text-sm text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Short Summary *</label>
                            <input type="text" name="short_description" required value="{{ old('short_description', $product->short_description) }}" class="w-full rounded-xl border border-indigo-950/50 bg-slate-950 px-4 py-2.5 text-sm text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Detailed Description *</label>
                            <textarea name="description" rows="6" required class="w-full rounded-xl border border-indigo-950/50 bg-slate-950 px-4 py-2.5 text-sm text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500">{{ old('description', $product->description) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Product Variants & Attribute Options (Dynamic JS Panel) -->
                <div class="rounded-2xl bg-slate-900/40 border border-indigo-950/40 backdrop-blur-md p-6">
                    <div class="flex items-center justify-between mb-5 border-b border-indigo-950/40 pb-3">
                        <h3 class="text-base font-bold text-white flex items-center gap-2">
                            <svg class="h-4.5 w-4.5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                            Product Variants & Stock Adjustments
                        </h3>
                        
                        <button type="button" onclick="addVariantRow()" class="inline-flex items-center gap-1 rounded-lg bg-purple-500/10 px-3 py-1.5 text-xs font-bold text-purple-400 ring-1 ring-inset ring-purple-500/20 hover:bg-purple-500/20 transition-all">
                            + Add Variant Option
                        </button>
                    </div>

                    <p class="text-xs text-slate-400 mb-4 leading-relaxed">
                        Specify variant values (e.g. Size, Color, Capacity). The **Price Modifier** is added to the base price of the product (e.g. adding +$10.00 for size XL).
                    </p>

                    <!-- Variant Container -->
                    <div id="variants-container" class="space-y-3">
                        <!-- Existing Variants will be loaded here -->
                        @foreach($product->variants as $index => $variant)
                            <div id="variant-row-{{ $index }}" class="grid grid-cols-1 sm:grid-cols-5 gap-3 p-3.5 bg-slate-950/60 rounded-xl border border-indigo-950/40 relative group items-center">
                                <div>
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Attr * (e.g. Size)</label>
                                    <input type="text" name="variants[{{ $index }}][attribute_name]" required value="{{ $variant->attribute_name }}" class="w-full rounded-lg border border-indigo-950/60 bg-slate-950 px-3 py-1.5 text-xs text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Val * (e.g. XL)</label>
                                    <input type="text" name="variants[{{ $index }}][attribute_value]" required value="{{ $variant->attribute_value }}" class="w-full rounded-lg border border-indigo-950/60 bg-slate-950 px-3 py-1.5 text-xs text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Modifier ($)</label>
                                    <input type="number" step="0.01" name="variants[{{ $index }}][price_modifier]" value="{{ $variant->price_modifier }}" class="w-full rounded-lg border border-indigo-950/60 bg-slate-950 px-3 py-1.5 text-xs text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Stock *</label>
                                    <input type="number" name="variants[{{ $index }}][stock_qty]" required value="{{ $variant->stock_qty }}" class="w-full rounded-lg border border-indigo-950/60 bg-slate-950 px-3 py-1.5 text-xs text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500">
                                </div>
                                <div class="flex items-center justify-between pt-4 sm:pt-0">
                                    <div class="flex-1 mr-2">
                                        <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Custom SKU</label>
                                        <input type="text" name="variants[{{ $index }}][sku]" value="{{ $variant->sku }}" class="w-full rounded-lg border border-indigo-950/60 bg-slate-950 px-3 py-1.5 text-xs text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500">
                                    </div>
                                    <button type="button" onclick="removeVariantRow({{ $index }})" class="mt-4 p-1.5 rounded-lg bg-red-500/10 text-red-400 border border-red-500/10 hover:bg-red-500/20 hover:text-red-300 transition-colors" title="Delete">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div id="no-variants-message" class="text-center py-6 text-slate-500 border border-dashed border-indigo-950/40 rounded-xl" style="{{ $product->variants->count() > 0 ? 'display: none;' : '' }}">
                        No product variants configured yet. Tap the button above to add variant attributes (like size or color options).
                    </div>
                </div>

            </div>

            <!-- Right 1 Column: Inventory, Pricing, and Organization -->
            <div class="space-y-8">
                
                <!-- Organization -->
                <div class="rounded-2xl bg-slate-900/40 border border-indigo-950/40 backdrop-blur-md p-6">
                    <h3 class="text-base font-bold text-white mb-5 border-b border-indigo-950/40 pb-3">Organization & Placement</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Category *</label>
                            <select name="category_id" required class="w-full rounded-xl border border-indigo-950/50 bg-slate-950 px-4 py-2.5 text-sm text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500">
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ $product->category_id === $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Brand *</label>
                            <select name="brand_id" required class="w-full rounded-xl border border-indigo-950/50 bg-slate-950 px-4 py-2.5 text-sm text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500">
                                @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}" {{ $product->brand_id === $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Pricing & Stock -->
                <div class="rounded-2xl bg-slate-900/40 border border-indigo-950/40 backdrop-blur-md p-6">
                    <h3 class="text-base font-bold text-white mb-5 border-b border-indigo-950/40 pb-3">Pricing & Inventory</h3>
                    
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Base Price ($) *</label>
                                <input type="number" step="0.01" name="base_price" required value="{{ old('base_price', $product->base_price) }}" class="w-full rounded-xl border border-indigo-950/50 bg-slate-950 px-4 py-2.5 text-sm text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Sale Price ($)</label>
                                <input type="number" step="0.01" name="sale_price" value="{{ old('sale_price', $product->sale_price) }}" class="w-full rounded-xl border border-indigo-950/50 bg-slate-950 px-4 py-2.5 text-sm text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Stock Count *</label>
                                <input type="number" name="stock_qty" required value="{{ old('stock_qty', $product->stock_qty) }}" class="w-full rounded-xl border border-indigo-950/50 bg-slate-950 px-4 py-2.5 text-sm text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Catalog SKU</label>
                                <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" class="w-full rounded-xl border border-indigo-950/50 bg-slate-950 px-4 py-2.5 text-sm text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Assets & Media Multiple File Upload Zone -->
                <div class="rounded-2xl bg-slate-900/40 border border-indigo-950/40 backdrop-blur-md p-6">
                    <h3 class="text-base font-bold text-white mb-5 border-b border-indigo-950/40 pb-3 flex items-center gap-2">
                        <svg class="h-4.5 w-4.5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Product Media Grid
                    </h3>
                    
                    <div class="space-y-4">
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Add New Images</label>
                        
                        <!-- Drag and Drop Dropzone container -->
                        <div id="media-dropzone" class="border border-dashed border-indigo-950/60 rounded-2xl bg-slate-950/40 hover:bg-slate-950/80 hover:border-purple-500/50 transition-all p-6 text-center cursor-pointer relative group">
                            <input type="file" name="uploaded_images[]" id="file-selector" multiple accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer z-10">
                            <div class="space-y-2 pointer-events-none">
                                <div class="h-10 w-10 mx-auto rounded-xl bg-purple-500/10 flex items-center justify-center text-purple-400 border border-purple-500/20 group-hover:scale-110 transition-transform">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                </div>
                                <p class="text-xs font-bold text-slate-300">Drag & drop files here, or tap to browse</p>
                                <p class="text-[10px] text-slate-500 font-medium">Supports JPEG, PNG, WEBP, GIF up to 5MB per file</p>
                            </div>
                        </div>

                        <!-- Current Product Gallery / Existing Database Images -->
                        @php
                            $images = !empty($product->images) ? (is_string($product->images) ? json_decode($product->images, true) : $product->images) : [];
                            if (empty($images) && !empty($product->image)) {
                                $images = [$product->image];
                            }
                        @endphp
                        
                        @if(!empty($images))
                            <div class="mt-4">
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Existing Product Gallery</label>
                                <div class="grid grid-cols-2 gap-3.5" id="existing-images-grid">
                                    @foreach($images as $idx => $img)
                                        <div class="relative rounded-xl border {{ $product->image === $img ? 'border-purple-500 ring-2 ring-purple-500/30' : 'border-indigo-950/40' }} overflow-hidden bg-slate-950 aspect-square existing-tile" data-path="{{ $img }}">
                                            <div class="w-full h-full bg-slate-900 flex items-center justify-center overflow-hidden">
                                                <img class="w-full h-full object-cover" src="{{ str_starts_with($img, 'http') ? $img : asset('uploads/products/' . $img) }}" alt="Thumbnail">
                                            </div>
                                            
                                            <!-- Overlay Controls -->
                                            <div class="absolute inset-0 bg-slate-950/70 opacity-0 hover:opacity-100 transition-opacity flex flex-col justify-between p-2">
                                                <!-- Delete button -->
                                                <button type="button" onclick="removeExistingImage(this, '{{ $img }}')" class="self-end p-1.5 rounded-lg bg-red-500/20 text-red-400 border border-red-500/20 hover:bg-red-500 hover:text-white transition-colors focus:outline-none">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                                
                                                <!-- Make Primary button -->
                                                <button type="button" onclick="setExistingPrimary('{{ $img }}')" class="w-full py-1.5 rounded-lg bg-purple-600/90 text-[10px] font-bold text-white shadow hover:bg-purple-500 transition-colors uppercase tracking-wider">
                                                    {{ $product->image === $img ? '★ Designated Main' : '☆ Make Primary' }}
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Previews Grid for newly dropped files -->
                        <div id="preview-grid" class="grid grid-cols-2 gap-3.5 mt-4"></div>

                        <!-- Hidden fields to communicate deletions and selections to the backend -->
                        <input type="hidden" name="primary_image_index" id="primary_image_index" value="-1">
                        <input type="hidden" name="primary_existing_image" id="primary_existing_image" value="{{ $product->image }}">
                        <input type="hidden" name="removed_existing_images" id="removed_existing_images" value="[]">
                    </div>
                </div>

                <!-- Visibility & Deals -->
                <div class="rounded-2xl bg-slate-900/40 border border-indigo-950/40 backdrop-blur-md p-6 space-y-4">
                    <h3 class="text-base font-bold text-white border-b border-indigo-950/40 pb-3 mb-4">Visibility & Campaigns</h3>
                    
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Deal Campaign Allocation</label>
                        <select name="deal_type" class="w-full rounded-xl border border-indigo-950/50 bg-slate-950 px-4 py-2.5 text-sm text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500 mb-3">
                            <option value="" {{ empty($product->deal_type) ? 'selected' : '' }}>No Active Deal</option>
                            <option value="flash" {{ $product->deal_type === 'flash' ? 'selected' : '' }}>Flash Deal</option>
                            <option value="weekly" {{ $product->deal_type === 'weekly' ? 'selected' : '' }}>Weekly Deal</option>
                            <option value="monthly" {{ $product->deal_type === 'monthly' ? 'selected' : '' }}>Monthly Deal</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-3">
                        <input type="checkbox" name="is_active" id="prod_is_active" value="1" {{ $product->is_active ? 'checked' : '' }} class="h-4.5 w-4.5 rounded border-indigo-950 bg-slate-950 text-purple-600 focus:ring-purple-500">
                        <label for="prod_is_active" class="text-sm font-semibold text-slate-300">Set active immediately</label>
                    </div>
                    
                    <div class="flex items-center gap-3">
                        <input type="checkbox" name="is_featured" id="prod_is_featured" value="1" {{ $product->is_featured ? 'checked' : '' }} class="h-4.5 w-4.5 rounded border-indigo-950 bg-slate-950 text-purple-600 focus:ring-purple-500">
                        <label for="prod_is_featured" class="text-sm font-semibold text-slate-300">Promote to home page carousel</label>
                    </div>
                </div>

                <!-- Save Action panel -->
                <div class="pt-2">
                    <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-purple-600 via-purple-500 to-indigo-500 py-4 text-base font-bold text-white shadow-xl shadow-purple-500/20 hover:from-purple-500 hover:to-indigo-500 transition-all duration-200">
                        Commit Updates
                    </button>
                    <a href="{{ route('admin.products.index') }}" class="block text-center mt-3 text-sm font-semibold text-slate-400 hover:text-slate-300 transition-colors">
                        Cancel & Return
                    </a>
                </div>

            </div>
            
        </div>
    </form>

    <script>
        // Set variantIndex based on count of existing variants so we append uniquely
        let variantIndex = {{ $product->variants->count() }};

        function addVariantRow() {
            // Hide no-variants placeholder message
            document.getElementById('no-variants-message').style.display = 'none';

            const container = document.getElementById('variants-container');
            const row = document.createElement('div');
            row.id = `variant-row-${variantIndex}`;
            row.className = "grid grid-cols-1 sm:grid-cols-5 gap-3 p-3.5 bg-slate-950/60 rounded-xl border border-indigo-950/40 relative group items-center";
            
            row.innerHTML = `
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Attr * (e.g. Size)</label>
                    <input type="text" name="variants[${variantIndex}][attribute_name]" required class="w-full rounded-lg border border-indigo-950/60 bg-slate-950 px-3 py-1.5 text-xs text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500" placeholder="Color">
                </div>
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Val * (e.g. XL)</label>
                    <input type="text" name="variants[${variantIndex}][attribute_value]" required class="w-full rounded-lg border border-indigo-950/60 bg-slate-950 px-3 py-1.5 text-xs text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500" placeholder="Royal Purple">
                </div>
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Modifier ($)</label>
                    <input type="number" step="0.01" name="variants[${variantIndex}][price_modifier]" class="w-full rounded-lg border border-indigo-950/60 bg-slate-950 px-3 py-1.5 text-xs text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500" placeholder="0.00">
                </div>
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Stock *</label>
                    <input type="number" name="variants[${variantIndex}][stock_qty]" required class="w-full rounded-lg border border-indigo-950/60 bg-slate-950 px-3 py-1.5 text-xs text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500" placeholder="10">
                </div>
                <div class="flex items-center justify-between pt-4 sm:pt-0">
                    <div class="flex-1 mr-2">
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Custom SKU</label>
                        <input type="text" name="variants[${variantIndex}][sku]" class="w-full rounded-lg border border-indigo-950/60 bg-slate-950 px-3 py-1.5 text-xs text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500" placeholder="Optional">
                    </div>
                    <button type="button" onclick="removeVariantRow(${variantIndex})" class="mt-4 p-1.5 rounded-lg bg-red-500/10 text-red-400 border border-red-500/10 hover:bg-red-500/20 hover:text-red-300 transition-colors" title="Delete">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>
            `;
            
            container.appendChild(row);
            variantIndex++;
        }

        function removeVariantRow(index) {
            const row = document.getElementById(`variant-row-${index}`);
            if (row) {
                row.remove();
            }

            const container = document.getElementById('variants-container');
            if (container.children.length === 0) {
                document.getElementById('no-variants-message').style.display = 'block';
            }
        }

        // Media edit/upload controller
        let uploadedFiles = [];
        let removedExistingImages = [];
        
        const fileSelector = document.getElementById('file-selector');
        const previewGrid = document.getElementById('preview-grid');
        const primaryImageIndexInput = document.getElementById('primary_image_index');
        const primaryExistingImageInput = document.getElementById('primary_existing_image');
        const removedExistingImagesInput = document.getElementById('removed_existing_images');

        if (fileSelector) {
            fileSelector.addEventListener('change', handleFileSelection);
        }

        function handleFileSelection(e) {
            const files = Array.from(e.target.files);
            files.forEach(file => {
                if (file.type.startsWith('image/')) {
                    uploadedFiles.push(file);
                }
            });
            renderPreviews();
            syncFilesToInput();
        }

        function renderPreviews() {
            previewGrid.innerHTML = '';
            uploadedFiles.forEach((file, index) => {
                const reader = new FileReader();
                const tile = document.createElement('div');
                const isPrimary = parseInt(primaryImageIndexInput.value) === index;
                
                tile.id = `new-tile-${index}`;
                tile.className = "relative rounded-xl border overflow-hidden transition-all bg-slate-950 aspect-square " + 
                    (isPrimary ? "border-purple-500 ring-2 ring-purple-500/30" : "border-indigo-950/40");
                
                tile.innerHTML = `
                    <div class="w-full h-full bg-slate-900 flex items-center justify-center overflow-hidden">
                        <img id="preview-img-${index}" class="w-full h-full object-cover" src="" alt="Thumbnail">
                    </div>
                    <div class="absolute inset-0 bg-slate-950/70 opacity-0 hover:opacity-100 transition-opacity flex flex-col justify-between p-2">
                        <button type="button" onclick="removeUploadedFile(${index})" class="self-end p-1.5 rounded-lg bg-red-500/20 text-red-400 border border-red-500/20 hover:bg-red-500 hover:text-white transition-colors focus:outline-none">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                        <button type="button" onclick="setNewPrimary(${index})" class="w-full py-1.5 rounded-lg bg-purple-600/90 text-[10px] font-bold text-white shadow hover:bg-purple-500 transition-colors uppercase tracking-wider">
                            ${isPrimary ? '★ Designated Main' : '☆ Make Primary'}
                        </button>
                    </div>
                `;
                
                previewGrid.appendChild(tile);
                reader.onload = function(e) {
                    const img = document.getElementById(`preview-img-${index}`);
                    if (img) img.src = e.target.result;
                };
                reader.readAsDataURL(file);
            });
        }

        function removeUploadedFile(index) {
            uploadedFiles.splice(index, 1);
            if (parseInt(primaryImageIndexInput.value) === index) {
                primaryImageIndexInput.value = "-1";
                // fallback to first existing image if possible
                const firstExisting = document.querySelector('.existing-tile');
                if (firstExisting) {
                    const path = firstExisting.getAttribute('data-path');
                    setExistingPrimary(path);
                }
            } else if (parseInt(primaryImageIndexInput.value) > index) {
                primaryImageIndexInput.value = parseInt(primaryImageIndexInput.value) - 1;
            }
            renderPreviews();
            syncFilesToInput();
        }

        function setNewPrimary(index) {
            primaryImageIndexInput.value = index;
            primaryExistingImageInput.value = "";
            
            // Remove border highlights from existing tiles
            document.querySelectorAll('.existing-tile').forEach(tile => {
                tile.className = "relative rounded-xl border border-indigo-950/40 overflow-hidden bg-slate-950 aspect-square existing-tile";
                const btn = tile.querySelector('button[onclick^="setExistingPrimary"]');
                if (btn) btn.innerText = "☆ Make Primary";
            });
            
            renderPreviews();
        }

        function setExistingPrimary(path) {
            primaryImageIndexInput.value = "-1";
            primaryExistingImageInput.value = path;
            
            document.querySelectorAll('.existing-tile').forEach(tile => {
                const isSelected = tile.getAttribute('data-path') === path;
                tile.className = "relative rounded-xl border overflow-hidden bg-slate-950 aspect-square existing-tile " +
                    (isSelected ? "border-purple-500 ring-2 ring-purple-500/30" : "border-indigo-950/40");
                
                const btn = tile.querySelector('button[onclick^="setExistingPrimary"]');
                if (btn) btn.innerText = isSelected ? "★ Designated Main" : "☆ Make Primary";
            });
            
            renderPreviews();
        }

        function removeExistingImage(btn, path) {
            removedExistingImages.push(path);
            removedExistingImagesInput.value = JSON.stringify(removedExistingImages);
            
            // Remove tile from DOM
            const tile = btn.closest('.existing-tile');
            if (tile) {
                tile.remove();
            }

            // If the primary image was this one, reset primary
            if (primaryExistingImageInput.value === path) {
                primaryExistingImageInput.value = "";
                // select another existing
                const nextExisting = document.querySelector('.existing-tile');
                if (nextExisting) {
                    setExistingPrimary(nextExisting.getAttribute('data-path'));
                } else if (uploadedFiles.length > 0) {
                    setNewPrimary(0);
                }
            }
        }

        function syncFilesToInput() {
            const dt = new DataTransfer();
            uploadedFiles.forEach(file => dt.items.add(file));
            fileSelector.files = dt.files;
        }
    </script>
@endsection
