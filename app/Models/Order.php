<?php

// app/Models/Order.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'cart_id',
        'order_number',
        'status',
        'total_amount',
        'coupon_id',
        'shipping_method_id',
        'shipping_cost',
        'shipping_quote_data',
        'shipping_address',
        'billing_address',
        'payment_method',
        'payment_status',
    ];

    protected $casts = [
        'shipping_address' => 'array',
        'billing_address'  => 'array',
        'shipping_quote_data' => 'array',
        'shipping_cost' => 'decimal:2',
    ];

    protected $withCount = ['items'];
    protected $with      = ['user'];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
    public function statusHistories()
    {
        return $this->hasMany(OrderStatusHistory::class)
            ->with('user')        // eager‐load the actor
            ->orderByDesc('created_at');
    }
    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function shippingMethod()
    {
        return $this->belongsTo(\App\Models\ShippingMethod::class);
    }

    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class);
    }

    public function latestShipment()
    {
        return $this->hasOne(Shipment::class)->latest();
    }

    public function getShippingStatusAttribute(): string
    {
        $latestShipment = $this->latestShipment;
        
        if (!$latestShipment) {
            return 'not_shipped';
        }
        
        return $latestShipment->status;
    }

    public function getTrackingNumberAttribute(): ?string
    {
        return $this->latestShipment?->tracking_number;
    }

    public function shouldCreateShipment(): bool
    {
        return in_array($this->status, ['confirmed', 'processing']) && 
               !$this->shipments()->exists();
    }
}
