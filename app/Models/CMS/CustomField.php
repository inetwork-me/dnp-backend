<?php

namespace App\Models\CMS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomField extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        // 'choices' => 'array',
        'options' => 'array',
    ];


    public function group()
    {
        return $this->belongsTo(FieldGroup::class, 'field_group_id');
    }
}
