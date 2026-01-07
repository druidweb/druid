<?php

declare(strict_types=1);

namespace App\Concerns;

use App\Models\Team;
use App\Rules\OwnerRole;
use App\Teams\Role;
use App\Teams\Teams;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

trait HasTeams
{
  /**
   * Determine if the given team is the current team.
   */
  public function isCurrentTeam(mixed $team): bool
  {
    /** @var Team $team */
    /** @var Team $currentTeam */
    $currentTeam = $this->currentTeam;

    return $team->id === $currentTeam->id;
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
    if (is_null($team)) {
      return false;
    }

    /** @var Team $team */
    return $this->id == $team->{$this->getForeignKey()};
  }

  /**
   * Determine if the user belongs to the given team.
   */
  public function belongsToTeam(mixed $team): bool
  {
    if (is_null($team)) {
      return false;
    }
    if ($this->ownsTeam($team)) {
      return true;
    }

    /** @var Team $team */
    return (bool) $this->teams->contains(fn ($t): bool => $t->id === $team->id);
  }

  /**
   * Get the role that the user has on the team.
   */
  public function teamRole(mixed $team): OwnerRole|null|Role
  {
    if ($this->ownsTeam($team)) {
      return new OwnerRole;
    }

    if (! $this->belongsToTeam($team)) {
      return null;
    }

    /** @var Team $team */
    /** @phpstan-ignore property.notFound */
    $membership = $team->users
      ->where('id', $this->id)
      ->first()
      ?->membership;

    if (! $membership) {
      return null;
    }

    /** @var object{role: string} $membership */
    return $membership->role ? Teams::findRole($membership->role) : null;
  }

  /**
   * Determine if the user has the given role on the given team.
   */
  public function hasTeamRole(mixed $team, string $role): bool
  {
    if ($this->ownsTeam($team)) {
      return true;
    }

    if (! $this->belongsToTeam($team)) {
      return false;
    }

    /** @var Team $team */
    /** @phpstan-ignore property.notFound */
    $membership = $team->users
      ->where('id', $this->id)
      ->first()
      ?->membership;

    if (! $membership) {
      return false;
    }

    /** @var object{role: string} $membership */
    return Teams::findRole($membership->role)?->key === $role;
  }

  /**
   * Get the user's permissions for the given team.
   *
   * @return array<int, string>
   */
  public function teamPermissions(mixed $team): array
  {
    if ($this->ownsTeam($team)) {
      return ['*'];
    }

    if (! $this->belongsToTeam($team)) {
      return [];
    }

    return (array) $this->teamRole($team)?->permissions;
  }

  /**
   * Determine if the user has the given permission on the given team.
   */
  public function hasTeamPermission(mixed $team, string $permission): bool
  {
    if ($this->ownsTeam($team)) {
      return true;
    }

    if (! $this->belongsToTeam($team)) {
      return false;
    }

    if (
      in_array(HasApiTokens::class, class_uses_recursive($this)) &&
      ! $this->tokenCan($permission) &&
      /** @phpstan-ignore notIdentical.alwaysTrue */
      $this->currentAccessToken() !== null
    ) {
      return false;
    }

    $permissions = $this->teamPermissions($team);

    return in_array($permission, $permissions) ||
           in_array('*', $permissions) ||
           (Str::endsWith($permission, ':create') && in_array('*:create', $permissions)) ||
           (Str::endsWith($permission, ':update') && in_array('*:update', $permissions));
  }
}
