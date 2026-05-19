<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\TranslatableTrait;

class ProductVariant extends Model
{
    use HasFactory, TranslatableTrait;

    protected $fillable = [
        'product_id',
        'attribute_name',
        'attribute_name_bn',
        'attribute_value',
        'attribute_value_bn',
        'price_modifier',
        'stock_qty',
        'sku',
    ];

    protected $casts = [
        'price_modifier' => 'decimal:2',
        'stock_qty' => 'integer',
    ];

    /**
     * Get the product that owns this variant.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getAttributeNameAttribute($value)
    {
        return $this->getTranslatedAttribute('attribute_name', $value);
    }

    public function getAttributeValueAttribute($value)
    {
        return $this->getTranslatedAttribute('attribute_value', $value);
    }
}
