<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories and brands.
     */
    public function index(): View
    {
        $categories = Category::with('parent')
            ->orderBy('name')
            ->get();
            
        $parentCategories = Category::whereNull('parent_id')
            ->orderBy('name')
            ->get();

        $brands = Brand::orderBy('name')->get();

        return view('admin.categories.index', compact('categories', 'parentCategories', 'brands'));
    }

    /**
     * Store a newly created category.
     */
    public function storeCategory(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'name_bn' => 'nullable|string|max:255',
            'slug' => 'nullable|string|unique:categories,slug',
            'parent_id' => 'nullable|exists:categories,id',
            'icon' => 'nullable|string|max:50',
        ]);

        $slug = $request->slug ? Str::slug($request->slug) : Str::slug($request->name);

        Category::create([
            'name' => $request->name,
            'name_bn' => $request->name_bn,
            'slug' => $slug,
            'parent_id' => $request->parent_id,
            'icon' => $request->icon ?? 'tag',
            'is_active' => $request->has('is_active'),
            'is_featured' => $request->has('is_featured'),
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Category created successfully!');
    }

    /**
     * Delete a category.
     */
    public function destroyCategory(int $id): RedirectResponse
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Category deleted successfully!');
    }

    /**
     * Store a newly created brand.
     */
    public function storeBrand(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'name_bn' => 'nullable|string|max:255',
            'slug' => 'nullable|string|unique:brands,slug',
            'logo' => 'nullable|string|max:255',
        ]);

        $slug = $request->slug ? Str::slug($request->slug) : Str::slug($request->name);

        Brand::create([
            'name' => $request->name,
            'name_bn' => $request->name_bn,
            'slug' => $slug,
            'logo' => $request->logo ?? 'default_logo.png',
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Brand created successfully!');
    }

    /**
     * Delete a brand.
     */
    public function destroyBrand(int $id): RedirectResponse
    {
        $brand = Brand::findOrFail($id);
        $brand->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Brand deleted successfully!');
    }
}
