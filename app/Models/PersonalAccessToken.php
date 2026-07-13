<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\WithoutIncrementing;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;
use Zen\Snowflake\Concerns\HasSnowflakePrimary;

#[WithoutIncrementing]
final class PersonalAccessToken extends SanctumPersonalAccessToken
{
  /** @phpstan-ignore missingType.generics */
  use HasFactory;

  use HasSnowflakePrimary;
}
