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
        'total_loyalty_points', 'used_loyalty_points',
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

    // Computed attribute for available loyalty points (only from completed orders)
    public function getAvailableLoyaltyPointsAttribute()
    {
        return $this->loyaltyTransactions()
            ->leftJoin('orders', 'loyalty_points_transactions.order_id', '=', 'orders.id')
            ->where(function ($query) {
                $query->whereNull('loyalty_points_transactions.order_id') // Non-order transactions (bonuses, referrals, manual adjustments, etc.)
                      ->orWhereIn('orders.status', ['completed']); // Only completed orders count
            })
            ->sum('loyalty_points_transactions.points');
    }

    // Helper methods
    public function addLoyaltyPoints(int $points, string $type = 'earned', ?Order $order = null, ?Product $product = null, string $description = null)
    {
        $this->increment('total_loyalty_points', $points);
        // Don't increment available_loyalty_points here - it's now computed

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
        // Don't decrement available_loyalty_points here - it's now computed

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
        $totalEarnedPoints = $this->total_loyalty_points; // Use total earned points for tier calculation

        // Get tier thresholds from settings
        $silverThreshold = \App\Models\LoyaltySetting::get('silver_tier_points', 1000);
        $goldThreshold = \App\Models\LoyaltySetting::get('gold_tier_points', 5000);
        $platinumThreshold = \App\Models\LoyaltySetting::get('platinum_tier_points', 10000);

        if ($totalEarnedPoints >= $platinumThreshold) {
            $tier = 'platinum';
        } elseif ($totalEarnedPoints >= $goldThreshold) {
            $tier = 'gold';
        } elseif ($totalEarnedPoints >= $silverThreshold) {
            $tier = 'silver';
        } else {
            $tier = 'bronze';
        }

        $this->update(['membership_tier' => $tier]);
        return $tier;
    }
}
