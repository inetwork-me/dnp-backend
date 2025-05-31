// app/Models/CouponRedemption.php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CouponRedemption extends Model
{
    protected $fillable = [
        'coupon_id', 'user_id', 'order_id', 'redeemed_at'
    ];

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
