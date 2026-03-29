<?php 

// app/Models/Menu.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model {
    protected $fillable = ['name', 'slug', 'is_default', 'items'];
    protected $casts = [
        'is_default' => 'boolean',
        'items' => 'array',
    ];
}
