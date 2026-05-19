<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\TranslatableTrait;

class Brand extends Model
{
    use HasFactory, TranslatableTrait;

    protected $fillable = [
        'name',
        'name_bn',
        'slug',
        'logo',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get all products for the brand.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function getNameAttribute($value)
    {
        return $this->getTranslatedAttribute('name', $value);
    }
}
