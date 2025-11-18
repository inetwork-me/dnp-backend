<?php
// app/Models/Block.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Block extends Model
{
    protected $fillable = [
        'name',
        'label',
        'icon',
        'fields',
        'settings',
    ];

    protected $casts = [
        'label'    => 'array',
        'fields'   => 'array',
        'settings' => 'array',
    ];
}
