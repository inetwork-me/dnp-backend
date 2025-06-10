<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $fillable = ['filename', 'path', 'mime_type', 'size', 'metadata', 'folder_id'];
    protected $casts = ['metadata' => 'array'];

    public function folder()
    {
        return $this->belongsTo(MediaFolder::class, 'folder_id');
    }
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}
