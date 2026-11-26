<?php

declare(strict_types=1);

namespace App\Concerns;

use App\Models\Team;
use App\Models\User;
use App\Rules\OwnerRole;
use App\Teams\Role;
use App\Teams\Teams;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

trait HasTeams
{
  /**
   * Determine if the given team is the current team.
   */
  public function isCurrentTeam(mixed $team): bool
  {
    // Reads the column, not the currentTeam relation, which would switch a team-less user to their personal team.
    /** @var Team|null $team */
    return $team !== null && (string) $team->id === (string) $this->current_team_id;
  }

  /**
   * Get the current team of the user's context.
   *
   * @return BelongsTo<Team, $this>
   */
  public function currentTeam(): BelongsTo
  {
    if (is_null($this->current_team_id) && $this->id) {
      /** @var Team $personalTeam */
      $personalTeam = $this->personalTeam();
      $this->switchTeam($personalTeam);
    }

    /** @var class-string<Team> $teamModel */
    $teamModel = Teams::teamModel();

    /** @var BelongsTo<Team, $this> */
    return $this->belongsTo($teamModel, 'current_team_id');
  }

  /**
   * Switch the user's context to the given team.
   */
  public function switchTeam(mixed $team): bool
  {
    if (! $this->belongsToTeam($team)) {
      return false;
    }

    /** @var Team $team */
    $this->forceFill([
      'current_team_id' => $team->id,
    ])->save();

    $this->setRelation('currentTeam', $team);

    return true;
  }

  /**
   * Get all of the teams the user owns or belongs to.
   *
   * @return Collection<int, Team>
   */
  public function allTeams(): Collection
  {
    return $this->ownedTeams->merge($this->teams)->sortBy('name');
  }

  /**
   * Get all of the teams the user owns.
   *
   * @return HasMany<Team, $this>
   */
  public function ownedTeams(): HasMany
  {
    /** @var class-string<Team> $teamModel */
    $teamModel = Teams::teamModel();

    /** @var HasMany<Team, $this> */
    return $this->hasMany($teamModel);
  }

  /**
   * Get all of the teams the user belongs to.
   *
   * @return BelongsToMany<Team, $this>
   */
  public function teams(): BelongsToMany
  {
    /** @var class-string<Team> $teamModel */
    $teamModel = Teams::teamModel();

    /** @var BelongsToMany<Team, $this> */
    return $this->belongsToMany($teamModel, Teams::membershipModel())
      ->withPivot('role')
      ->withTimestamps()
      ->as('membership');
  }

  /**
   * Get the user's "personal" team.
   */
  public function personalTeam(): ?Team
  {
    /** @var Team|null */
    return $this->ownedTeams->where('personal_team', true)->first();
  }

  /**
   * Determine if the user owns the given team.
   */
  public function ownsTeam(mixed $team): bool
  {
    /** @var Team|null $team */
    return $team !== null && $this->id == $team->{$this->getForeignKey()};
  }

  /**
   * Determine if the user belongs to the given team.
   */
  public function belongsToTeam(mixed $team): bool
  {
    /** @var Team|null $team */
    return $this->ownsTeam($team) || ($team !== null && $this->teams->contains(fn ($t): bool => $t->id === $team->id));
  }

  /**
   * Get the role that the user has on the team.
   */
  public function teamRole(mixed $team): OwnerRole|null|Role
  {
    if ($this->ownsTeam($team)) {
      return new OwnerRole;
    }

    // Non-members (and a null team) have no membership pivot, so they fall through to null.
    /** @var Team|null $team */
    /** @var object{role: string|null}|null $membership */
    $membership = $team?->users->firstWhere('id', $this->id)?->membership; // @phpstan-ignore property.notFound

    return $membership?->role ? Teams::findRole($membership->role) : null;
  }

  /**
   * Determine if the user has the given role on the given team.
   */
  public function hasTeamRole(mixed $team, string $role): bool
  {
    // Owners hold every role.
    return $this->ownsTeam($team) || $this->teamRole($team)?->key === $role;
  }

  /**
   * Get the user's permissions for the given team.
   *
   * @return array<int, string>
   */
  public function teamPermissions(mixed $team): array
  {
    return (array) $this->teamRole($team)?->permissions;
  }

  /**
   * Determine if the user has the given permission on the given team.
   */
  public function hasTeamPermission(mixed $team, string $permission): bool
  {
    // Owners resolve to OwnerRole ('*') via teamPermissions(), so an owner's API token is still bound by its abilities.
    if (! $this->belongsToTeam($team)) {
      return false;
    }

    if (
      $this->currentAccessToken() !== null && // @phpstan-ignore notIdentical.alwaysTrue
      ! $this->tokenCan($permission)
    ) {
      return false;
    }

    $permissions = $this->teamPermissions($team);

    return in_array($permission, $permissions, true) || in_array('*', $permissions, true);
  }

  // A role may only be granted by someone who holds all of its permissions. Owners hold '*' through OwnerRole, but this
  // still runs through hasTeamPermission() so an owner's scoped API token can't grant more than the token allows.
  public function canAssignTeamRole(mixed $team, string $role): bool
  {
    $permissions = Teams::findRole($role)->permissions ?? ['*'];

    return array_all($permissions, fn (string $permission): bool => $this->hasTeamPermission($team, $permission));
  }

  // Only other non-owner members are manageable, and only when they hold no permission the user (or their token) lacks.
  public function canManageTeamMember(mixed $team, User $member): bool
  {
    if ((string) $member->id === (string) $this->id || $member->ownsTeam($team) || ! $member->belongsToTeam($team)) {
      return false;
    }

    return array_all($member->teamPermissions($team), fn (string $permission): bool => $this->hasTeamPermission($team, $permission));
  }
}
