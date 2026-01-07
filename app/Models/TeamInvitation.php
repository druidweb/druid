<?php

namespace App\Models;

use App\Teams\Teams;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Zen\Snowflake\Concerns\HasSnowflakePrimary;

class TeamInvitation extends Model
{
  use HasFactory;
  use HasSnowflakePrimary;

  /**
   * Indicates if the IDs are auto-incrementing.
   *
   * @var bool
   */
  public $incrementing = false;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'email',
    'role',
  ];

  /**
   * Get the team that the invitation belongs to.
   *
   * @return BelongsTo
   */
  public function team()
  {
    return $this->belongsTo(Teams::teamModel());
  }
}
