<?php

namespace App\Models\CMS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FieldGroupLocationRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'field_group_id',
        'rule_key',     // e.g. post_type
        'operator',     // e.g. is_equal_to
        'value',        // e.g. service or page
        'group',        // rule group (to group by "OR")
    ];

    public function fieldGroup()
    {
        return $this->belongsTo(FieldGroup::class);
    }
}
