<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecipeTranslation extends Model
{
  protected $fillable = [
    'title',
    'slug',
    'description',
    'meta_title',
    'meta_img',
    'meta_description',
    'meta_keywords',
    'lang',
    'recipe_id'
  ];

  public function recipe()
  {
    return $this->belongsTo(Recipe::class);
  }
}
