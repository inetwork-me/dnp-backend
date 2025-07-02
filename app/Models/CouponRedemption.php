<?php
// app/Models/CouponRedemption.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// app/Models/CouponRedemption.php

class CouponRedemption extends Model
{
    protected $fillable = [
        'coupon_id', 'user_id', 'cart_id',
        'order_id',     // ← added
        'discount',
    ];

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
