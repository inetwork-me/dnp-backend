<?php

namespace App\Models\CMS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostTranslation extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'excerpt',
        'content',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
