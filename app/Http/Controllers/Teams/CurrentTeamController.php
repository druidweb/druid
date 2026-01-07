<?php

namespace App\Http\Controllers\Teams;

use App\Teams\Teams;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Redirector;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class CurrentTeamController implements HasMiddleware
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
        if (! Teams::hasTeamFeatures()) {
          abort(HttpResponse::HTTP_FORBIDDEN);
        }

        return $next($request);
      },
    ];
  }

  /**
   * Update the authenticated user's current team.
   *
   * @return RedirectResponse
   */
  public function update(Request $request): Redirector|RedirectResponse
  {
    $team = Teams::newTeamModel()->findOrFail($request->team_id);

    abort_unless($request->user()->switchTeam($team), 403);

    return redirect(config('fortify.home'), 303);
  }
}
