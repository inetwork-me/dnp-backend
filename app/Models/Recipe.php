<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Recipe extends Model
{
    protected $with = ['recipe_translations'];
    use SoftDeletes;
    
    public function category() {
        return $this->belongsTo(RecipeCategory::class, 'category_id');
    }

    public function getTranslation($field = '', $lang = false)
    {
        $lang = $lang == false ? App::getLocale() : $lang;
        $recipe_translation = $this->recipe_translations->where('lang', $lang)->first();
        return $recipe_translation != null ? $recipe_translation->$field : $this->$field;
    }

    public function recipe_translations()
    {
        return $this->hasMany(RecipeTranslation::class);
    }
}
