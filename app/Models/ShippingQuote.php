<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShippingQuote extends Model
{
    protected $fillable = [
        'cart_id',
        'destination_address',
        'available_methods',
        'selected_method_id',
        'expires_at'
    ];

    protected $casts = [
        'destination_address' => 'array',
        'available_methods' => 'array',
        'expires_at' => 'datetime',
    ];

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function selectedMethod(): BelongsTo
    {
        return $this->belongsTo(ShippingMethod::class, 'selected_method_id');
    }

    public function scopeActive($query)
    {
        return $query->where('expires_at', '>', now());
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function getSelectedMethodDetails()
    {
        if (!$this->selected_method_id || empty($this->available_methods)) {
            return null;
        }

        return collect($this->available_methods)->firstWhere('method_id', $this->selected_method_id);
    }
}