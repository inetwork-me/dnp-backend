<?php

namespace App\Models\CMS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostTypeTranslation extends Model
{
    use HasFactory;
    protected $fillable = [
        'plural_label',
        'singular_label',
        'menu_name',
        'description',
    ];

    public function posttype()
    {
        return $this->belongsTo(PostType::class);
    }
}
