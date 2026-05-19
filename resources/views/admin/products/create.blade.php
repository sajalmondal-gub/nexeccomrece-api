@extends('layouts.admin')

@section('title', 'Add New Product — NexCommerce')
@section('page_title', 'Add Catalog Product')

@section('content')
    <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="space-y-8">
        @csrf
        
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
            
            <!-- Left 2 Columns: Product Info Form -->
            <div class="lg:col-span-2 space-y-8">
                
                <!-- Core Product Information -->
                <div class="rounded-2xl bg-slate-900/40 border border-indigo-950/40 backdrop-blur-md p-6">
                    <h3 class="text-base font-bold text-white mb-5 border-b border-indigo-950/40 pb-3 flex items-center gap-2">
                        <svg class="h-4.5 w-4.5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Product Fundamentals
                    </h3>
                    
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Product Title *</label>
                                <input type="text" name="name" required class="w-full rounded-xl border border-indigo-950/50 bg-slate-950 px-4 py-2.5 text-sm text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500" placeholder="e.g. Nebula SoundPro ANC">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Product Title (Bangla)</label>
                                <input type="text" name="name_bn" class="w-full rounded-xl border border-indigo-950/50 bg-slate-950 px-4 py-2.5 text-sm text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500" placeholder="e.g. নেবুলা সাউন্ডপ্রো এএনসি">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">URL Slug (Optional)</label>
                            <input type="text" name="slug" class="w-full rounded-xl border border-indigo-950/50 bg-slate-950 px-4 py-2.5 text-sm text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500" placeholder="e.g. nebula-soundpro-headphones">
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Short Summary *</label>
                                <input type="text" name="short_description" required class="w-full rounded-xl border border-indigo-950/50 bg-slate-950 px-4 py-2.5 text-sm text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500" placeholder="e.g. High-end ANC headphones with premium purple neon glow.">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Short Summary (Bangla)</label>
                                <input type="text" name="short_description_bn" class="w-full rounded-xl border border-indigo-950/50 bg-slate-950 px-4 py-2.5 text-sm text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500" placeholder="e.g. প্রিমিয়াম কোয়ালিটির এএনসি হেডফোন।">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Detailed Description *</label>
                            <textarea name="description" rows="6" required class="w-full rounded-xl border border-indigo-950/50 bg-slate-950 px-4 py-2.5 text-sm text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500" placeholder="Provide full features, product specifications, materials, and luxury styling aspects..."></textarea>
                        </div>
                        
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Detailed Description (Bangla)</label>
                            <textarea name="description_bn" rows="6" class="w-full rounded-xl border border-indigo-950/50 bg-slate-950 px-4 py-2.5 text-sm text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500" placeholder="Provide full features in Bangla..."></textarea>
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
                        <!-- Dynamic Rows injected here -->
                    </div>

                    <div id="no-variants-message" class="text-center py-6 text-slate-500 border border-dashed border-indigo-950/40 rounded-xl">
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
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Brand *</label>
                            <select name="brand_id" required class="w-full rounded-xl border border-indigo-950/50 bg-slate-950 px-4 py-2.5 text-sm text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500">
                                <option value="">Select Brand</option>
                                @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
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
                                <input type="number" step="0.01" name="base_price" required class="w-full rounded-xl border border-indigo-950/50 bg-slate-950 px-4 py-2.5 text-sm text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500" placeholder="e.g. 199.99">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Sale Price ($)</label>
                                <input type="number" step="0.01" name="sale_price" class="w-full rounded-xl border border-indigo-950/50 bg-slate-950 px-4 py-2.5 text-sm text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500" placeholder="e.g. 149.99">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Stock Count *</label>
                                <input type="number" name="stock_qty" required class="w-full rounded-xl border border-indigo-950/50 bg-slate-950 px-4 py-2.5 text-sm text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500" placeholder="e.g. 50">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Catalog SKU</label>
                                <input type="text" name="sku" class="w-full rounded-xl border border-indigo-950/50 bg-slate-950 px-4 py-2.5 text-sm text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500" placeholder="e.g. NBL-SPRO-HDP">
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
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Multiple Image Uploads</label>
                        
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

                        <!-- Previews Grid -->
                        <div id="preview-grid" class="grid grid-cols-2 gap-3.5 mt-4">
                            <!-- JS will inject preview thumbnails here -->
                        </div>

                        <!-- Hidden primary image indicator -->
                        <input type="hidden" name="primary_image_index" id="primary_image_index" value="0">
                    </div>
                </div>

                <!-- Visibility & Deals -->
                <div class="rounded-2xl bg-slate-900/40 border border-indigo-950/40 backdrop-blur-md p-6 space-y-4">
                    <h3 class="text-base font-bold text-white border-b border-indigo-950/40 pb-3 mb-4">Visibility & Campaigns</h3>
                    
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Deal Campaign Allocation</label>
                        <select name="deal_type" class="w-full rounded-xl border border-indigo-950/50 bg-slate-950 px-4 py-2.5 text-sm text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500 mb-3">
                            <option value="">No Active Deal</option>
                            <option value="flash">Flash Deal</option>
                            <option value="weekly">Weekly Deal</option>
                            <option value="monthly">Monthly Deal</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-3">
                        <input type="checkbox" name="is_active" id="prod_is_active" checked value="1" class="h-4.5 w-4.5 rounded border-indigo-950 bg-slate-950 text-purple-600 focus:ring-purple-500">
                        <label for="prod_is_active" class="text-sm font-semibold text-slate-300">Set active immediately</label>
                    </div>
                    
                    <div class="flex items-center gap-3">
                        <input type="checkbox" name="is_featured" id="prod_is_featured" value="1" class="h-4.5 w-4.5 rounded border-indigo-950 bg-slate-950 text-purple-600 focus:ring-purple-500">
                        <label for="prod_is_featured" class="text-sm font-semibold text-slate-300">Promote to home page carousel</label>
                    </div>
                </div>

                <!-- Save Action panel -->
                <div class="pt-2">
                    <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-purple-600 via-purple-500 to-indigo-500 py-4 text-base font-bold text-white shadow-xl shadow-purple-500/20 hover:from-purple-500 hover:to-indigo-500 transition-all duration-200">
                        Compile & Create Product
                    </button>
                    <a href="{{ route('admin.products.index') }}" class="block text-center mt-3 text-sm font-semibold text-slate-400 hover:text-slate-300 transition-colors">
                        Cancel & Return
                    </a>
                </div>

            </div>
            
        </div>
    </form>

    <script>
        let variantIndex = 0;

        function addVariantRow() {
            // Hide no-variants placeholder message
            document.getElementById('no-variants-message').style.display = 'none';

            const container = document.getElementById('variants-container');
            const row = document.createElement('div');
            row.id = `variant-row-${variantIndex}`;
            row.className = "grid grid-cols-1 sm:grid-cols-5 gap-3 p-3.5 bg-slate-950/60 rounded-xl border border-indigo-950/40 relative group items-center";
            
            row.innerHTML = `
                <div class="col-span-1 sm:col-span-5 grid grid-cols-2 gap-3 mb-2">
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Attr * (e.g. Size)</label>
                        <input type="text" name="variants[${variantIndex}][attribute_name]" required class="w-full rounded-lg border border-indigo-950/60 bg-slate-950 px-3 py-1.5 text-xs text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500" placeholder="Color">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Attr Bangla</label>
                        <input type="text" name="variants[${variantIndex}][attribute_name_bn]" class="w-full rounded-lg border border-indigo-950/60 bg-slate-950 px-3 py-1.5 text-xs text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500" placeholder="রঙ">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Val * (e.g. XL)</label>
                        <input type="text" name="variants[${variantIndex}][attribute_value]" required class="w-full rounded-lg border border-indigo-950/60 bg-slate-950 px-3 py-1.5 text-xs text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500" placeholder="Royal Purple">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Val Bangla</label>
                        <input type="text" name="variants[${variantIndex}][attribute_value_bn]" class="w-full rounded-lg border border-indigo-950/60 bg-slate-950 px-3 py-1.5 text-xs text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500" placeholder="রয়েল পার্পল">
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Modifier ($)</label>
                    <input type="number" step="0.01" name="variants[${variantIndex}][price_modifier]" class="w-full rounded-lg border border-indigo-950/60 bg-slate-950 px-3 py-1.5 text-xs text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500" placeholder="0.00">
                </div>
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Stock *</label>
                    <input type="number" name="variants[${variantIndex}][stock_qty]" required class="w-full rounded-lg border border-indigo-950/60 bg-slate-950 px-3 py-1.5 text-xs text-slate-200 focus:border-purple-500 focus:ring-1 focus:ring-purple-500" placeholder="10">
                </div>
                <div class="flex items-center justify-between pt-4 sm:pt-0 sm:col-span-3">
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

        // Media upload preview controller
        let uploadedFiles = [];
        const fileSelector = document.getElementById('file-selector');
        const previewGrid = document.getElementById('preview-grid');
        const primaryImageIndexInput = document.getElementById('primary_image_index');

        if (fileSelector) {
            fileSelector.addEventListener('change', handleFileSelection);
        }

        function handleFileSelection(e) {
            const files = Array.from(e.target.files);
            
            // Append files to our array
            files.forEach(file => {
                if (file.type.startsWith('image/')) {
                    uploadedFiles.push(file);
                }
            });

            // Update previews
            renderPreviews();
            syncFilesToInput();
        }

        function renderPreviews() {
            previewGrid.innerHTML = '';
            
            if (uploadedFiles.length === 0) {
                return;
            }

            uploadedFiles.forEach((file, index) => {
                const reader = new FileReader();
                const tile = document.createElement('div');
                tile.className = "relative rounded-xl border overflow-hidden transition-all bg-slate-950 aspect-square " + 
                    (parseInt(primaryImageIndexInput.value) === index 
                     ? "border-purple-500 ring-2 ring-purple-500/30" 
                     : "border-indigo-950/40");
                
                tile.innerHTML = `
                    <div class="w-full h-full bg-slate-900 flex items-center justify-center overflow-hidden">
                        <img id="preview-img-${index}" class="w-full h-full object-cover" src="" alt="Thumbnail">
                    </div>
                    
                    <!-- Overlays & Controls -->
                    <div class="absolute inset-0 bg-slate-950/70 opacity-0 hover:opacity-100 transition-opacity flex flex-col justify-between p-2">
                        <!-- Discard Button -->
                        <button type="button" onclick="removeUploadedFile(${index})" class="self-end p-1.5 rounded-lg bg-red-500/20 text-red-400 border border-red-500/20 hover:bg-red-500 hover:text-white transition-colors focus:outline-none">
                            <svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                        
                        <!-- Primary Selector Overlay -->
                        <button type="button" onclick="setPrimaryImageIndex(${index})" class="w-full py-1.5 rounded-lg bg-purple-600/90 text-[10px] font-bold text-white shadow hover:bg-purple-500 transition-colors uppercase tracking-wider">
                            ${parseInt(primaryImageIndexInput.value) === index ? '★ Designated Main' : '☆ Make Primary'}
                        </button>
                    </div>
                `;

                previewGrid.appendChild(tile);

                reader.onload = function(e) {
                    const img = document.getElementById(`preview-img-${index}`);
                    if (img) img.src = e.target.result;
                }
                reader.readAsDataURL(file);
            });
        }

        function setPrimaryImageIndex(index) {
            primaryImageIndexInput.value = index;
            renderPreviews();
        }

        function removeUploadedFile(index) {
            uploadedFiles.splice(index, 1);
            
            // Adjust primary index bounds
            let primaryIndex = parseInt(primaryImageIndexInput.value);
            if (primaryIndex >= uploadedFiles.length) {
                primaryImageIndexInput.value = Math.max(0, uploadedFiles.length - 1);
            }
            
            renderPreviews();
            syncFilesToInput();
        }

        function syncFilesToInput() {
            const dt = new DataTransfer();
            uploadedFiles.forEach(file => dt.items.add(file));
            fileSelector.files = dt.files;
        }
    </script>
@endsection
