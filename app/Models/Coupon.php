<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'type', // percentage, fixed
        'value',
        'max_discount',
        'min_order',
        'expires_at',
        'usage_limit',
        'used_count',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'min_order' => 'decimal:2',
        'expires_at' => 'datetime',
        'usage_limit' => 'integer',
        'used_count' => 'integer',
    ];

    /**
     * Check if the coupon is valid.
     */
    public function isValidForOrder(float $orderTotal): bool
    {
        // Check expiry
        if ($this->expires_at->isPast()) {
            return false;
        }

        // Check usage limit
        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit) {
            return false;
        }

        // Check minimum order requirement
        if ($orderTotal < (float) $this->min_order) {
            return false;
        }

        return true;
    }

    /**
     * Calculate discount amount.
     */
    public function calculateDiscount(float $orderTotal): float
    {
        $discount = 0.00;

        if ($this->type === 'percentage') {
            $discount = $orderTotal * ((float) $this->value / 100);
            if ($this->max_discount !== null) {
                $discount = min($discount, (float) $this->max_discount);
            }
        } elseif ($this->type === 'fixed') {
            $discount = (float) $this->value;
        }

        return min($discount, $orderTotal);
    }
}
