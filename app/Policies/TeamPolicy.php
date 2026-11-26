<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Team;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

// Team management maps onto the role permissions (create/read/update/delete). Owners hold '*' via OwnerRole.
// Escalation guards for specific members and roles live in HasTeams::canManageTeamMember()/canAssignTeamRole().
final class TeamPolicy
{
  use HandlesAuthorization;

  public function viewAny(): bool
  {
    return true;
  }

  public function view(User $user, Team $team): bool
  {
    return $team->hasUser($user);
  }

  public function create(): bool
  {
    return true;
  }

  public function update(User $user, Team $team): bool
  {
    return $team->userHasPermission($user, 'update');
  }

  public function addTeamMember(User $user, Team $team): bool
  {
    return $team->userHasPermission($user, 'create');
  }

  public function updateTeamMember(User $user, Team $team): bool
  {
    return $team->userHasPermission($user, 'update');
  }

  public function removeTeamMember(User $user, Team $team): bool
  {
    return $team->userHasPermission($user, 'delete');
  }

  // Deleting a team stays owner-only regardless of role, and an owner's API token still needs the delete ability.
  public function delete(User $user, Team $team): bool
  {
    return $user->ownsTeam($team) && $team->userHasPermission($user, 'delete');
  }
}
