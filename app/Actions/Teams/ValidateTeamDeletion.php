<?php

namespace App\Actions\Teams;

use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class ValidateTeamDeletion
{
  /**
   * Validate that the team can be deleted by the given user.
   *
   * @param  mixed  $user
   * @param  mixed  $team
   */
  public function validate($user, $team): void
  {
    Gate::forUser($user)->authorize('delete', $team);

    if ($team->personal_team) {
      throw ValidationException::withMessages([
        'team' => __('You may not delete your personal team.'),
      ])->errorBag('deleteTeam');
    }
  }
}
