<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\HasProfilePhoto;
use App\Concerns\HasTeams;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;
use Zen\Snowflake\Concerns\HasSnowflakePrimary;

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
  use TwoFactorAuthenticatable;

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
    'name',
    'email',
    'password',
  ];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var list<string>
   */
  protected $hidden = [
    'password',
    'two_factor_secret',
    'two_factor_recovery_codes',
    'remember_token',
  ];

  /**
   * The accessors to append to the model's array form.
   *
   * @var list<string>
   */
  protected $appends = [
    'profile_photo_url',
  ];

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
      'two_factor_confirmed_at' => 'datetime',
    ];
  }
}
