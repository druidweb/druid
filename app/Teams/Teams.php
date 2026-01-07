<?php

namespace App\Teams;

use App\Concerns\HasTeams;
use App\Contracts\AddsTeamMembers;
use App\Contracts\CreatesTeams;
use App\Contracts\DeletesTeams;
use App\Contracts\DeletesUsers;
use App\Contracts\InvitesTeamMembers;
use App\Contracts\RemovesTeamMembers;
use App\Contracts\UpdatesTeamNames;
use App\Models\Membership;
use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\User;
use Illuminate\Support\Arr;

class Teams
{
  /**
   * Indicates if Teams routes will be registered.
   *
   * @var bool
   */
  public static $registersRoutes = true;

  /**
   * The roles that are available to assign to users.
   *
   * @var array
   */
  public static $roles = [];

  /**
   * The permissions that exist within the application.
   *
   * @var array
   */
  public static $permissions = [];

  /**
   * The default permissions that should be available to new entities.
   *
   * @var array
   */
  public static $defaultPermissions = [];

  /**
   * The user model that should be used by Teams.
   *
   * @var string
   */
  public static $userModel = User::class;

  /**
   * The team model that should be used by Teams.
   *
   * @var string
   */
  public static $teamModel = Team::class;

  /**
   * The membership model that should be used by Teams.
   *
   * @var string
   */
  public static $membershipModel = Membership::class;

  /**
   * The team invitation model that should be used by Teams.
   *
   * @var string
   */
  public static $teamInvitationModel = TeamInvitation::class;

  /**
   * Determine if Teams has registered roles.
   */
  public static function hasRoles(): bool
  {
    return count(static::$roles) > 0;
  }

  /**
   * Find the role with the given key.
   *
   * @return Role|null
   */
  public static function findRole(string $key)
  {
    return static::$roles[$key] ?? null;
  }

  /**
   * Define a role.
   *
   * @return Role
   */
  public static function role(string $key, string $name, array $permissions)
  {
    static::$permissions = collect(array_merge(static::$permissions, $permissions))
      ->unique()
      ->sort()
      ->values()
      ->all();

    return tap(new Role($key, $name, $permissions), function ($role) use ($key): void {
      static::$roles[$key] = $role;
    });
  }

  /**
   * Determine if any permissions have been registered with Teams.
   */
  public static function hasPermissions(): bool
  {
    return count(static::$permissions) > 0;
  }

  /**
   * Define the available API token permissions.
   */
  public static function permissions(array $permissions): static
  {
    static::$permissions = $permissions;

    return new static;
  }

  /**
   * Define the default permissions that should be available to new API tokens.
   */
  public static function defaultApiTokenPermissions(array $permissions): static
  {
    static::$defaultPermissions = $permissions;

    return new static;
  }

  /**
   * Return the permissions in the given list that are actually defined permissions for the application.
   */
  public static function validPermissions(array $permissions): array
  {
    return array_values(array_intersect($permissions, static::$permissions));
  }

  /**
   * Determine if Teams is managing profile photos.
   *
   * @return bool
   */
  public static function managesProfilePhotos()
  {
    return Features::managesProfilePhotos();
  }

  /**
   * Determine if Teams is supporting API features.
   *
   * @return bool
   */
  public static function hasApiFeatures()
  {
    return Features::hasApiFeatures();
  }

  /**
   * Determine if Teams is supporting team features.
   *
   * @return bool
   */
  public static function hasTeamFeatures()
  {
    return Features::hasTeamFeatures();
  }

  /**
   * Determine if a given user model utilizes the "HasTeams" trait.
   *
   * @param  \Illuminate\Database\Eloquent\Model
   */
  public static function userHasTeamFeatures($user): bool
  {
    return (array_key_exists(HasTeams::class, class_uses_recursive($user)) ||
            method_exists($user, 'currentTeam')) &&
            static::hasTeamFeatures();
  }

  /**
   * Determine if the application is using the terms confirmation feature.
   *
   * @return bool
   */
  public static function hasTermsAndPrivacyPolicyFeature()
  {
    return Features::hasTermsAndPrivacyPolicyFeature();
  }

  /**
   * Determine if the application is using any account deletion features.
   *
   * @return bool
   */
  public static function hasAccountDeletionFeatures()
  {
    return Features::hasAccountDeletionFeatures();
  }

