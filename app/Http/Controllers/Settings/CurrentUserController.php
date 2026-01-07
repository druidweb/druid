<?php

namespace App\Http\Controllers\Settings;

use App\Contracts\DeletesUsers;
use App\Teams\Teams;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Laravel\Fortify\Actions\ConfirmPassword;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class CurrentUserController implements HasMiddleware
{
  /**
   * Get the middleware that should be assigned to the controller.
   *
   * @return array<int, Middleware|string>
   */
  public static function middleware(): array
  {
    return [
      function (Request $request, callable $next): HttpResponse {
        if (! Teams::hasAccountDeletionFeatures()) {
          abort(HttpResponse::HTTP_FORBIDDEN);
        }

        return $next($request);
      },
    ];
  }

  /**
   * Delete the current user.
   *
   * @return Response
   */
  public function destroy(Request $request, StatefulGuard $guard)
  {
    $confirmed = app(ConfirmPassword::class)(
      $guard, $request->user(), $request->password
    );

    if (! $confirmed) {
      throw ValidationException::withMessages([
        'password' => __('The password is incorrect.'),
      ]);
    }

    app(DeletesUsers::class)->delete($request->user()->fresh());

    $guard->logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return Inertia::location(url('/'));
  }
}
