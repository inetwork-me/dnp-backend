<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FrequentlyBoughtProduct extends Model
{
    use HasFactory;

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    
    public function frequently_bought_product()
    {
        return $this->belongsTo(Product::class, 'frequently_bought_product_id');
    }
}
