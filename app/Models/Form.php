<?php


// app/Models/Form.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    protected $fillable = ['slug', 'label', 'settings'];
    protected $casts = [
        'label'    => 'array',    // { en: "...", ar: "..." }

        'settings' => 'array',
    ];

    public function fields()
    {
        return $this->hasMany(FormField::class)->orderBy('order');
    }

    public function submissions()
    {
        return $this->hasMany(FormSubmission::class);
    }

    // public function getRouteKeyName(): string
    // {
    //     return 'slug';
    // }
}
