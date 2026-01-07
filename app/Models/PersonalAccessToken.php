<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;
use Zen\Snowflake\Concerns\HasSnowflakePrimary;

final class PersonalAccessToken extends SanctumPersonalAccessToken
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
}
