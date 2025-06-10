<?php
// app/Models/PostType.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostType extends Model
{
    protected $fillable = ['slug', 'label'];

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
    public function categories()
    {
        return $this->hasMany(PostTypeCategory::class);
    }
}
