<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShippingRate extends Model
{
    protected $fillable = [
        'method_id',
        'price_type',
        'base_price',
        'weight_ranges',
        'free_shipping_threshold'
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'weight_ranges' => 'array',
        'free_shipping_threshold' => 'decimal:2',
    ];

    public function method(): BelongsTo
    {
        return $this->belongsTo(ShippingMethod::class, 'method_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForWeight($query, $weight)
    {
        return $query->where(function($q) use ($weight) {
            $q->whereNull('min_weight')
              ->orWhere('min_weight', '<=', $weight);
        })->where(function($q) use ($weight) {
            $q->whereNull('max_weight')
              ->orWhere('max_weight', '>=', $weight);
        });
    }

    public function isApplicableForWeight($weight): bool
    {
        if ($this->min_weight && $weight < $this->min_weight) {
            return false;
        }
        
        if ($this->max_weight && $weight > $this->max_weight) {
            return false;
        }
        
        return $this->is_active;
    }
}