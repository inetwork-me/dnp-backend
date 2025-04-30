<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FrequentlyBroughtProduct extends Model
{
    use HasFactory;

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    
    public function frequently_brought_product()
    {
        return $this->belongsTo(Product::class, 'frequently_brought_product_id');
    }
}
