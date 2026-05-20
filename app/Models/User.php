<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\HasProfilePhoto;
use App\Concerns\HasTeams;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Appends;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
/* @chisel-two-factor */
use Laravel\Fortify\TwoFactorAuthenticatable;
/* @end-chisel-two-factor */
use Laravel\Sanctum\HasApiTokens;
use Zen\Snowflake\Concerns\HasSnowflakePrimary;

#[Appends([
  'profile_photo_url',
])]
#[Fillable([
  'name',
  'email',
  'password',
])]
#[Hidden([
  'password',
  /* @chisel-two-factor */
  'two_factor_secret',
  'two_factor_recovery_codes',
  /* @end-chisel-two-factor */
  'remember_token',
])]
final class User extends Authenticatable implements MustVerifyEmail
{
  use HasApiTokens;

  /** @use HasFactory<UserFactory> */
  use HasFactory;

  use HasProfilePhoto;
  use HasSnowflakePrimary;
  use HasTeams;
  use Notifiable;
  use SoftDeletes;

  /* @chisel-two-factor */
  use TwoFactorAuthenticatable;
  /* @end-chisel-two-factor */

  /**
   * Indicates if the IDs are auto-incrementing.
   *
   * @var bool
   */
  public $incrementing = false;

  /**
   * Get the attributes that should be cast.
   *
   * @return array<string, string>
   */
  protected function casts(): array
  {
    return [
      'email_verified_at' => 'datetime',
      'password' => 'hashed',
      /* @chisel-two-factor */
      'two_factor_confirmed_at' => 'datetime',
      /* @end-chisel-two-factor */
    ];
  }
}
