<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\User;
use App\Rules\OwnerRole;
use App\Teams\Teams;

it('can check if team is current team', function (): void {
  $user = User::factory()->withPersonalTeam()->create();
  $team = $user->currentTeam;

  expect($user->isCurrentTeam($team))->toBeTrue();

  $otherTeam = Team::factory()->create(['user_id' => $user->id]);
  expect($user->isCurrentTeam($otherTeam))->toBeFalse();
});

it('returns personal team when current team is null', function (): void {
  $user = User::factory()->withPersonalTeam()->create();
  $user->current_team_id = null;
  $user->save();

  $user->refresh();
  $currentTeam = $user->currentTeam;

  expect($currentTeam)->not->toBeNull();
  expect($currentTeam->personal_team)->toBeTrue();
});

it('can switch teams', function (): void {
  $user = User::factory()->withPersonalTeam()->create();
  $newTeam = Team::factory()->create(['user_id' => $user->id]);

  expect($user->switchTeam($newTeam))->toBeTrue();
  expect($user->fresh()->current_team_id)->toBe((int) $newTeam->id);
});

it('cannot switch to team user does not belong to', function (): void {
  $user = User::factory()->withPersonalTeam()->create();
  $otherUser = User::factory()->withPersonalTeam()->create();

  expect($user->switchTeam($otherUser->currentTeam))->toBeFalse();
});

it('returns all teams user owns or belongs to', function (): void {
  $user = User::factory()->withPersonalTeam()->create();
  $ownedTeam = Team::factory()->create(['user_id' => $user->id]);
  $memberTeam = Team::factory()->create();
  $memberTeam->users()->attach($user, ['role' => 'admin']);

  $allTeams = $user->allTeams();

  expect($allTeams)->toHaveCount(3);
});

it('returns personal team', function (): void {
  $user = User::factory()->withPersonalTeam()->create();

  $personalTeam = $user->personalTeam();

  expect($personalTeam)->not->toBeNull();
  expect($personalTeam->personal_team)->toBeTrue();
});

it('determines if user owns team', function (): void {
  $user = User::factory()->withPersonalTeam()->create();
  $otherUser = User::factory()->withPersonalTeam()->create();

  expect($user->ownsTeam($user->currentTeam))->toBeTrue();
  expect($user->ownsTeam($otherUser->currentTeam))->toBeFalse();
  expect($user->ownsTeam(null))->toBeFalse();
});

it('determines if user belongs to team', function (): void {
  $user = User::factory()->withPersonalTeam()->create();
  $personalTeam = $user->currentTeam;
  $memberTeam = Team::factory()->create();
  $memberTeam->users()->attach($user, ['role' => 'admin']);

  // Reload user with teams relationship
  $user = User::with(['teams', 'ownedTeams', 'currentTeam'])->find($user->id);
  $memberTeam = Team::query()->find($memberTeam->id);

  // User owns their personal team
  expect($user->belongsToTeam($personalTeam))->toBeTrue();
  // User is a member of memberTeam
  expect($user->belongsToTeam($memberTeam))->toBeTrue();
  expect($user->belongsToTeam(null))->toBeFalse();
});

it('returns owner role for team owner', function (): void {
  $user = User::factory()->withPersonalTeam()->create();

  $role = $user->teamRole($user->currentTeam);

  expect($role)->toBeInstanceOf(OwnerRole::class);
});

it('returns null role for non-member', function (): void {
  $user = User::factory()->withPersonalTeam()->create();
  $otherUser = User::factory()->withPersonalTeam()->create();

  $role = $user->teamRole($otherUser->currentTeam);

  expect($role)->toBeNull();
});

it('returns role for team member', function (): void {
  $user = User::factory()->withPersonalTeam()->create();
  $memberTeam = Team::factory()->create();
  $memberTeam->users()->attach($user, ['role' => 'admin']);

  $user->refresh();
  $memberTeam->refresh();

  $role = $user->teamRole($memberTeam);

  expect($role)->not->toBeNull();
  expect($role->key)->toBe('admin');
});

it('checks if user has team role', function (): void {
  $user = User::factory()->withPersonalTeam()->create();
  $memberTeam = Team::factory()->create();
  $memberTeam->users()->attach($user, ['role' => 'admin']);

  $user->refresh();
  $memberTeam->refresh();

  // Owner has any role
  expect($user->hasTeamRole($user->currentTeam, 'admin'))->toBeTrue();
  expect($user->hasTeamRole($memberTeam, 'admin'))->toBeTrue();
  expect($user->hasTeamRole($memberTeam, 'editor'))->toBeFalse();
});

it('returns team permissions for owner', function (): void {
  $user = User::factory()->withPersonalTeam()->create();

  $permissions = $user->teamPermissions($user->currentTeam);

  expect($permissions)->toBe(['*']);
});

it('returns empty permissions for non-member', function (): void {
  $user = User::factory()->withPersonalTeam()->create();
  $otherUser = User::factory()->withPersonalTeam()->create();

  $permissions = $user->teamPermissions($otherUser->currentTeam);

  expect($permissions)->toBe([]);
});

it('checks team permission for owner', function (): void {
  $user = User::factory()->withPersonalTeam()->create();

  expect($user->hasTeamPermission($user->currentTeam, 'read'))->toBeTrue();
  expect($user->hasTeamPermission($user->currentTeam, 'any:permission'))->toBeTrue();
});

it('returns false for hasTeamRole when user does not belong to team', function (): void {
  $user = User::factory()->withPersonalTeam()->create();
  $otherUser = User::factory()->withPersonalTeam()->create();

  expect($user->hasTeamRole($otherUser->currentTeam, 'admin'))->toBeFalse();
});

