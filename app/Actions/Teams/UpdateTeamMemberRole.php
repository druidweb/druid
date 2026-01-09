<?php

declare(strict_types=1);

namespace App\Actions\Teams;

use App\Events\TeamMemberUpdated;
use App\Models\Team;
use App\Rules\Role;
use App\Teams\Teams;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

final class UpdateTeamMemberRole
{
  /**
   * Update the role for the given team member.
   */
  public function update(mixed $user, mixed $team, int $teamMemberId, string $role): void
  {
    Gate::forUser($user)->authorize('updateTeamMember', $team);

    Validator::make([
      'role' => $role,
    ], [
      'role' => ['required', 'string', new Role],
    ])->validate();

    /** @var Team $team */
    $team->users()->updateExistingPivot($teamMemberId, [
      'role' => $role,
    ]);

    /** @var Team $freshTeam */
    $freshTeam = $team->fresh();
    event(new TeamMemberUpdated($freshTeam, Teams::findUserByIdOrFail($teamMemberId)));
  }
}
