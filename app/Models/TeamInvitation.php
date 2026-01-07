<?php

namespace App\Models;

use App\Teams\Teams;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Zen\Snowflake\Concerns\HasSnowflakePrimary;

class TeamInvitation extends Model
{
  /** @phpstan-ignore missingType.generics */
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
   * @var list<string>
   */
  protected $fillable = [
    'email',
    'role',
  ];

  /**
   * Get the team that the invitation belongs to.
   *
   * @return BelongsTo<Team, $this>
   */
  public function team(): BelongsTo
  {
    /** @var class-string<Team> $teamModel */
    $teamModel = Teams::teamModel();

    /** @var BelongsTo<Team, $this> */
    return $this->belongsTo($teamModel);
  }
}
