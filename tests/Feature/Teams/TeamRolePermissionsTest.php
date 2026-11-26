<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\User;
use App\Rules\OwnerRole;
use Illuminate\Support\Facades\Gate;

// A team owned by one user with an admin, two editors and a member on an undefined (permission-less) role.
function rolesTeam(): array
{
  $owner = User::factory()->withPersonalTeam()->create();
  $team = $owner->currentTeam;

  $members = [
    'admin' => User::factory()->create(),
    'editor' => User::factory()->create(),
    'editor2' => User::factory()->create(),
    'retired' => User::factory()->create(),
  ];

  $team->users()->attach($members['admin'], ['role' => 'admin']);
  $team->users()->attach($members['editor'], ['role' => 'editor']);
  $team->users()->attach($members['editor2'], ['role' => 'editor']);
  $team->users()->attach($members['retired'], ['role' => 'retired-role']);

  return ['owner' => $owner, 'team' => $team->fresh(), ...$members];
}

it('maps team abilities onto role permissions', function (string $actor, string $ability, bool $allowed): void {
  $t = rolesTeam();

  expect(Gate::forUser($t[$actor])->check($ability, $t['team']))->toBe($allowed);
})->with([
  ['owner', 'update', true], ['owner', 'addTeamMember', true], ['owner', 'updateTeamMember', true], ['owner', 'removeTeamMember', true], ['owner', 'delete', true],
  ['admin', 'update', true], ['admin', 'addTeamMember', true], ['admin', 'updateTeamMember', true], ['admin', 'removeTeamMember', true], ['admin', 'delete', false],
  ['editor', 'update', true], ['editor', 'addTeamMember', true], ['editor', 'updateTeamMember', true], ['editor', 'removeTeamMember', false], ['editor', 'delete', false],
  ['retired', 'update', false], ['retired', 'addTeamMember', false], ['retired', 'updateTeamMember', false], ['retired', 'removeTeamMember', false], ['retired', 'delete', false],
]);

it('resolves the owner to the owner role', function (): void {
  $t = rolesTeam();

  expect($t['owner']->teamRole($t['team']))->toBeInstanceOf(OwnerRole::class)
    ->and($t['owner']->teamPermissions($t['team']))->toBe(['*']);
});

it('binds an owner api token to its abilities', function (): void {
  $t = rolesTeam();

  $t['owner']->withAccessToken($t['owner']->createToken('limited', ['read'])->accessToken);

  expect($t['owner']->hasTeamPermission($t['team'], 'read'))->toBeTrue()
    ->and($t['owner']->hasTeamPermission($t['team'], 'delete'))->toBeFalse();
});

it('only lets users grant roles within their own permissions', function (string $actor, string $role, bool $allowed): void {
  $t = rolesTeam();

  expect($t[$actor]->canAssignTeamRole($t['team'], $role))->toBe($allowed);
})->with([
  ['owner', 'admin', true], ['owner', 'editor', true], ['owner', 'undefined-role', true],
  ['admin', 'admin', true], ['admin', 'editor', true], ['admin', 'undefined-role', false],
  ['editor', 'admin', false], ['editor', 'editor', true],
]);

it('only lets users manage members who do not outrank them', function (string $actor, string $target, bool $allowed): void {
  $t = rolesTeam();

  expect($t[$actor]->canManageTeamMember($t['team'], $t[$target]))->toBe($allowed);
})->with([
  ['owner', 'admin', true], ['owner', 'editor', true], ['owner', 'owner', false],
  ['admin', 'admin', false], ['admin', 'editor', true], ['admin', 'owner', false], ['admin', 'retired', true],
  ['editor', 'admin', false], ['editor', 'editor2', true], ['editor', 'editor', false],
]);

it('does not treat a non-member as manageable', function (): void {
  $t = rolesTeam();

  expect($t['owner']->canManageTeamMember($t['team'], User::factory()->create()))->toBeFalse();
});

it('lets an admin promote an editor', function (): void {
  $t = rolesTeam();

  $this->actingAs($t['admin'])
    ->put("/teams/{$t['team']->id}/members/{$t['editor']->id}", ['role' => 'admin'])
    ->assertSessionHasNoErrors();

  expect($t['editor']->fresh()->hasTeamRole($t['team']->fresh(), 'admin'))->toBeTrue();
});

it('stops an editor promoting anyone to admin', function (): void {
  $t = rolesTeam();

  $this->actingAs($t['editor'])
    ->put("/teams/{$t['team']->id}/members/{$t['editor2']->id}", ['role' => 'admin'])
    ->assertSessionHasErrors('role');

  expect($t['editor2']->fresh()->hasTeamRole($t['team']->fresh(), 'editor'))->toBeTrue();
});

it('stops an editor changing an admin role', function (): void {
  $t = rolesTeam();

  $this->actingAs($t['editor'])
    ->put("/teams/{$t['team']->id}/members/{$t['admin']->id}", ['role' => 'editor'])
    ->assertForbidden();

  expect($t['admin']->fresh()->hasTeamRole($t['team']->fresh(), 'admin'))->toBeTrue();
});

