<?php

declare(strict_types=1);

namespace App\Models;

use App\Events\TeamCreated;
use App\Events\TeamDeleted;
use App\Events\TeamUpdated;
use App\Teams\Teams;
use Database\Factories\TeamFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Zen\Snowflake\Concerns\HasSnowflakePrimary;

final class Team extends Model
{
  /** @use HasFactory<TeamFactory> */
  use HasFactory;

  use HasSnowflakePrimary;

  /**
   * Indicates if the IDs are auto-incrementing.
   *
   * @var bool
   */
  public $incrementing = false;

  /**
   * The attributes that are mass assignable.
   *
   * @var list<string>
   */
  protected $fillable = [
    'name',
    'personal_team',
  ];

  /**
   * The event map for the model.
   *
   * @var array<string, class-string>
   */
  protected $dispatchesEvents = [
    'created' => TeamCreated::class,
    'updated' => TeamUpdated::class,
    'deleted' => TeamDeleted::class,
  ];

  /**
   * Get the attributes that should be cast.
   *
   * @return array<string, string>
   */
  protected function casts(): array
  {
    return [
      'personal_team' => 'boolean',
    ];
  }

  /**
   * Get the owner of the team.
   *
   * @return BelongsTo<User, $this>
   */
  public function owner(): BelongsTo
  {
    /** @var class-string<User> $userModel */
    $userModel = Teams::userModel();

    /** @var BelongsTo<User, $this> */
    return $this->belongsTo($userModel, 'user_id');
  }

  /**
   * Get all of the team's users including its owner.
   *
   * @return Collection<int, User>
   */
  public function allUsers(): Collection
  {
    /** @var User|null $owner */
    $owner = $this->owner;

    return $this->users->merge($owner ? [$owner] : []);
  }

  /**
   * Get all of the users that belong to the team.
   *
   * @return BelongsToMany<User, $this>
   */
  public function users(): BelongsToMany
  {
    /** @var class-string<User> $userModel */
    $userModel = Teams::userModel();

    /** @var BelongsToMany<User, $this> $relation */
    $relation = $this->belongsToMany($userModel, Teams::membershipModel())
      ->withPivot('role')
      ->withTimestamps()
      ->as('membership');

    return $relation;
  }

  /**
   * Determine if the given user belongs to the team.
   */
  public function hasUser(mixed $user): bool
  {
    if ($user instanceof User && $this->users->contains('id', $user->id)) {
      return true;
    }

    /** @var User $user */
    return $user->ownsTeam($this);
  }

  /**
   * Determine if the given email address belongs to a user on the team.
   */
  public function hasUserWithEmail(string $email): bool
  {
    return $this->allUsers()->contains(fn (mixed $user): bool => $user->email === $email);
  }

  /**
   * Determine if the given user has the given permission on the team.
   */
  public function userHasPermission(mixed $user, string $permission): bool
  {
    /** @var User $user */
    return $user->hasTeamPermission($this, $permission);
  }

  /**
   * Get all of the pending user invitations for the team.
   *
   * @return HasMany<TeamInvitation, $this>
   */
  public function teamInvitations(): HasMany
  {
    /** @var class-string<TeamInvitation> $model */
    $model = Teams::teamInvitationModel();

    /** @var HasMany<TeamInvitation, $this> $relation */
    $relation = $this->hasMany($model);

    return $relation;
  }

  /**
   * Remove the given user from the team.
   */
  public function removeUser(mixed $user): void
  {
    /** @var User $user */
    if ($user->current_team_id === $this->id) {
      $user->forceFill([
        'current_team_id' => null,
      ])->save();
    }

    $this->users()->detach($user);
  }

  /**
   * Purge all of the team's resources.
   */
  public function purge(): void
  {
    $this->owner()->where('current_team_id', $this->id)
      ->update(['current_team_id' => null]);

    $this->users()->where('current_team_id', $this->id)
      ->update(['current_team_id' => null]);

    $this->users()->detach();

    $this->delete();
  }
}
