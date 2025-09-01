<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShippingZone extends Model
{
    protected $fillable = [
        'name',
        'countries',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'countries' => 'array'
    ];

    public function shippingMethods(): HasMany
    {
        return $this->hasMany(ShippingMethod::class, 'zone_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function includesAddress($address): bool
    {
        // Check if the address matches this zone's criteria
        if (!empty($this->countries) && !in_array($address['country'], $this->countries)) {
            return false;
        }
        
        return true;
    }
}