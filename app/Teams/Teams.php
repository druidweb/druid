<?php

declare(strict_types=1);

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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

final class Teams
{
  /**
   * Indicates if Teams routes will be registered.
   */
  public static bool $registersRoutes = true;

  /**
   * The roles that are available to assign to users.
   *
   * @var array<string, Role>
   */
  public static array $roles = [];

  /**
   * The permissions that exist within the application.
   *
   * @var array<int, string>
   */
  public static array $permissions = [];

  /**
   * The default permissions that should be available to new entities.
   *
   * @var array<int, string>
   */
  public static array $defaultPermissions = [];

  /**
   * The user model that should be used by Teams.
   *
   * @var class-string
   */
  public static string $userModel = User::class;

  /**
   * The team model that should be used by Teams.
   *
   * @var class-string
   */
  public static string $teamModel = Team::class;

  /**
   * The membership model that should be used by Teams.
   *
   * @var class-string
   */
  public static string $membershipModel = Membership::class;

  /**
   * The team invitation model that should be used by Teams.
   *
   * @var class-string
   */
  public static string $teamInvitationModel = TeamInvitation::class;

  /**
   * Determine if Teams has registered roles.
   */
  public static function hasRoles(): bool
  {
    return count(self::$roles) > 0;
  }

  /**
   * Find the role with the given key.
   */
  public static function findRole(string $key): ?Role
  {
    return self::$roles[$key] ?? null;
  }

  /**
   * Define a role.
   *
   * @param  array<int, string>  $permissions
   */
  public static function role(string $key, string $name, array $permissions): Role
  {
    /** @var array<int, string> $mergedPermissions */
    $mergedPermissions = collect(array_merge(self::$permissions, $permissions))
      ->unique()
      ->sort()
      ->values()
      ->all();

    self::$permissions = $mergedPermissions;

    return tap(new Role($key, $name, $permissions), function (Role $role) use ($key): void {
      self::$roles[$key] = $role;
    });
  }

  /**
   * Determine if any permissions have been registered with Teams.
   */
  public static function hasPermissions(): bool
  {
    return count(self::$permissions) > 0;
  }

  /**
   * Define the available API token permissions.
   *
   * @param  array<int, string>  $permissions
   */
  public static function permissions(array $permissions): static
  {
    /** @var array<int, string> $permissions */
    self::$permissions = $permissions;

    return new self;
  }

  /**
   * Define the default permissions that should be available to new API tokens.
   *
   * @param  array<int, string>  $permissions
   */
  public static function defaultApiTokenPermissions(array $permissions): static
  {
    /** @var array<int, string> $permissions */
    self::$defaultPermissions = $permissions;

    return new self;
  }

  /**
   * Return the permissions in the given list that are actually defined permissions for the application.
   *
   * @param  array<int|string, mixed>  $permissions
   * @return array<int, string>
   */
  public static function validPermissions(array $permissions): array
  {
    /** @var array<int, string> $result */
    $result = array_values(array_intersect($permissions, self::$permissions));

    return $result;
  }

  /**
   * Determine if Teams is managing profile photos.
   */
  public static function managesProfilePhotos(): bool
  {
    return Features::managesProfilePhotos();
  }

  /**
   * Determine if Teams is supporting API features.
   */
  public static function hasApiFeatures(): bool
  {
    return Features::hasApiFeatures();
  }

  /**
   * Determine if Teams is supporting team features.
   */
  public static function hasTeamFeatures(): bool
  {
    return Features::hasTeamFeatures();
  }

  /**
   * Determine if a given user model utilizes the "HasTeams" trait.
   */
  public static function userHasTeamFeatures(mixed $user): bool
  {
    if (! is_object($user) && ! is_string($user)) {
      return false;
    }

    return (array_key_exists(HasTeams::class, class_uses_recursive($user)) ||
            method_exists($user, 'currentTeam')) &&
            self::hasTeamFeatures();
  }

  /**
   * Determine if the application is using the terms confirmation feature.
   */
  public static function hasTermsAndPrivacyPolicyFeature(): bool
  {
    return Features::hasTermsAndPrivacyPolicyFeature();
  }

  /**
   * Determine if the application is using any account deletion features.
   */
  public static function hasAccountDeletionFeatures(): bool
  {
    return Features::hasAccountDeletionFeatures();
  }

