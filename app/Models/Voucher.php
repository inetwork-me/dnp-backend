<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Voucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'customer_id', 'value', 'currency', 'points_used',
        'status', 'expires_at', 'used_at', 'used_in_order', 
        'created_by', 'notes'
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'expires_at' => 'datetime',
        'used_at' => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($voucher) {
            if (!$voucher->code) {
                $voucher->code = 'V' . strtoupper(Str::random(8));
            }
        });
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function usedInOrder()
    {
        return $this->belongsTo(Order::class, 'used_in_order');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where(function ($q) {
                        $q->whereNull('expires_at')
                          ->orWhere('expires_at', '>', now());
                    });
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', now())
                    ->where('status', 'active');
    }

    public function scopeUsed($query)
    {
        return $query->where('status', 'used');
    }

    public function scopeForCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    // Helper methods
    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isUsable()
    {
        return $this->status === 'active' && !$this->isExpired();
    }

    public function markAsUsed(?Order $order = null)
    {
        $this->update([
            'status' => 'used',
            'used_at' => now(),
            'used_in_order' => $order?->id
        ]);

        return $this;
    }

    public function markAsExpired()
    {
        $this->update(['status' => 'expired']);
        return $this;
    }

    public static function generateUniqueCode()
    {
        do {
            $code = 'V' . strtoupper(Str::random(8));
        } while (static::where('code', $code)->exists());

        return $code;
    }

    public static function createFromPoints(Customer $customer, int $points, float $value, ?int $expiryDays = null)
    {
        // Deduct points from customer
        $customer->deductLoyaltyPoints($points, 'redeemed', null, 'Converted to voucher');

        // Get default currency from loyalty settings
        $defaultCurrency = \App\Models\LoyaltySetting::get('default_currency', 'EGP');

        // Create voucher
        return static::create([
            'customer_id' => $customer->id,
            'code' => static::generateUniqueCode(),
            'value' => $value,
            'currency' => $defaultCurrency,
            'points_used' => $points,
            'expires_at' => $expiryDays ? now()->addDays($expiryDays) : null,
            'notes' => "Created from {$points} loyalty points"
        ]);
    }
}
