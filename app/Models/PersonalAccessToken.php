<?php

declare(strict_types=1);

namespace App\Models;

use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;
use Zen\Snowflake\Concerns\HasSnowflakePrimary;

final class PersonalAccessToken extends SanctumPersonalAccessToken
{
  use HasSnowflakePrimary;

  /**
   * Indicates if the IDs are auto-incrementing.
   *
   * @var bool
   */
  public $incrementing = false;
}
