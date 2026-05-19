<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ComboOffer;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class ComboOfferController extends Controller
{
    /**
     * Enforce combo offers management authorization.
     */
    private function checkPermission(): void
    {
        if (!auth()->user()->hasPermissionTo('manage_products') && !auth()->user()->hasRole('Super Admin')) {
            abort(403, 'Unauthorized access.');
        }
    }

    /**
     * Display a listing of combo offers.
     */
    public function index(): View
    {
        $this->checkPermission();
        $comboOffers = ComboOffer::with('products')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.combo_offers.index', compact('comboOffers'));
    }

    /**
     * Show the form for creating a new combo offer.
     */
    public function create(): View
    {
        $this->checkPermission();
        $products = Product::where('is_active', true)->orderBy('name')->get();
        return view('admin.combo_offers.create', compact('products'));
    }

    /**
     * Store a newly created combo offer.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->checkPermission();
        $request->validate([
            'name' => 'required|string|max:255',
            'name_bn' => 'nullable|string|max:255',
            'slug' => 'nullable|string|unique:combo_offers,slug',
            'description' => 'nullable|string',
            'description_bn' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'products' => 'required|array|min:2',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        $slug = $request->slug ? Str::slug($request->slug) : Str::slug($request->name);

        $imagePath = null;
        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $filename = time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/combos'), $filename);
            $imagePath = 'uploads/combos/' . $filename;
        }

        DB::transaction(function () use ($request, $slug, $imagePath) {
            $combo = ComboOffer::create([
                'name' => $request->name,
                'name_bn' => $request->name_bn,
                'slug' => $slug,
                'description' => $request->description,
                'description_bn' => $request->description_bn,
                'price' => $request->price,
                'image' => $imagePath ?? 'uploads/combos/default_combo.png',
                'is_active' => $request->has('is_active'),
            ]);

            foreach ($request->products as $item) {
                $combo->products()->attach($item['product_id'], [
                    'quantity' => $item['quantity'],
                ]);
            }
        });

        return redirect()->route('admin.combo-offers.index')->with('success', 'Combo Offer created successfully!');
    }

    /**
     * Show the form for editing the specified combo offer.
     */
    public function edit(int $id): View
    {
        $this->checkPermission();
        $comboOffer = ComboOffer::with('products')->findOrFail($id);
        $products = Product::where('is_active', true)->orderBy('name')->get();
        return view('admin.combo_offers.edit', compact('comboOffer', 'products'));
    }

    /**
     * Update the specified combo offer.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $this->checkPermission();
        $combo = ComboOffer::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'name_bn' => 'nullable|string|max:255',
            'slug' => "required|string|unique:combo_offers,slug,{$combo->id}",
            'description' => 'nullable|string',
            'description_bn' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'products' => 'required|array|min:2',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        $imagePath = $combo->image;
        if ($request->hasFile('image_file')) {
            // Delete old file
            if ($combo->image && $combo->image !== 'uploads/combos/default_combo.png') {
                $oldPath = public_path($combo->image);
                if (file_exists($oldPath) && is_file($oldPath)) {
                    @unlink($oldPath);
                }
            }

            $file = $request->file('image_file');
            $filename = time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/combos'), $filename);
            $imagePath = 'uploads/combos/' . $filename;
        }

        DB::transaction(function () use ($request, $combo, $imagePath) {
            $combo->update([
                'name' => $request->name,
                'name_bn' => $request->name_bn,
                'slug' => Str::slug($request->slug),
                'description' => $request->description,
                'description_bn' => $request->description_bn,
                'price' => $request->price,
                'image' => $imagePath,
                'is_active' => $request->has('is_active'),
            ]);

            // Sync pivot relationship
            $syncData = [];
            foreach ($request->products as $item) {
                $syncData[$item['product_id']] = ['quantity' => $item['quantity']];
            }
            $combo->products()->sync($syncData);
        });

        return redirect()->route('admin.combo-offers.index')->with('success', 'Combo Offer updated successfully!');
    }

    /**
     * Remove the specified combo offer from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $this->checkPermission();
        $combo = ComboOffer::findOrFail($id);

        if ($combo->image && $combo->image !== 'uploads/combos/default_combo.png') {
            $path = public_path($combo->image);
            if (file_exists($path) && is_file($path)) {
                @unlink($path);
            }
        }

        $combo->delete();
        return redirect()->route('admin.combo-offers.index')->with('success', 'Combo Offer deleted successfully!');
    }
}
