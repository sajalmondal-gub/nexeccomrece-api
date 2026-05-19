<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\TranslatableTrait;

class Category extends Model
{
    use HasFactory, TranslatableTrait;

    protected $fillable = [
        'name',
        'name_bn',
        'slug',
        'parent_id',
        'icon',
        'is_active',
        'is_featured'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the parent category.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * Get the child categories.
     */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /**
     * Get all products for the category.
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
