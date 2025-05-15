<?php

namespace App\Models\CMS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FieldGroupLocationRule extends Model
{
    use HasFactory;


    protected $table = 'field_group_rules';

    protected $guarded = [];

    public function fieldGroup()
    {
        return $this->belongsTo(FieldGroup::class);
    }
}
