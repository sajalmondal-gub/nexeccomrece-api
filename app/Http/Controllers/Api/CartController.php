<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ComboOffer;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CartController extends Controller
{
    /**
     * Display a listing of items in the authenticated user's cart.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $cartItems = CartItem::with(['product', 'variant', 'comboOffer.products'])
            ->where('user_id', $user->id)
            ->get();

        $formattedItems = $cartItems->map(function ($item) {
            $product = $item->product;
            $variant = $item->variant;
            $combo = $item->comboOffer;
            
            if ($combo) {
                $unitPrice = (float) $combo->price;
                return [
                    'id' => $item->id,
                    'product_id' => null,
                    'variant_id' => null,
                    'combo_offer_id' => $item->combo_offer_id,
                    'quantity' => (int) $item->quantity,
                    'unit_price' => $unitPrice,
                    'subtotal' => $unitPrice * $item->quantity,
                    'product' => null,
                    'variant' => null,
                    'combo_offer' => [
                        'id' => $combo->id,
                        'name' => $combo->name,
                        'slug' => $combo->slug,
                        'description' => $combo->description,
                        'image_url' => asset($combo->image),
                        'price' => (float) $combo->price,
                        'original_price' => $combo->original_price,
                        'savings_amount' => $combo->savings_amount,
                        'products' => $combo->products->map(fn($p) => [
                            'id' => $p->id,
                            'name' => $p->name,
                            'slug' => $p->slug,
                            'quantity' => $p->pivot->quantity,
                        ]),
                    ],
                ];
            }

            // Calculate item price: incorporate variant modifiers if any
            $unitPrice = $product ? (float) $product->final_price : 0.0;
            if ($variant) {
                $unitPrice += (float) $variant->price_modifier;
            }

            return [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'variant_id' => $item->variant_id,
                'combo_offer_id' => null,
                'quantity' => (int) $item->quantity,
                'unit_price' => $unitPrice,
                'subtotal' => $unitPrice * $item->quantity,
                'product' => $product ? [
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'image' => $product->image,
                    'sku' => $product->sku,
                ] : null,
                'variant' => $variant ? [
                    'attribute_name' => $variant->attribute_name,
                    'attribute_value' => $variant->attribute_value,
                    'sku' => $variant->sku,
                ] : null,
                'combo_offer' => null,
            ];
        });

        $cartTotal = $formattedItems->sum('subtotal');

        return response()->json([
            'status' => 'success',
            'data' => [
                'items' => $formattedItems,
                'cart_total' => $cartTotal
            ]
        ]);
    }

    /**
     * Add an item to the shopping cart.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required_without:combo_offer_id|nullable|exists:products,id',
            'variant_id' => 'nullable|exists:product_variants,id',
            'combo_offer_id' => 'required_without:product_id|nullable|exists:combo_offers,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $user = $request->user();
        $quantityToAdd = $request->quantity;

        // --- Option A: Adding Combo Offer ---
        if ($request->filled('combo_offer_id')) {
            $combo = ComboOffer::with('products')->findOrFail($request->combo_offer_id);

            if (!$combo->is_active) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'This promo combo offer is no longer active.'
                ], 422);
            }

            // 1. Stock validation for EVERY product inside the combo
            foreach ($combo->products as $product) {
                $requiredQty = $quantityToAdd * $product->pivot->quantity;

                // Check current cart qty of this combo to do aggregate stock check
                $existingCartComboQty = CartItem::where('user_id', $user->id)
                    ->where('combo_offer_id', $combo->id)
                    ->sum('quantity');

                $totalTargetQty = ($existingCartComboQty + $quantityToAdd) * $product->pivot->quantity;

                if ($product->stock_qty < $totalTargetQty) {
                    return response()->json([
                        'status' => 'error',
                        'message' => "Insufficient stock. Only {$product->stock_qty} left for product '{$product->name}' which is part of this combo bundle."
                    ], 422);
                }
            }

            // 2. Check if combo already exists in customer's cart
            $cartItem = CartItem::where('user_id', $user->id)
                ->where('combo_offer_id', $combo->id)
                ->first();

            if ($cartItem) {
                $cartItem->update(['quantity' => $cartItem->quantity + $quantityToAdd]);
            } else {
                $cartItem = CartItem::create([
                    'user_id' => $user->id,
                    'combo_offer_id' => $combo->id,
                    'quantity' => $quantityToAdd,
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Combo bundle added to cart successfully!',
                'data' => $cartItem
            ], 201);
        }

        // --- Option B: Adding Standard Product ---
        $product = Product::findOrFail($request->product_id);

        // 1. Stock validation
        if ($request->filled('variant_id')) {
            $variant = ProductVariant::findOrFail($request->variant_id);
            if ($variant->stock_qty < $quantityToAdd) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Insufficient stock. Only {$variant->stock_qty} items left for option {$variant->attribute_value}."
                ], 422);
            }
        } else {
            if ($product->stock_qty < $quantityToAdd) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Insufficient stock. Only {$product->stock_qty} items left in inventory."
                ], 422);
            }
        }

        // 2. Check if item already exists in customer's cart
        $cartItem = CartItem::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->where('variant_id', $request->variant_id)
            ->first();

        if ($cartItem) {
            $newQty = $cartItem->quantity + $quantityToAdd;
            
            // Check stock again for updated sum
            $stockQty = $request->filled('variant_id') ? $variant->stock_qty : $product->stock_qty;
            if ($stockQty < $newQty) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Cannot add more. Total in cart ({$newQty}) exceeds stock limit of {$stockQty}."
                ], 422);
            }
            
            $cartItem->update(['quantity' => $newQty]);
        } else {
            $cartItem = CartItem::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'variant_id' => $request->variant_id,
                'quantity' => $quantityToAdd,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Product added to cart successfully!',
            'data' => $cartItem
        ], 201);
    }

    /**
     * Update quantity of a cart item.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $user = $request->user();
        $cartItem = CartItem::where('user_id', $user->id)->findOrFail($id);
        $newQty = $request->quantity;

        // Stock checking
        if ($cartItem->combo_offer_id) {
            $combo = ComboOffer::with('products')->findOrFail($cartItem->combo_offer_id);
            
            foreach ($combo->products as $product) {
                $totalTargetQty = $newQty * $product->pivot->quantity;
                if ($product->stock_qty < $totalTargetQty) {
                    return response()->json([
                        'status' => 'error',
                        'message' => "Insufficient stock. Only {$product->stock_qty} left for product '{$product->name}' which is part of this combo bundle."
                    ], 422);
                }
            }
        } elseif ($cartItem->variant_id) {
            $variant = ProductVariant::findOrFail($cartItem->variant_id);
            if ($variant->stock_qty < $newQty) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Insufficient stock. Only {$variant->stock_qty} units left."
                ], 422);
            }
        } else {
            $product = Product::findOrFail($cartItem->product_id);
            if ($product->stock_qty < $newQty) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Insufficient stock. Only {$product->stock_qty} units left."
                ], 422);
            }
        }

        $cartItem->update(['quantity' => $newQty]);

        return response()->json([
            'status' => 'success',
            'message' => 'Cart item updated successfully!',
            'data' => $cartItem
        ]);
    }

    /**
     * Remove an item from the cart.
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $cartItem = CartItem::where('user_id', $user->id)->findOrFail($id);
        
        $cartItem->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Item removed from cart successfully!'
        ]);
    }

    /**
     * Merge guest cart items into the authenticated user's cart on login.
     */
    public function sync(Request $request): JsonResponse
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required_without:items.*.combo_offer_id|nullable|exists:products,id',
            'items.*.variant_id' => 'nullable|exists:product_variants,id',
            'items.*.combo_offer_id' => 'required_without:items.*.product_id|nullable|exists:combo_offers,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $user = $request->user();

        foreach ($request->items as $itemData) {
            if (!empty($itemData['combo_offer_id'])) {
                $cartItem = CartItem::where('user_id', $user->id)
                    ->where('combo_offer_id', $itemData['combo_offer_id'])
                    ->first();

                if ($cartItem) {
                    $cartItem->update([
                        'quantity' => $cartItem->quantity + $itemData['quantity']
                    ]);
                } else {
                    CartItem::create([
                        'user_id' => $user->id,
                        'combo_offer_id' => $itemData['combo_offer_id'],
                        'quantity' => $itemData['quantity'],
                    ]);
                }
            } else {
                $cartItem = CartItem::where('user_id', $user->id)
                    ->where('product_id', $itemData['product_id'])
                    ->where('variant_id', $itemData['variant_id'] ?? null)
                    ->first();

                if ($cartItem) {
                    $cartItem->update([
                        'quantity' => $cartItem->quantity + $itemData['quantity']
                    ]);
                } else {
                    CartItem::create([
                        'user_id' => $user->id,
                        'product_id' => $itemData['product_id'],
                        'variant_id' => $itemData['variant_id'] ?? null,
                        'quantity' => $itemData['quantity'],
                    ]);
                }
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Guest cart synchronized successfully!'
        ]);
    }
}
