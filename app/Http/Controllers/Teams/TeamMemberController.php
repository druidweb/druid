<?php

namespace App\Http\Controllers\Teams;

use App\Actions\UpdateTeamMemberRole;
use App\Contracts\AddsTeamMembers;
use App\Contracts\InvitesTeamMembers;
use App\Contracts\RemovesTeamMembers;
use App\Teams\Features;
use App\Teams\Teams;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Routing\Redirector;

class TeamMemberController extends Controller
{
  /**
   * Add a new team member to a team.
   *
   * @param  int  $teamId
   */
  public function store(Request $request, $teamId): RedirectResponse
  {
    $team = Teams::newTeamModel()->findOrFail($teamId);

    if (Features::sendsTeamInvitations()) {
      app(InvitesTeamMembers::class)->invite(
        $request->user(),
        $team,
        $request->email ?: '',
        $request->role
      );
    } else {
      app(AddsTeamMembers::class)->add(
        $request->user(),
        $team,
        $request->email ?: '',
        $request->role
      );
    }

    return back(303);
  }

  /**
   * Update the given team member's role.
   *
   * @param  int  $teamId
   * @param  int  $userId
   */
  public function update(Request $request, $teamId, $userId): RedirectResponse
  {
    app(UpdateTeamMemberRole::class)->update(
      $request->user(),
      Teams::newTeamModel()->findOrFail($teamId),
      $userId,
      $request->role
    );

    return back(303);
  }

  /**
   * Remove the given user from the given team.
   *
   * @param  int  $teamId
   * @param  int  $userId
   * @return RedirectResponse
   */
  public function destroy(Request $request, $teamId, $userId): Redirector|RedirectResponse
  {
    $team = Teams::newTeamModel()->findOrFail($teamId);

    app(RemovesTeamMembers::class)->remove(
      $request->user(),
      $team,
      $user = Teams::findUserByIdOrFail($userId)
    );

    if ($request->user()->id === $user->id) {
      return redirect(config('fortify.home'));
    }

    return back(303);
  }
}
