<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Permission extends SpatiePermission
{
  use HasFactory;

  protected $fillable = [
    'name',
    'guard_name',
    'section',
  ];

  /**
   * Get permissions grouped by section
   */
  public static function groupedBySection()
  {
    return self::all()->groupBy('section');
  }
}
