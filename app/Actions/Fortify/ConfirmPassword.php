<?php

declare(strict_types=1);

namespace App\Actions\Fortify;

use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Support\Facades\Auth;

class ConfirmPassword
{
  /**
   * Confirm that the given password is valid for the given user.
   */
  public function __invoke(?string $password = null): bool
  {
    /** @var string|null $guardName */
    $guardName = config('fortify.guard');

    /** @var StatefulGuard $guard */
    $guard = Auth::guard($guardName);

    $user = $guard->user();

    if (! $user) {
      return false;
    }

    return $guard->validate([
      'email' => $user->email,
      'password' => $password,
    ]);
  }
}
