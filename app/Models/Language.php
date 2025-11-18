<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;


class Language extends Model
{
    protected $fillable = [
        'code',
        'name',
        'app_lang_code',
        'rtl',
        'status',
        'is_default',
    ];

    protected $casts = [
        'rtl'     => 'boolean',
        'status'  => 'boolean',
        'is_default' => 'boolean',
    ];
}
