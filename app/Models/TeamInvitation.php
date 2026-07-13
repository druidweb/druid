<?php

declare(strict_types=1);

namespace App\Models;

use App\Teams\Teams;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\WithoutIncrementing;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Zen\Snowflake\Concerns\HasSnowflakePrimary;

#[Fillable([
  'email',
  'role',
])]
#[WithoutIncrementing]
final class TeamInvitation extends Model
{
  /** @phpstan-ignore missingType.generics */
  use HasFactory;

  use HasSnowflakePrimary;

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