it('returns permissions for team member', function (): void {
  $user = User::factory()->withPersonalTeam()->create();
  $memberTeam = Team::factory()->create();
  $memberTeam->users()->attach($user, ['role' => 'admin']);

  $user = User::with(['teams', 'ownedTeams'])->find($user->id);
  $memberTeam = Team::with('users')->find($memberTeam->id);

  $permissions = $user->teamPermissions($memberTeam);

  expect($permissions)->toBeArray();
});

it('returns false for hasTeamPermission when user does not belong to team', function (): void {
  $user = User::factory()->withPersonalTeam()->create();
  $otherUser = User::factory()->withPersonalTeam()->create();

  expect($user->hasTeamPermission($otherUser->currentTeam, 'read'))->toBeFalse();
});

it('checks team permission for member with specific permissions', function (): void {
  $user = User::factory()->withPersonalTeam()->create();
  $memberTeam = Team::factory()->create();
  $memberTeam->users()->attach($user, ['role' => 'admin']);

  $user = User::with(['teams', 'ownedTeams'])->find($user->id);
  $memberTeam = Team::with('users')->find($memberTeam->id);

  // Admin role has 'read' permission
  expect($user->hasTeamPermission($memberTeam, 'read'))->toBeTrue();
});

it('returns null role when user not in team users collection', function (): void {
  $user = User::factory()->withPersonalTeam()->create();
  $otherTeam = Team::factory()->create();

  // User is not attached to otherTeam
  $role = $user->teamRole($otherTeam);

  expect($role)->toBeNull();
});

it('returns false for hasTeamRole when user not in team users collection', function (): void {
  $user = User::factory()->withPersonalTeam()->create();
  $otherTeam = Team::factory()->create();

  // User is not attached to otherTeam
  expect($user->hasTeamRole($otherTeam, 'admin'))->toBeFalse();
});

it('checks team permission with create wildcard', function (): void {
  Teams::role('creator', 'Creator', ['*:create'])->description('Can create anything');

  $user = User::factory()->withPersonalTeam()->create();
  $memberTeam = Team::factory()->create();
  $memberTeam->users()->attach($user, ['role' => 'creator']);

  $user = User::with(['teams', 'ownedTeams'])->find($user->id);
  $memberTeam = Team::with('users')->find($memberTeam->id);

  expect($user->hasTeamPermission($memberTeam, 'posts:create'))->toBeTrue();
  expect($user->hasTeamPermission($memberTeam, 'posts:update'))->toBeFalse();
});

it('checks team permission with update wildcard', function (): void {
  Teams::role('updater', 'Updater', ['*:update'])->description('Can update anything');

  $user = User::factory()->withPersonalTeam()->create();
  $memberTeam = Team::factory()->create();
  $memberTeam->users()->attach($user, ['role' => 'updater']);

  $user = User::with(['teams', 'ownedTeams'])->find($user->id);
  $memberTeam = Team::with('users')->find($memberTeam->id);

  expect($user->hasTeamPermission($memberTeam, 'posts:update'))->toBeTrue();
  expect($user->hasTeamPermission($memberTeam, 'posts:create'))->toBeFalse();
});

it('returns false for hasTeamPermission when api token lacks permission', function (): void {
  $user = User::factory()->withPersonalTeam()->create();
  $memberTeam = Team::factory()->create();
  $memberTeam->users()->attach($user, ['role' => 'admin']);

  // Create API token without any abilities
  $token = $user->createToken('test-token', []);
  $user->withAccessToken($token->accessToken);

  $user = User::with(['teams', 'ownedTeams'])->find($user->id);
  $memberTeam = Team::with('users')->find($memberTeam->id);

  // Re-apply the token to the loaded user
  $user->withAccessToken($token->accessToken);

  expect($user->hasTeamPermission($memberTeam, 'read'))->toBeFalse();
});

it('returns null role when user teams and team users are out of sync', function (): void {
  $user = User::factory()->withPersonalTeam()->create();
  $memberTeam = Team::factory()->create();

  // Attach user to team
  $memberTeam->users()->attach($user, ['role' => 'admin']);

  // Load user with teams relationship (includes memberTeam)
  $user = User::with(['teams', 'ownedTeams'])->find($user->id);

  // Detach user from team in database
  $memberTeam->users()->detach($user);

  // Get fresh team with users loaded (won't include this user now)
  $freshTeam = Team::with('users')->find($memberTeam->id);

  // User's teams still has memberTeam (stale), but freshTeam->users doesn't have user
  // belongsToTeam returns true (based on stale user->teams), but user not in team->users
  $role = $user->teamRole($freshTeam);

  expect($role)->toBeNull();
});

it('returns false for hasTeamRole when user teams and team users are out of sync', function (): void {
  $user = User::factory()->withPersonalTeam()->create();
  $memberTeam = Team::factory()->create();

  // Attach user to team
  $memberTeam->users()->attach($user, ['role' => 'admin']);

  // Load user with teams relationship (includes memberTeam)
  $user = User::with(['teams', 'ownedTeams'])->find($user->id);

  // Detach user from team in database
  $memberTeam->users()->detach($user);

  // Get fresh team with users loaded (won't include this user now)
  $freshTeam = Team::with('users')->find($memberTeam->id);

  // User's teams still has memberTeam (stale), but freshTeam->users doesn't have user
  expect($user->hasTeamRole($freshTeam, 'admin'))->toBeFalse();
});
