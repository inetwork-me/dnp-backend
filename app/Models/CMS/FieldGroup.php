<?php

namespace App\Models\CMS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FieldGroup extends Model
{
    protected $guarded = [];

    public function customFields()
    {
        return $this->hasMany(CustomField::class);
    }

    public function locationRules()
    {
        return $this->hasMany(FieldGroupLocationRule::class);
    }
}
