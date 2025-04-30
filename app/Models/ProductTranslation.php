<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductTranslation extends Model
{
    protected $fillable = ['product_id', 'name', 'unit', 'description', 'lang','specifications','product_service_adress','sub_title','product_service_custom_data'];

    public function product(){
      return $this->belongsTo(Product::class);
    }

    protected $casts = [
      'product_service_custom_data' => 'array', // Cast to array for easy manipulation
  ];
}
