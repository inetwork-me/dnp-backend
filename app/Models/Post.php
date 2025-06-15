<?php
// app/Models/Post.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
  protected $fillable = [
    'post_type_id',
    'title',
    'slug',
    'content',
    'blocks',
    'featured_image',
    'status',
    'published_at',
    'author_id',
  ];

  protected $casts = [
    'title'         => 'array',   // { en:string, ar:string }
    'content'       => 'array',   // your block JSON
    'published_at'  => 'datetime',
    'blocks'       => 'array',

  ];



  public function type()
  {
    return $this->belongsTo(PostType::class, 'post_type_id');
  }

  public function postType()
  {
    return $this->belongsTo(PostType::class, 'post_type_id');
  }

  public function category()
  {
    return $this->belongsTo(PostTypeCategory::class, 'post_type_category_id');
  }


  public function author()
  {
    return $this->belongsTo(User::class, 'author_id');
  }
}
