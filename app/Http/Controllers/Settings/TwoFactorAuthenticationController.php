<?php

declare(strict_types=1);

namespace App\Http\Controllers\Settings;

use App\Http\Requests\Settings\TwoFactorAuthenticationRequest;
use App\Models\User;
use Illuminate\Routing\Controllers\HasMiddleware;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Fortify\Features;
use Spatie\RouteDiscovery\Attributes\Route;

final class TwoFactorAuthenticationController implements HasMiddleware
{
  /**
   * Get the middleware that should be assigned to the controller.
   */
  public static function middleware(): array
  {
    return [];
  }

  /**
   * Show the user's two-factor authentication settings page.
   */
  #[Route('settings/two-factor', name: 'two-factor.show', middleware: ['auth'])]
  public function show(TwoFactorAuthenticationRequest $request): Response
  {
    $request->ensureStateIsValid();

    /** @var User $user */
    $user = $request->user();

    // Set the intended URL for password confirmation redirect
    if (! $request->session()->has('auth.password_confirmed_at')) {
      $request->session()->put('url.intended', $request->url());
    }

    return Inertia::render('settings/TwoFactor', [
      'twoFactorEnabled' => $user->hasEnabledTwoFactorAuthentication(),
      'requiresConfirmation' => Features::optionEnabled(Features::twoFactorAuthentication(), 'confirm'),
      'passwordConfirmed' => $request->session()->has('auth.password_confirmed_at'),
    ]);
  }
}
