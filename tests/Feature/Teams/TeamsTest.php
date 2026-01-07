<?php

declare(strict_types=1);

use App\Models\Membership;
use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\User;
use App\Teams\Teams;

it('can check if permissions are registered', function (): void {
  expect(Teams::hasPermissions())->toBeTrue();
});

it('can set and get permissions', function (): void {
  $originalPermissions = Teams::$permissions;

  Teams::permissions(['read', 'write', 'delete']);

  expect(Teams::$permissions)->toBe(['read', 'write', 'delete']);

  // Restore original
  Teams::$permissions = $originalPermissions;
});

it('can set default api token permissions', function (): void {
  $originalDefaults = Teams::$defaultPermissions;

  Teams::defaultApiTokenPermissions(['read']);

  expect(Teams::$defaultPermissions)->toBe(['read']);

  // Restore original
  Teams::$defaultPermissions = $originalDefaults;
});

it('can validate permissions', function (): void {
  $originalPermissions = Teams::$permissions;
  Teams::$permissions = ['read', 'write', 'delete'];

  $valid = Teams::validPermissions(['read', 'invalid', 'write']);

  expect($valid)->toBe(['read', 'write']);

  // Restore original
  Teams::$permissions = $originalPermissions;
});

it('can check feature flags', function (): void {
  expect(Teams::managesProfilePhotos())->toBeBool();
  expect(Teams::hasApiFeatures())->toBeBool();
  expect(Teams::hasTeamFeatures())->toBeBool();
});

it('can check if user has team features', function (): void {
  $user = User::factory()->withPersonalTeam()->create();

  expect(Teams::userHasTeamFeatures($user))->toBeTrue();
  expect(Teams::userHasTeamFeatures(123))->toBeFalse();
  expect(Teams::userHasTeamFeatures(null))->toBeFalse();
});

it('can get and set user model', function (): void {
  $originalModel = Teams::userModel();

  expect(Teams::userModel())->toBe(User::class);
  expect(Teams::newUserModel())->toBeInstanceOf(User::class);

  Teams::useUserModel(User::class);
  expect(Teams::userModel())->toBe(User::class);

  // Restore original
  Teams::useUserModel($originalModel);
});

it('can get and set team model', function (): void {
  $originalModel = Teams::teamModel();

  expect(Teams::teamModel())->toBe(Team::class);
  expect(Teams::newTeamModel())->toBeInstanceOf(Team::class);

  Teams::useTeamModel(Team::class);
  expect(Teams::teamModel())->toBe(Team::class);

  // Restore original
  Teams::useTeamModel($originalModel);
});

it('can get and set membership model', function (): void {
  $originalModel = Teams::membershipModel();

  expect(Teams::membershipModel())->toBe(Membership::class);

  Teams::useMembershipModel(Membership::class);
  expect(Teams::membershipModel())->toBe(Membership::class);

  // Restore original
  Teams::useMembershipModel($originalModel);
});

it('can get and set team invitation model', function (): void {
  $originalModel = Teams::teamInvitationModel();

  expect(Teams::teamInvitationModel())->toBe(TeamInvitation::class);

  Teams::useTeamInvitationModel(TeamInvitation::class);
  expect(Teams::teamInvitationModel())->toBe(TeamInvitation::class);

  // Restore original
  Teams::useTeamInvitationModel($originalModel);
});

it('can find a role by key', function (): void {
  $role = Teams::findRole('admin');

  expect($role)->not->toBeNull();
  expect($role->key)->toBe('admin');
});

it('returns null for unknown role', function (): void {
  $role = Teams::findRole('unknown-role');

  expect($role)->toBeNull();
});

it('can ignore routes', function (): void {
  $originalValue = Teams::$registersRoutes;

  Teams::ignoreRoutes();

  expect(Teams::$registersRoutes)->toBeFalse();

  // Restore original
  Teams::$registersRoutes = $originalValue;
});
