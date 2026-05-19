<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'short_description',
        'brand_id',
        'category_id',
        'base_price',
        'sale_price',
        'sku',
        'stock_status',
        'stock_qty',
        'is_featured',
        'deal_type',
        'is_active',
        'image',
        'images',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'stock_qty' => 'integer',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'images' => 'array',
    ];

    /**
     * Get the brand of the product.
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Get the category of the product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the variants of the product.
     */
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    /**
     * Get the reviews for the product.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get approved reviews for the product.
     */
    public function approvedReviews(): HasMany
    {
        return $this->hasMany(Review::class)->where('approved', true);
    }

    /**
     * Helper to get the actual price (sale price if exists, otherwise base price).
     */
    public function getFinalPriceAttribute(): float
    {
        return (float) ($this->sale_price ?? $this->base_price);
    }
}
