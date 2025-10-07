<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    use HasFactory;

    protected $casts = [
        'name' => 'array',
    ];

    public function country(){
        return $this->belongsTo(Country::class);
    }

    public function cities(){
        return $this->hasMany(City::class);
    }

    public function getName($locale = 'en')
    {
        if (is_array($this->name)) {
            return $this->name[$locale] ?? $this->name['en'] ?? '';
        }
        return $this->name ?? '';
    }
}
