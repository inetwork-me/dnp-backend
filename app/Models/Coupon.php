<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Coupon extends Model
{
    protected $fillable = [
        'code', 'type', 'value',
        'usage_limit_per_customer', 'usage_limit_global',
        'starts_at', 'ends_at', 'active', 'times_used'
    ];
    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at'   => 'datetime',
        'active'    => 'boolean',
    ];

    public function redemptions()
    {
        return $this->hasMany(CouponRedemption::class);
    }

    public function isValidForUser($user = null)
    {
        if (!$this->active) {
            return false;
        }

        $now = Carbon::now();
        if ($this->starts_at && $now->lt($this->starts_at)) return false;
        if ($this->ends_at   && $now->gt($this->ends_at))   return false;

        // only enforce per-customer usage if we have a logged-in user
        if ($this->usage_limit_per_customer && $user) {
            $usedByUser = $this->redemptions()
                ->where('user_id', $user->id)
                ->count();
            if ($usedByUser >= $this->usage_limit_per_customer) {
                return false;
            }
        }

        // global limit
        if ($this->usage_limit_global) {
            $usedGlobal = $this->redemptions()->count();
            if ($usedGlobal >= $this->usage_limit_global) {
                return false;
            }
        }

        return true;
    }


    public function calculateDiscount($amount)
    {
        if ($this->type === 'percent') {
            return round($amount * ($this->value / 100), 2);
        }

        return min($this->value, $amount);
    }
}