  /**
   * Find a user instance by the given ID.
   *
   * @return User
   */
  public static function findUserByIdOrFail(mixed $id): mixed
  {
    /** @var User $model */
    $model = self::newUserModel();

    /** @var Builder<User> $query */
    $query = $model->where('id', $id);

    return $query->firstOrFail();
  }

  /**
   * Find a user instance by the given email address or fail.
   *
   * @return User
   */
  public static function findUserByEmailOrFail(string $email): mixed
  {
    /** @var User $model */
    $model = self::newUserModel();

    /** @var Builder<User> $query */
    $query = $model->where('email', $email);

    return $query->firstOrFail();
  }

  /**
   * Get the name of the user model used by the application.
   */
  public static function userModel(): string
  {
    return self::$userModel;
  }

  /**
   * Get a new instance of the user model.
   */
  public static function newUserModel(): mixed
  {
    $model = self::userModel();

    return new $model;
  }

  /**
   * Specify the user model that should be used by Teams.
   *
   * @param  class-string  $model
   */
  public static function useUserModel(string $model): static
  {
    /** @var class-string $model */
    self::$userModel = $model;

    return new self;
  }

  /**
   * Get the name of the team model used by the application.
   *
   * @return class-string
   */
  public static function teamModel(): string
  {
    return self::$teamModel;
  }

  /**
   * Get a new instance of the team model.
   *
   * @return Team
   */
  public static function newTeamModel(): mixed
  {
    /** @var class-string<Team> $model */
    $model = self::teamModel();

    return new $model;
  }

  /**
   * Specify the team model that should be used by Teams.
   *
   * @param  class-string  $model
   */
  public static function useTeamModel(string $model): static
  {
    /** @var class-string $model */
    self::$teamModel = $model;

    return new self;
  }

  /**
   * Get the name of the membership model used by the application.
   */
  public static function membershipModel(): string
  {
    return self::$membershipModel;
  }

  /**
   * Specify the membership model that should be used by Teams.
   *
   * @param  class-string  $model
   */
  public static function useMembershipModel(string $model): static
  {
    /** @var class-string $model */
    self::$membershipModel = $model;

    return new self;
  }

  /**
   * Get the name of the team invitation model used by the application.
   *
   * @return class-string
   */
  public static function teamInvitationModel(): string
  {
    return self::$teamInvitationModel;
  }

  /**
   * Specify the team invitation model that should be used by Teams.
   *
   * @param  class-string  $model
   */
  public static function useTeamInvitationModel(string $model): static
  {
    /** @var class-string $model */
    self::$teamInvitationModel = $model;

    return new self;
  }

  /**
   * Register a class / callback that should be used to create teams.
   */
  public static function createTeamsUsing(string $class): void
  {
    app()->singleton(CreatesTeams::class, $class);
  }

  /**
   * Register a class / callback that should be used to update team names.
   */
  public static function updateTeamNamesUsing(string $class): void
  {
    app()->singleton(UpdatesTeamNames::class, $class);
  }

  /**
   * Register a class / callback that should be used to add team members.
   */
  public static function addTeamMembersUsing(string $class): void
  {
    app()->singleton(AddsTeamMembers::class, $class);
  }

  /**
   * Register a class / callback that should be used to add team members.
   */
  public static function inviteTeamMembersUsing(string $class): void
  {
    app()->singleton(InvitesTeamMembers::class, $class);
  }

  /**
   * Register a class / callback that should be used to remove team members.
   */
  public static function removeTeamMembersUsing(string $class): void
  {
    app()->singleton(RemovesTeamMembers::class, $class);
  }

  /**
   * Register a class / callback that should be used to delete teams.
   */
  public static function deleteTeamsUsing(string $class): void
  {
    app()->singleton(DeletesTeams::class, $class);
  }

  /**
   * Register a class / callback that should be used to delete users.
   */
  public static function deleteUsersUsing(string $class): void
  {
    app()->singleton(DeletesUsers::class, $class);
  }

  /**
   * Find the path to a localized Markdown resource.
   */
  public static function localizedMarkdownPath(string $name): ?string
  {
    $localName = preg_replace('#(\.md)$#i', '.'.app()->getLocale().'$1', $name);

    return Arr::first([
      resource_path('markdown/'.$localName),
      resource_path('markdown/'.$name),
    ], fn (string $path): bool => file_exists($path));
  }

  /**
   * Configure Teams to not register its routes.
   */
  public static function ignoreRoutes(): static
  {
    self::$registersRoutes = false;

    return new self;
  }
}
