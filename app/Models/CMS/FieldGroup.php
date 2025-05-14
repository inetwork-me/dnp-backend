<?php

namespace App\Models\CMS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FieldGroup extends Model
{
    protected $fillable = [
        'title',
        'position',
        'style',
        'instruction_placement',
        'label_placement',
        'description',
    ];

    public function fields()
    {
        return $this->hasMany(CustomField::class);
    }

    public function locationRules()
    {
        return $this->hasMany(FieldGroupLocationRule::class);
    }
}
