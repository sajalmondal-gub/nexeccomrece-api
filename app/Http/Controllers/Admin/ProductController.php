<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Enforce product management authorization.
     */
    private function checkPermission(): void
    {
        if (!auth()->user()->hasPermissionTo('manage_products') && !auth()->user()->hasRole('Super Admin')) {
            abort(403, 'Unauthorized access.');
        }
    }

    /**
     * Display a listing of the products.
     */
    public function index(): View
    {
        $this->checkPermission();
        $products = Product::with(['category', 'brand', 'variants'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create(): View
    {
        $this->checkPermission();
        $categories = Category::orderBy('name')->get();
        $brands = Brand::orderBy('name')->get();

        return view('admin.products.create', compact('categories', 'brands'));
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->checkPermission();
        $request->validate([
            'name' => 'required|string|max:255',
            'name_bn' => 'nullable|string|max:255',
            'slug' => 'nullable|string|unique:products,slug',
            'description' => 'required|string',
            'description_bn' => 'nullable|string',
            'short_description' => 'nullable|string|max:255',
            'short_description_bn' => 'nullable|string|max:255',
            'base_price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:base_price',
            'stock_qty' => 'required|integer|min:0',
            'sku' => 'nullable|string|unique:products,sku',
            'deal_type' => 'nullable|in:flash,weekly,monthly',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'uploaded_images' => 'nullable|array',
            'uploaded_images.*' => 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
        ]);

        $slug = $request->slug ? Str::slug($request->slug) : Str::slug($request->name);

        // Handle file uploads
        $storedImages = [];
        if ($request->hasFile('uploaded_images')) {
            $uploadPath = public_path('uploads/products');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            foreach ($request->file('uploaded_images') as $file) {

                $filename = time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
                $file->move($uploadPath, $filename);
                $storedImages[] = $filename;
            }
        }

        // Determine Primary image
        $primaryIndex = (int) $request->input('primary_image_index', 0);
        $primaryImage = isset($storedImages[$primaryIndex]) ? $storedImages[$primaryIndex] : ($storedImages[0] ?? 'default_product.png');

        DB::transaction(function () use ($request, $slug, $storedImages, $primaryImage) {
            $product = Product::create([
                'name' => $request->name,
                'name_bn' => $request->name_bn,
                'slug' => $slug,
                'description' => $request->description,
                'description_bn' => $request->description_bn,
                'short_description' => $request->short_description,
                'short_description_bn' => $request->short_description_bn,
                'base_price' => $request->base_price,
                'sale_price' => $request->sale_price,
                'stock_qty' => $request->stock_qty,
                'stock_status' => $request->stock_qty > 0 ? 'in_stock' : 'out_of_stock',
                'sku' => $request->sku ?? 'NEX-' . strtoupper(Str::random(8)),
                'category_id' => $request->category_id,
                'brand_id' => $request->brand_id,
                'is_featured' => $request->has('is_featured'),
                'is_active' => $request->has('is_active'),
                'deal_type' => $request->deal_type,
                'image' => $primaryImage,
                'images' => json_encode($storedImages),
            ]);

            // Save variants if any are provided
            if ($request->has('variants') && is_array($request->variants)) {
                foreach ($request->variants as $variantData) {
                    if (!empty($variantData['attribute_name']) && !empty($variantData['attribute_value'])) {
                        ProductVariant::create([
                            'product_id' => $product->id,
                            'attribute_name' => $variantData['attribute_name'],
                            'attribute_name_bn' => $variantData['attribute_name_bn'] ?? null,
                            'attribute_value' => $variantData['attribute_value'],
                            'attribute_value_bn' => $variantData['attribute_value_bn'] ?? null,
                            'price_modifier' => $variantData['price_modifier'] ?? 0.00,
                            'stock_qty' => $variantData['stock_qty'] ?? 0,
                            'sku' => $variantData['sku'] ?? $product->sku . '-' . strtoupper(substr($variantData['attribute_value'], 0, 3)),
                        ]);
                    }
                }
            }
        });

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully with variants and visual gallery!');
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(int $id): View
    {
        $this->checkPermission();
        $product = Product::with('variants')->findOrFail($id);
        $categories = Category::orderBy('name')->get();
        $brands = Brand::orderBy('name')->get();

        return view('admin.products.edit', compact('product', 'categories', 'brands'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $this->checkPermission();
        $product = Product::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'name_bn' => 'nullable|string|max:255',
            'slug' => "required|string|unique:products,slug,{$product->id}",
            'description' => 'required|string',
            'description_bn' => 'nullable|string',
            'short_description' => 'nullable|string|max:255',
            'short_description_bn' => 'nullable|string|max:255',
            'base_price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:base_price',
            'stock_qty' => 'required|integer|min:0',
            'sku' => "nullable|string|unique:products,sku,{$product->id}",
            'deal_type' => 'nullable|in:flash,weekly,monthly',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'uploaded_images' => 'nullable|array',
            'uploaded_images.*' => 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
        ]);

        // 1. Manage Existing Images array
        $existingImages = !empty($product->images) ? (is_string($product->images) ? json_decode($product->images, true) : $product->images) : [];
        if (empty($existingImages) && !empty($product->image)) {
            $existingImages = [$product->image];
        }

        // Parse removals
        $removedExisting = json_decode($request->input('removed_existing_images', '[]'), true);
        if (is_array($removedExisting)) {
            foreach ($removedExisting as $removedPath) {
                // Remove from local list
                $existingImages = array_filter($existingImages, function($path) use ($removedPath) {
                    return $path !== $removedPath;
                });
                
                // Discard file from storage if it is local and exists
                $filePath = public_path('uploads/products/' . $removedPath);
                if (file_exists($filePath) && is_file($filePath)) {
                    @unlink($filePath);
                }
            }
            $existingImages = array_values($existingImages); // reindex
        }

        // 2. Handle New Uploads
        $newStoredImages = [];
        if ($request->hasFile('uploaded_images')) {
            $uploadPath = public_path('uploads/products');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            foreach ($request->file('uploaded_images') as $file) {
                $filename = time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
                $file->move($uploadPath, $filename);
                $newStoredImages[] = $filename;
            }
        }

        // 3. Merge All Gallery Images
        $mergedImages = array_merge($existingImages, $newStoredImages);

        // 4. Calculate designated primary
        $primaryImage = 'default_product.png';
        $primaryIndex = (int) $request->input('primary_image_index', -1);
        
        if ($primaryIndex !== -1 && isset($newStoredImages[$primaryIndex])) {
            // New upload designated
            $primaryImage = $newStoredImages[$primaryIndex];
        } else {
            // Existing or fallback
            $existingPrimary = $request->input('primary_existing_image');
            if (!empty($existingPrimary) && in_array($existingPrimary, $mergedImages)) {
                $primaryImage = $existingPrimary;
            } else {
                $primaryImage = $mergedImages[0] ?? 'default_product.png';
            }
        }

        DB::transaction(function () use ($request, $product, $mergedImages, $primaryImage) {
            $product->update([
                'name' => $request->name,
                'name_bn' => $request->name_bn,
                'slug' => Str::slug($request->slug),
                'description' => $request->description,
                'description_bn' => $request->description_bn,
                'short_description' => $request->short_description,
                'short_description_bn' => $request->short_description_bn,
                'base_price' => $request->base_price,
                'sale_price' => $request->sale_price,
                'stock_qty' => $request->stock_qty,
                'stock_status' => $request->stock_qty > 0 ? 'in_stock' : 'out_of_stock',
                'sku' => $request->sku ?? $product->sku,
                'category_id' => $request->category_id,
                'brand_id' => $request->brand_id,
                'is_featured' => $request->has('is_featured'),
                'is_active' => $request->has('is_active'),
                'deal_type' => $request->deal_type,
                'image' => $primaryImage,
                'images' => json_encode($mergedImages),
            ]);

            // Replace variants
            $product->variants()->delete();

            if ($request->has('variants') && is_array($request->variants)) {
                foreach ($request->variants as $variantData) {
                    if (!empty($variantData['attribute_name']) && !empty($variantData['attribute_value'])) {
                        ProductVariant::create([
                            'product_id' => $product->id,
                            'attribute_name' => $variantData['attribute_name'],
                            'attribute_name_bn' => $variantData['attribute_name_bn'] ?? null,
                            'attribute_value' => $variantData['attribute_value'],
                            'attribute_value_bn' => $variantData['attribute_value_bn'] ?? null,
                            'price_modifier' => $variantData['price_modifier'] ?? 0.00,
                            'stock_qty' => $variantData['stock_qty'] ?? 0,
                            'sku' => $variantData['sku'] ?? $product->sku . '-' . strtoupper(substr($variantData['attribute_value'], 0, 3)),
                        ]);
                    }
                }
            }
        });

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully!');
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $this->checkPermission();
        $product = Product::findOrFail($id);
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully!');
    }
}
