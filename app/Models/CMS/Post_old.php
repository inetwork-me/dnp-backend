<?php

namespace App\Models\CMS;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post_old extends Model
{
    use HasFactory;

    protected $table = 'posts';
    protected $guarded = [];

    protected $with = ['post_translations'];

    public function getTranslation($field = '', $lang = false)
    {
        $lang = $lang == false ? App::getLocale() : $lang;
        $post_translation = $this->post_translations->where('lang', $lang)->first();
        return $post_translation != null ? $post_translation->$field : $this->$field;
    }

    public function post_translations()
    {
        return $this->hasMany(PostTranslation::class);
    }

    public function postType()
    {
        return $this->belongsTo(PostType::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
