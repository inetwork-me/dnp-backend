<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecipeCategory extends Model
{
    use SoftDeletes;

    public function getTranslation($field = '', $lang = false){
        $lang = $lang == false ? App::getLocale() : $lang;
        $recipe_translation = $this->recipe_category_translations->where('lang', $lang)->first();
        return $recipe_translation != null ? $recipe_translation->$field : $this->$field;
    }

    public function recipe_category_translations(){
       return $this->hasMany(RecipeCategoryTranslation::class);
    }
    
    public function recipes()
    {
        return $this->hasMany(Recipe::class);
    }
}
