<?php

declare(strict_types=1);

namespace App\Actions\Teams;

use App\Events\TeamMemberUpdated;
use App\Models\Team;
use App\Models\User;
use App\Rules\Role;
use App\Teams\Teams;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

final class UpdateTeamMemberRole
{
  public function update(User $user, Team $team, int $teamMemberId, string $role): void
  {
    Gate::forUser($user)->authorize('updateTeamMember', $team);

    Validator::make([
      'role' => $role,
    ], [
      'role' => ['required', 'string', new Role],
    ])->validate();

    /** @var User $teamMember */
    $teamMember = Teams::findUserByIdOrFail($teamMemberId);

    // Blocks self-promotion, touching the owner, and acting on members who outrank the user.
    throw_unless($user->canManageTeamMember($team, $teamMember), AuthorizationException::class);

    throw_unless($user->canAssignTeamRole($team, $role), ValidationException::withMessages([
      'role' => __('You may not assign a role with more permissions than your own.'),
    ]));

    // Re-selecting the member's current role changes nothing, so don't write or announce an update.
    if ($teamMember->hasTeamRole($team, $role)) {
      return;
    }

    $team->users()->updateExistingPivot($teamMemberId, [
      'role' => $role,
    ]);

    /** @var Team $freshTeam */
    $freshTeam = $team->fresh();
    event(new TeamMemberUpdated($freshTeam, $teamMember));
  }
}
