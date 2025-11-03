<?php

namespace App\Http\Controllers\Teams;

use App\Teams\Teams;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Routing\Redirector;

class CurrentTeamController extends Controller
{
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