  /**
   * Find a user instance by the given ID.
   *
   * @param  int  $id
   * @return mixed
   */
  public static function findUserByIdOrFail($id)
  {
    return static::newUserModel()->where('id', $id)->firstOrFail();
  }

  /**
   * Find a user instance by the given email address or fail.
   *
   * @return mixed
   */
  public static function findUserByEmailOrFail(string $email)
  {
    return static::newUserModel()->where('email', $email)->firstOrFail();
  }

  /**
   * Get the name of the user model used by the application.
   *
   * @return string
   */
  public static function userModel()
  {
    return static::$userModel;
  }

  /**
   * Get a new instance of the user model.
   *
   * @return mixed
   */
  public static function newUserModel()
  {
    $model = static::userModel();

    return new $model;
  }

  /**
   * Specify the user model that should be used by Teams.
   */
  public static function useUserModel(string $model): static
  {
    static::$userModel = $model;

    return new static;
  }

  /**
   * Get the name of the team model used by the application.
   *
   * @return string
   */
  public static function teamModel()
  {
    return static::$teamModel;
  }

  /**
   * Get a new instance of the team model.
   *
   * @return mixed
   */
  public static function newTeamModel()
  {
    $model = static::teamModel();

    return new $model;
  }

  /**
   * Specify the team model that should be used by Teams.
   */
  public static function useTeamModel(string $model): static
  {
    static::$teamModel = $model;

    return new static;
  }

  /**
   * Get the name of the membership model used by the application.
   *
   * @return string
   */
  public static function membershipModel()
  {
    return static::$membershipModel;
  }

  /**
   * Specify the membership model that should be used by Teams.
   */
  public static function useMembershipModel(string $model): static
  {
    static::$membershipModel = $model;

    return new static;
  }

  /**
   * Get the name of the team invitation model used by the application.
   *
   * @return string
   */
  public static function teamInvitationModel()
  {
    return static::$teamInvitationModel;
  }

  /**
   * Specify the team invitation model that should be used by Teams.
   */
  public static function useTeamInvitationModel(string $model): static
  {
    static::$teamInvitationModel = $model;

    return new static;
  }

  /**
   * Register a class / callback that should be used to create teams.
   *
   * @return void
   */
  public static function createTeamsUsing(string $class)
  {
    return app()->singleton(CreatesTeams::class, $class);
  }

  /**
   * Register a class / callback that should be used to update team names.
   *
   * @return void
   */
  public static function updateTeamNamesUsing(string $class)
  {
    return app()->singleton(UpdatesTeamNames::class, $class);
  }

  /**
   * Register a class / callback that should be used to add team members.
   *
   * @return void
   */
  public static function addTeamMembersUsing(string $class)
  {
    return app()->singleton(AddsTeamMembers::class, $class);
  }

  /**
   * Register a class / callback that should be used to add team members.
   *
   * @return void
   */
  public static function inviteTeamMembersUsing(string $class)
  {
    return app()->singleton(InvitesTeamMembers::class, $class);
  }

  /**
   * Register a class / callback that should be used to remove team members.
   *
   * @return void
   */
  public static function removeTeamMembersUsing(string $class)
  {
    return app()->singleton(RemovesTeamMembers::class, $class);
  }

  /**
   * Register a class / callback that should be used to delete teams.
   *
   * @return void
   */
  public static function deleteTeamsUsing(string $class)
  {
    return app()->singleton(DeletesTeams::class, $class);
  }

  /**
   * Register a class / callback that should be used to delete users.
   *
   * @return void
   */
  public static function deleteUsersUsing(string $class)
  {
    return app()->singleton(DeletesUsers::class, $class);
  }

  /**
   * Find the path to a localized Markdown resource.
   *
   * @return string|null
   */
  public static function localizedMarkdownPath(string $name)
  {
    $localName = preg_replace('#(\.md)$#i', '.'.app()->getLocale().'$1', $name);

    return Arr::first([
      resource_path('markdown/'.$localName),
      resource_path('markdown/'.$name),
    ], fn ($path) => file_exists($path));
  }

  /**
   * Configure Teams to not register its routes.
   */
  public static function ignoreRoutes(): static
  {
    static::$registersRoutes = false;

    return new static;
  }
}
