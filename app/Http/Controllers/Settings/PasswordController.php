<?php

declare(strict_types=1);

namespace App\Http\Controllers\Settings;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\RouteDiscovery\Attributes\Route;

class PasswordController
{
  /**
   * Show the user's password settings page.
   */
  #[Route('settings/password', name: 'user-password.edit', middleware: ['auth'])]
  public function edit(): Response
  {
    return Inertia::render('settings/Password');
  }

  /**
   * Update the user's password.
   */
  #[Route('settings/password', name: 'user-password.update', middleware: ['auth', 'throttle:6,1'])]
  public function update(Request $request): RedirectResponse
  {
    /** @var array{current_password: string, password: string} $validated */
    $validated = $request->validate([
      'current_password' => ['required', 'current_password'],
      'password' => ['required', Password::defaults(), 'confirmed'],
    ]);

    /** @var User $user */
    $user = $request->user();
    $user->update([
      'password' => $validated['password'],
    ]);

    return back();
  }
}
