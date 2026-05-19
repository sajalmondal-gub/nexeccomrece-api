<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    /**
     * Fetch products list with advanced search, categorizations, and sorting.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Product::with(['category', 'brand', 'variants'])
            ->where('is_active', true);

        // 1. Filter by search text
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('short_description', 'like', "%{$search}%");
            });
        }

        // 2. Filter by Category slug or id
        if ($request->filled('category')) {
            $cat = $request->category;
            $query->whereHas('category', function ($q) use ($cat) {
                if (is_numeric($cat)) {
                    $q->where('id', $cat);
                } else {
                    $q->where('slug', $cat);
                }
            });
        }

        // 3. Filter by Brand slug or id
        if ($request->filled('brand')) {
            $br = $request->brand;
            $query->whereHas('brand', function ($q) use ($br) {
                if (is_numeric($br)) {
                    $q->where('id', $br);
                } else {
                    $q->where('slug', $br);
                }
            });
        }

        // 4. Sort Options
        $sortBy = $request->input('sort_by', 'newest');
        switch ($sortBy) {
            case 'price_low_high':
                $query->orderBy('base_price', 'asc');
                break;
            case 'price_high_low':
                $query->orderBy('base_price', 'desc');
                break;
            case 'featured':
                $query->orderBy('is_featured', 'desc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $products = $query->paginate($request->input('per_page', 10));

        // Format items to append computed final_price
        $products->getCollection()->transform(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'short_description' => $product->short_description,
                'image' => $product->image,
                'base_price' => (float) $product->base_price,
                'sale_price' => $product->sale_price ? (float) $product->sale_price : null,
                'final_price' => (float) $product->final_price,
                'stock_qty' => (int) $product->stock_qty,
                'stock_status' => $product->stock_status,
                'is_featured' => (bool) $product->is_featured,
                'category' => $product->category ? $product->category->name : null,
                'brand' => $product->brand ? $product->brand->name : null,
                'variants_count' => $product->variants->count(),
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $products
        ]);
    }

    /**
     * Fetch a single detailed product with active variants, ratings, and related items.
     */
    public function show(string $slug): JsonResponse
    {
        $product = Product::with(['category', 'brand', 'variants', 'reviews.user'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found.'
            ], 404);
        }

        // Calculate reviews average & approved list
        $approvedReviews = $product->reviews->where('approved', true);
        $avgRating = $approvedReviews->avg('rating') ?? 0.0;

        // Fetch related products (same category, excluding self)
        $related = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->limit(4)
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'slug' => $p->slug,
                    'image' => $p->image,
                    'final_price' => (float) $p->final_price,
                ];
            });

        // Format product details
        $formattedProduct = [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'description' => $product->description,
            'short_description' => $product->short_description,
            'sku' => $product->sku,
            'base_price' => (float) $product->base_price,
            'sale_price' => $product->sale_price ? (float) $product->sale_price : null,
            'final_price' => (float) $product->final_price,
            'stock_qty' => (int) $product->stock_qty,
            'stock_status' => $product->stock_status,
            'image' => $product->image,
            'images' => $product->images ? json_decode($product->images) : [],
            'avg_rating' => round($avgRating, 1),
            'reviews_count' => $approvedReviews->count(),
            'brand' => $product->brand ? [
                'id' => $product->brand->id,
                'name' => $product->brand->name,
                'slug' => $product->brand->slug,
            ] : null,
            'category' => $product->category ? [
                'id' => $product->category->id,
                'name' => $product->category->name,
                'slug' => $product->category->slug,
            ] : null,
            'variants' => $product->variants->map(function ($v) {
                return [
                    'id' => $v->id,
                    'attribute_name' => $v->attribute_name,
                    'attribute_value' => $v->attribute_value,
                    'price_modifier' => (float) $v->price_modifier,
                    'stock_qty' => (int) $v->stock_qty,
                    'sku' => $v->sku,
                ];
            }),
            'reviews' => $approvedReviews->map(function ($r) {
                return [
                    'id' => $r->id,
                    'reviewer' => $r->user->name ?? 'Verified Buyer',
                    'rating' => (int) $r->rating,
                    'comment' => $r->comment,
                    'created_at' => $r->created_at->diffForHumans(),
                ];
            })->values(),
        ];

        return response()->json([
            'status' => 'success',
            'data' => [
                'product' => $formattedProduct,
                'related_products' => $related
            ]
        ]);
    }

    /**
     * Return hierarchical tree list of categories.
     */
    public function categories(): JsonResponse
    {
        $categories = Category::whereNull('parent_id')
            ->with(['children' => function ($q) {
                $q->where('is_active', true);
            }])
            ->where('is_active', true)
            ->get()
            ->map(function ($cat) {
                return [
                    'id' => $cat->id,
                    'name' => $cat->name,
                    'slug' => $cat->slug,
                    'icon' => $cat->icon,
                    'children' => $cat->children->map(function ($child) {
                        return [
                            'id' => $child->id,
                            'name' => $child->name,
                            'slug' => $child->slug,
                            'icon' => $child->icon,
                        ];
                    })
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => $categories
        ]);
    }

    /**
     * Return a simple array of active brands.
     */
    public function brands(): JsonResponse
    {
        $brands = Brand::where('is_active', true)
            ->get()
            ->map(function ($brand) {
                return [
                    'id' => $brand->id,
                    'name' => $brand->name,
                    'slug' => $brand->slug,
                    'logo' => $brand->logo,
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => $brands
        ]);
    }
}