it('lets an admin remove an editor', function (): void {
  $t = rolesTeam();

  $this->actingAs($t['admin'])
    ->delete("/teams/{$t['team']->id}/members/{$t['editor']->id}");

  expect($t['editor']->fresh()->belongsToTeam($t['team']->fresh()))->toBeFalse();
});

it('stops an admin removing the owner', function (): void {
  $t = rolesTeam();

  $this->actingAs($t['admin'])
    ->delete("/teams/{$t['team']->id}/members/{$t['owner']->id}")
    ->assertForbidden();
});

it('lets an admin manage a peer admin', function (): void {
  $t = rolesTeam();
  $otherAdmin = User::factory()->create();
  $t['team']->users()->attach($otherAdmin, ['role' => 'admin']);

  expect($t['admin']->canManageTeamMember($t['team']->fresh(), $otherAdmin))->toBeTrue();
});

it('stops an editor removing members but still lets them leave', function (): void {
  $t = rolesTeam();

  $this->actingAs($t['editor'])
    ->delete("/teams/{$t['team']->id}/members/{$t['editor2']->id}")
    ->assertForbidden();

  $this->actingAs($t['editor'])
    ->delete("/teams/{$t['team']->id}/members/{$t['editor']->id}");

  expect($t['editor']->fresh()->belongsToTeam($t['team']->fresh()))->toBeFalse();
});

it('stops an editor inviting an admin', function (): void {
  $t = rolesTeam();

  $this->actingAs($t['editor'])
    ->post("/teams/{$t['team']->id}/members", ['email' => 'new@example.com', 'role' => 'admin'])
    ->assertSessionHasErrorsIn('addTeamMember', 'role');

  expect($t['team']->teamInvitations()->count())->toBe(0);
});

it('lets an editor cancel invitations they could send, but not admin invitations', function (): void {
  $t = rolesTeam();
  $editorInvite = $t['team']->teamInvitations()->create(['email' => 'ed@example.com', 'role' => 'editor']);
  $adminInvite = $t['team']->teamInvitations()->create(['email' => 'ad@example.com', 'role' => 'admin']);

  $this->actingAs($t['editor'])->delete("/team-invitations/{$adminInvite->id}")->assertForbidden();
  $this->actingAs($t['editor'])->delete("/team-invitations/{$editorInvite->id}");
  $this->actingAs($t['admin'])->delete("/team-invitations/{$adminInvite->id}");

  expect($editorInvite->fresh())->toBeNull()
    ->and($adminInvite->fresh())->toBeNull();
});

it('stops a member without the create permission cancelling invitations', function (): void {
  $t = rolesTeam();
  $invitation = $t['team']->teamInvitations()->create(['email' => 'ed@example.com', 'role' => 'editor']);

  $this->actingAs($t['retired'])->delete("/team-invitations/{$invitation->id}")->assertForbidden();

  expect($invitation->fresh())->not->toBeNull();
});

it('stops an owner read-only token from deleting the team', function (): void {
  $owner = User::factory()->withPersonalTeam()->create();
  $team = Team::factory()->create(['user_id' => $owner->id, 'personal_team' => false]);

  $this->withToken($owner->createToken('read-only', ['read'])->plainTextToken)
    ->delete("/teams/{$team->id}")
    ->assertForbidden();

  expect($team->fresh())->not->toBeNull();
});

it('stops an owner token granting a role beyond its own abilities', function (): void {
  $t = rolesTeam();

  $t['owner']->withAccessToken($t['owner']->createToken('create-only', ['create'])->accessToken);

  expect($t['owner']->canAssignTeamRole($t['team'], 'admin'))->toBeFalse()
    ->and($t['owner']->canManageTeamMember($t['team'], $t['admin']))->toBeFalse();
});

it('refuses api token management from an api token', function (): void {
  $user = User::factory()->withPersonalTeam()->create();
  $token = $user->createToken('read-only', ['read']);

  $this->withToken($token->plainTextToken)
    ->put("/user/api-tokens/{$token->accessToken->id}", ['permissions' => ['create', 'read', 'update', 'delete']])
    ->assertForbidden();

  $this->withToken($token->plainTextToken)
    ->post('/user/api-tokens', ['name' => 'escalated', 'permissions' => ['delete']])
    ->assertForbidden();

  expect($token->accessToken->fresh()->abilities)->toBe(['read'])
    ->and($user->tokens()->count())->toBe(1);
});

it('tells the team page which roles and members the user can act on', function (): void {
  $t = rolesTeam();

  $this->actingAs($t['editor'])
    ->get("/teams/{$t['team']->id}")
    ->assertInertia(fn ($page) => $page
      ->component('teams/Show')
      ->where('assignableRoles', ['editor'])
      ->where('manageableMembers', fn ($ids): bool => collect($ids)->map(fn ($id): string => (string) $id)->sort()->values()->all()
        === collect([$t['editor2']->id, $t['retired']->id])->map(fn ($id): string => (string) $id)->sort()->values()->all())
      ->where('permissions.canRemoveTeamMembers', false)
      ->where('permissions.canUpdateTeam', true));
});
