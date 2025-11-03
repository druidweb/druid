<?php

namespace App\Actions\Teams;

use App\Events\TeamMemberUpdated;
use App\Rules\Role;
use App\Teams\Teams;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class UpdateTeamMemberRole
{
  /**
   * Update the role for the given team member.
   *
   * @param  mixed  $user
   * @param  mixed  $team
   * @param  int  $teamMemberId
   */
  public function update($user, $team, $teamMemberId, string $role): void
  {
    Gate::forUser($user)->authorize('updateTeamMember', $team);

    Validator::make([
      'role' => $role,
    ], [
      'role' => ['required', 'string', new Role],
    ])->validate();

    $team->users()->updateExistingPivot($teamMemberId, [
      'role' => $role,
    ]);

    event(new TeamMemberUpdated($team->fresh(), Teams::findUserByIdOrFail($teamMemberId)));
  }
}
