<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'first_name', 'last_name', 'date_of_birth', 'gender', 'phone',
        'billing_address', 'billing_city', 'billing_state', 'billing_country', 'billing_postal_code',
        'shipping_address', 'shipping_city', 'shipping_state', 'shipping_country', 'shipping_postal_code',
        'preferred_language', 'preferred_currency', 'email_notifications', 'sms_notifications',
        'total_loyalty_points', 'used_loyalty_points', 'available_loyalty_points',
        'status', 'membership_tier', 'accepts_marketing', 'last_login_at',
        'referral_code', 'referred_by', 'lifetime_value', 'total_orders', 'average_order_value'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'email_notifications' => 'boolean',
        'sms_notifications' => 'boolean',
        'accepts_marketing' => 'boolean',
        'last_login_at' => 'datetime',
        'lifetime_value' => 'decimal:2',
        'average_order_value' => 'decimal:2'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($customer) {
            if (!$customer->referral_code) {
                $customer->referral_code = strtoupper(Str::random(8));
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function loyaltyTransactions()
    {
        return $this->hasMany(LoyaltyPointsTransaction::class);
    }

    public function vouchers()
    {
        return $this->hasMany(Voucher::class);
    }

    public function referrer()
    {
        return $this->belongsTo(Customer::class, 'referred_by');
    }

    public function referrals()
    {
        return $this->hasMany(Customer::class, 'referred_by');
    }

    public function orders()
    {
        return $this->hasManyThrough(Order::class, User::class, 'id', 'user_id', 'user_id', 'id');
    }

    // Helper methods
    public function addLoyaltyPoints(int $points, string $type = 'earned', ?Order $order = null, ?Product $product = null, string $description = null)
    {
        $this->increment('total_loyalty_points', $points);
        $this->increment('available_loyalty_points', $points);

        return $this->loyaltyTransactions()->create([
            'order_id' => $order?->id,
            'product_id' => $product?->id,
            'type' => $type,
            'points' => $points,
            'description' => $description,
            'source' => 'system'
        ]);
    }

    public function deductLoyaltyPoints(int $points, string $type = 'redeemed', ?Order $order = null, ?string $description = null)
    {
        if ($this->available_loyalty_points < $points) {
            throw new \Exception('Insufficient loyalty points');
        }

        $this->increment('used_loyalty_points', $points);
        $this->decrement('available_loyalty_points', $points);

        return $this->loyaltyTransactions()->create([
            'order_id' => $order?->id,
            'type' => $type,
            'points' => -$points,
            'description' => $description,
            'source' => 'system'
        ]);
    }

    public function getFullNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function updateMembershipTier()
    {
        $totalPoints = $this->total_loyalty_points;
        
        if ($totalPoints >= 10000) {
            $tier = 'platinum';
        } elseif ($totalPoints >= 5000) {
            $tier = 'gold';
        } elseif ($totalPoints >= 1000) {
            $tier = 'silver';
        } else {
            $tier = 'bronze';
        }
        
        $this->update(['membership_tier' => $tier]);
        return $tier;
    }
}
