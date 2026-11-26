<?php

declare(strict_types=1);

namespace App\Http\Controllers\Settings;

use App\Teams\Agent;
use Illuminate\Auth\SessionGuard;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Fortify\Actions\ConfirmPassword;

final class OtherBrowserSessionsController
{
  /**
   * Show the browser sessions page.
   */
  public function index(Request $request): Response
  {
    $agent = new Agent;
    $agent->setUserAgent((string) $request->userAgent());

    return Inertia::render('settings/Sessions', [
      'device' => [
        'platform' => $agent->platform() ?? __('base.sessions.unknown'),
        'browser' => $agent->browser() ?? __('base.sessions.unknown'),
        'is_desktop' => $agent->isDesktop(),
        'ip_address' => $request->ip(),
      ],
    ]);
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
