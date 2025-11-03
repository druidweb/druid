<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Membership extends Pivot
{
  use HasFactory;

  /**
   * Indicates if the IDs are auto-incrementing.
   *
   * @var bool
   */
  public $incrementing = true;

  /**
   * The table associated with the pivot model.
   *
   * @var string
   */
  protected $table = 'team_user';
}
