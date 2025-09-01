<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShippingMethod extends Model
{
    protected $fillable = [
        'carrier_id',
        'zone_id',
        'name',
        'service_code',
        'estimated_days_min',
        'estimated_days_max',
        'rate_source',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    const TYPE_FLAT_RATE = 'flat_rate';
    const TYPE_WEIGHT_BASED = 'weight_based';
    const TYPE_LIVE_RATES = 'live_rates';
    const TYPE_FREE = 'free';

    public function zone(): BelongsTo
    {
        return $this->belongsTo(ShippingZone::class, 'zone_id');
    }

    public function carrier(): BelongsTo
    {
        return $this->belongsTo(ShippingCarrier::class, 'carrier_id');
    }

    public function rates(): HasMany
    {
        return $this->hasMany(ShippingRate::class, 'method_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForOrderAmount($query, $amount)
    {
        return $query->where(function($q) use ($amount) {
            $q->whereNull('min_order_amount')
              ->orWhere('min_order_amount', '<=', $amount);
        })->where(function($q) use ($amount) {
            $q->whereNull('max_order_amount')
              ->orWhere('max_order_amount', '>=', $amount);
        });
    }

    public function isAvailableForOrder($orderAmount): bool
    {
        if ($this->min_order_amount && $orderAmount < $this->min_order_amount) {
            return false;
        }
        
        if ($this->max_order_amount && $orderAmount > $this->max_order_amount) {
            return false;
        }
        
        return $this->is_active;
    }
}