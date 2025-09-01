<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShippingCarrier extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'is_active',
        'api_config',
        'supports_tracking',
        'supports_labels',
        'supports_live_rates',
        'cache_duration'
    ];

    protected $casts = [
        'api_config' => 'array',
        'is_active' => 'boolean',
        'supports_tracking' => 'boolean',
        'supports_labels' => 'boolean',
        'supports_live_rates' => 'boolean',
    ];

    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class, 'carrier_id');
    }

    public function shippingMethods(): HasMany
    {
        return $this->hasMany(ShippingMethod::class, 'carrier_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function isConfigured(): bool
    {
        if (empty($this->api_config)) {
            return false;
        }

        // Check based on carrier type
        switch (strtolower($this->slug)) {
            case 'aramex':
                return isset($this->api_config['username']) && 
                       isset($this->api_config['password']) && 
                       isset($this->api_config['account_number']) &&
                       isset($this->api_config['account_pin']) &&
                       !empty($this->api_config['username']) &&
                       !empty($this->api_config['password']);
                       
            case 'dhl':
            case 'fedex':
                return isset($this->api_config['api_key']) && 
                       !empty($this->api_config['api_key']);
                       
            case 'local':
                return true; // Local delivery doesn't need API config
                
            default:
                return isset($this->api_config['api_key']) && 
                       !empty($this->api_config['api_key']);
        }
    }
}