<?php

declare(strict_types=1);

namespace App\Http\Controllers\Settings;

use Illuminate\Auth\SessionGuard;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Fortify\Actions\ConfirmPassword;

class OtherBrowserSessionsController
{
  /**
   * Show the browser sessions page.
   */
  public function index(): Response
  {
    return Inertia::render('settings/Sessions');
  }

  /**
   * Log out from other browser sessions.
   */
  public function destroy(Request $request, StatefulGuard $guard): RedirectResponse
  {
    /** @var ConfirmPassword $confirmPassword */
    $confirmPassword = resolve(ConfirmPassword::class);

    /** @var string|null $password */
    $password = $request->password;

    $confirmed = $confirmPassword(
      $guard, $request->user(), $password
    );

    if (! $confirmed) {
      throw ValidationException::withMessages([
        'password' => __('The password is incorrect.'),
      ]);
    }

    /** @var SessionGuard $guard */
    $guard->logoutOtherDevices((string) $password);

    return back(303);
  }
}
