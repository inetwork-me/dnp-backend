<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{

  protected $fillable = [
    'product_id',
    'user_id',
    'rating',
    'comment',
    'status',    // e.g. ‘approved’ / ‘pending’
    'viewed',
  ];

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function product()
  {
    return $this->belongsTo(Product::class);
  }
}
