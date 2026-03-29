<?php


// app/Models/FormField.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormField extends Model
{
    protected $fillable = [
        'form_id', 'label', 'name', 'type', 'required', 'supports_multilang',
        'multiple', 'conditional_logic', 'options', 'validation', 'order'
    ];
    protected $casts = [
        'label'             => 'array',
        'options'           => 'array',
        'validation'        => 'array',
        'conditional_logic' => 'array',
        'required'          => 'boolean',
        'supports_multilang'=> 'boolean',
        'multiple'          => 'boolean',
    ];

    public function form()
    {
        return $this->belongsTo(Form::class);
    }
}
