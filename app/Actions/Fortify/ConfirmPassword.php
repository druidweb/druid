<?php

declare(strict_types=1);

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Support\Facades\Auth;

// Registered via Fortify::confirmPasswordsUsing(), so Fortify's confirm-password route and every
// controller that resolves Fortify's ConfirmPassword action delegate here.
final class ConfirmPassword
{
  public function __invoke(User $user, ?string $password = null): bool
  {
    if ($password === null) {
      return false;
    }

    /** @var string|null $guardName */
    $guardName = config('fortify.guard');

    /** @var StatefulGuard $guard */
    $guard = Auth::guard($guardName);

    return $guard->validate([
      'email' => $user->email,
      'password' => $password,
    ]);
  }
}
