<?php

declare(strict_types=1);

namespace App\Actions\Teams;

use App\Contracts\RemovesTeamMembers;
use App\Events\TeamMemberRemoved;
use App\Models\Team;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

final class RemoveTeamMember implements RemovesTeamMembers
{
  /**
   * Remove the team member from the given team.
   */
  public function remove(User $user, Team $team, User $teamMember): void
  {
    $this->authorize($user, $team, $teamMember);

    $this->ensureUserDoesNotOwnTeam($teamMember, $team);

    $team->removeUser($teamMember);

    event(new TeamMemberRemoved($team, $teamMember));
  }

  /**
   * Authorize that the user can remove the team member.
   */
  private function authorize(User $user, Team $team, User $teamMember): void
  {
    throw_if(! Gate::forUser($user)->check('removeTeamMember', $team) &&
        (string) $user->id !== (string) $teamMember->id, AuthorizationException::class);
  }

  /**
   * Ensure that the currently authenticated user does not own the team.
   */
  private function ensureUserDoesNotOwnTeam(User $teamMember, Team $team): void
  {
    /** @var User|null $owner */
    $owner = $team->owner;
    if ($owner && (string) $teamMember->id === (string) $owner->id) {
      throw ValidationException::withMessages([
        'team' => [__('You may not leave a team that you created.')],
      ])->errorBag('removeTeamMember');
    }
  }
}
