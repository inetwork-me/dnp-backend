<?php

namespace App\Models\CMS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomField extends Model
{
    use HasFactory;

    protected $fillable = [
        'field_group_id',
        'name',
        'label',
        'type',
        'required',
        'default_value',
        'options',
        'order',
    ];

    public function group()
    {
        return $this->belongsTo(FieldGroup::class, 'field_group_id');
    }
}
