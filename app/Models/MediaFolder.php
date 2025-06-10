<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaFolder extends Model
{
    protected $fillable = ['name', 'parent_id', 'order'];

    public function parent()
    {
        return $this->belongsTo(MediaFolder::class, 'parent_id');
    }
    public function children()
    {
        return $this->hasMany(MediaFolder::class, 'parent_id')->orderBy('order');
    }
    public function media()
    {
        return $this->hasMany(Media::class, 'folder_id');
    }
}
