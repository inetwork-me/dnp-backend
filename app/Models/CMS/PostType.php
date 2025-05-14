<?php

namespace App\Models\CMS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostType extends Model
{
    use HasFactory;

    protected $table = 'post_types';
    protected $guarded = [];
    protected $casts = [
        'supports' => 'array'
    ];

    protected $with = ['post_type_translations'];

    public function getTranslation($field = '', $lang = false)
    {
        $lang = $lang == false ? App::getLocale() : $lang;
        $post_type_translation = $this->post_type_translations->where('lang', $lang)->first();
        return $post_type_translation != null ? $post_type_translation->$field : $this->$field;
    }

    public function post_type_translations()
    {
        return $this->hasMany(PostTypeTranslation::class);
    }
}
