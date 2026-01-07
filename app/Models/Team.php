<?php

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

class Team extends Model
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
   * @var array<int, string>
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
   * @return BelongsTo
   */
  public function owner()
  {
    return $this->belongsTo(Teams::userModel(), 'user_id');
  }

  /**
   * Get all of the team's users including its owner.
   *
   * @return Collection
   */
  public function allUsers()
  {
    return $this->users->merge([$this->owner]);
  }

  /**
   * Get all of the users that belong to the team.
   *
   * @return BelongsToMany
   */
  public function users()
  {
    return $this->belongsToMany(Teams::userModel(), Teams::membershipModel())
      ->withPivot('role')
      ->withTimestamps()
      ->as('membership');
  }

  /**
   * Determine if the given user belongs to the team.
   *
   * @param  User  $user
   */
  public function hasUser($user): bool
  {
    if ($this->users->contains($user)) {
      return true;
    }

    return $user->ownsTeam($this);
  }

  /**
   * Determine if the given email address belongs to a user on the team.
   *
   * @return bool
   */
  public function hasUserWithEmail(string $email)
  {
    return $this->allUsers()->contains(fn ($user): bool => $user->email === $email);
  }

  /**
   * Determine if the given user has the given permission on the team.
   *
   * @param  User  $user
   * @return bool
   */
  public function userHasPermission($user, string $permission)
  {
    return $user->hasTeamPermission($this, $permission);
  }

  /**
   * Get all of the pending user invitations for the team.
   *
   * @return HasMany
   */
  public function teamInvitations()
  {
    return $this->hasMany(Teams::teamInvitationModel());
  }

  /**
   * Remove the given user from the team.
   *
   * @param  User  $user
   */
  public function removeUser($user): void
  {
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
