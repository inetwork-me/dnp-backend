<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackageDetail extends Model
{
    protected $fillable = ['product_id', 'min_months', 'min_products', 'points'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
