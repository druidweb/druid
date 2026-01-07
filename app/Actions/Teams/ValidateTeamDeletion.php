<?php

declare(strict_types=1);

namespace App\Actions\Teams;

use App\Models\Team;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class ValidateTeamDeletion
{
  /**
   * Validate that the team can be deleted by the given user.
   */
  public function validate(mixed $user, mixed $team): void
  {
    Gate::forUser($user)->authorize('delete', $team);

    /** @var Team $team */
    if ($team->personal_team) {
      throw ValidationException::withMessages([
        'team' => __('You may not delete your personal team.'),
      ])->errorBag('deleteTeam');
    }
  }
}
