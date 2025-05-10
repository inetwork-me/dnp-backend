<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecipeCategoryTranslation extends Model
{
  protected $fillable = ['category_name', 'lang', 'recipe_category_id'];

  public function recipe_category(){
    return $this->belongsTo(RecipeCategory::class);
  }
}
