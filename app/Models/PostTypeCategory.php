<?php
// app/Models/PostTypeCategory.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostTypeCategory extends Model
{
    protected $casts = ['name' => 'array'];

    public function postType()
    {
        return $this->belongsTo(PostType::class);
    }
}
