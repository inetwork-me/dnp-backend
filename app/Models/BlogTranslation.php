<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogTranslation extends Model
{
  protected $fillable = [
    'title',
    'slug',
    'short_description',
    'description',
    'meta_title',
    'meta_img',
    'meta_description',
    'meta_keywords',
    'lang',
    'blog_id'
  ];

  public function blog()
  {
    return $this->belongsTo(Blog::class);
  }
}
