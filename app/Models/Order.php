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
        'shipping_address',
        'billing_address',
        'payment_method',
        'payment_status',
    ];

    protected $casts = [
        'shipping_address' => 'array',
        'billing_address'  => 'array',
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
}
