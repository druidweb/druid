<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class EmailVerificationNotificationController extends Controller
{
  /**
   * Send a new email verification notification.
   */
  public function store(Request $request): RedirectResponse
  {
    $user = $request->user();

    // @codeCoverageIgnoreStart
    if ($user === null) {
      return redirect()->route('login');
    }
    // @codeCoverageIgnoreEnd

    if ($user->hasVerifiedEmail()) {
      return redirect()->intended(route('dashboard', absolute: false));
    }

    $user->sendEmailVerificationNotification();

    return back()->with('status', 'verification-link-sent');
  }
}
