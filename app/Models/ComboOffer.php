<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ComboOffer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'image',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price' => 'decimal:2',
    ];

    /**
     * Get the products bundled in this combo offer.
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'combo_offer_products')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    /**
     * Helper to get total price of individual items in the combo
     */
    public function getOriginalPriceAttribute(): float
    {
        return (float) $this->products->sum(function ($product) {
            return $product->final_price * $product->pivot->quantity;
        });
    }

    /**
     * Helper to get the discount savings amount
     */
    public function getSavingsAmountAttribute(): float
    {
        return max(0, $this->original_price - (float) $this->price);
    }
}
