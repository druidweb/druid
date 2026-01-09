<?php

declare(strict_types=1);

namespace App\Http\Controllers\Teams;

use App\Models\Team;
use App\Models\User;
use App\Teams\Teams;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Redirector;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

final class CurrentTeamController implements HasMiddleware
{
  /**
   * Get the middleware that should be assigned to the controller.
   *
   * @return array<int, Middleware|Closure|string>
   */
  public static function middleware(): array
  {
    return [
      static function (Request $request, callable $next): HttpResponse {
        abort_unless(Teams::hasTeamFeatures(), HttpResponse::HTTP_FORBIDDEN);

        /** @var HttpResponse $response */
        $response = $next($request);

        return $response;
      },
    ];
  }

  /**
   * Update the authenticated user's current team.
   */
  public function update(Request $request): Redirector|RedirectResponse
  {
    /** @var Team $team */
    $team = Teams::newTeamModel()->findOrFail($request->team_id);

    /** @var User $user */
    $user = $request->user();

    abort_unless($user->switchTeam($team), 403);

    /** @var string|null $home */
    $home = config('fortify.home');

    return redirect($home, 303);
  }
}
