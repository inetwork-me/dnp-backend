<?php
// app/Models/BmiRecord.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BmiRecord extends Model
{
    protected $fillable = [
        'gender', 'age', 'weight', 'height', 'activity',
        'bmi', 'bmi_category', 'bmr', 'tee', 'calories', 'water_intake_l',
    ];
}
