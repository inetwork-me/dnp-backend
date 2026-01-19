<?php
// app/Models/Post.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
  protected $fillable = [
    'post_type_id',
    'category_id',
    'title',
    'description',
    'slug',
    'content',
    'blocks',
    'featured_image',
    'seo',
    'status',
    'published_at',
    'author_id',
    'fields'
  ];

  protected $casts = [
    'title'         => 'array',   // { en:string, ar:string }
    'description'   => 'array',   // { en: string, ar: string }
    'content'       => 'array',   // your block JSON
    'published_at'  => 'datetime',
    'blocks'       => 'array',
    // featured_image handled by accessor to check media existence
    'seo'            => 'array',
    'fields'            => 'array'

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
    return $this->belongsTo(PostTypeCategory::class, 'category_id');
  }


  public function author()
  {
    return $this->belongsTo(User::class, 'author_id');
  }

  /**
   * Get the featured image, checking if the media still exists.
   */
  public function getFeaturedImageAttribute($value)
  {
    $data = is_string($value) ? json_decode($value, true) : $value;

    \Log::info('Featured image accessor called', ['data' => $data]);

    if (empty($data) || !isset($data['id'])) {
      \Log::info('No id found in featured_image, returning data as-is');
      return $data;
    }

    // Check if media still exists
    $mediaExists = Media::where('id', $data['id'])->exists();
    \Log::info('Media exists check', ['id' => $data['id'], 'exists' => $mediaExists]);

    if (!$mediaExists) {
      return null;
    }

    return $data;
  }

  /**
   * Set the featured image attribute.
   */
  public function setFeaturedImageAttribute($value)
  {
    $this->attributes['featured_image'] = is_array($value) ? json_encode($value) : $value;
  }
}
