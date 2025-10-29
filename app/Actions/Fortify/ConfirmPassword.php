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
    /** @var StatefulGuard $guard */
    $guard = Auth::guard(config('fortify.guard'));

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

