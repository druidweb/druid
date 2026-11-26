<?php

declare(strict_types=1);

use App\Actions\Teams\AddTeamMember;
use App\Events\AddingTeamMember;
use App\Events\TeamMemberAdded;
use App\Models\User;
use App\Teams\Teams;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Event;
use Illuminate\Validation\ValidationException;

beforeEach(function (): void {
  Event::fake([AddingTeamMember::class, TeamMemberAdded::class]);
});

it('can add a team member', function (): void {
  $owner = User::factory()->withPersonalTeam()->create();
  $newMember = User::factory()->create();

  $action = new AddTeamMember;
  $action->add($owner, $owner->currentTeam, $newMember->email, 'admin');

  expect($owner->currentTeam->fresh()->hasUser($newMember))->toBeTrue();

  Event::assertDispatched(AddingTeamMember::class);
  Event::assertDispatched(TeamMemberAdded::class);
});

it('throws authorization exception when the member lacks the create permission', function (): void {
  $owner = User::factory()->withPersonalTeam()->create();
  $member = User::factory()->create();
  $newMember = User::factory()->create();

  // An undefined role key grants no permissions.
  $owner->currentTeam->users()->attach($member, ['role' => 'retired-role']);

  expect(fn () => (new AddTeamMember)->add($member, $owner->currentTeam, $newMember->email, 'editor'))
    ->toThrow(AuthorizationException::class);
});

it('lets an editor add members at their own role', function (): void {
  $owner = User::factory()->withPersonalTeam()->create();
  $editor = User::factory()->create();
  $newMember = User::factory()->create();

  $owner->currentTeam->users()->attach($editor, ['role' => 'editor']);

  (new AddTeamMember)->add($editor, $owner->currentTeam, $newMember->email, 'editor');

  expect($newMember->fresh()->belongsToTeam($owner->currentTeam))->toBeTrue();
});

it('stops an editor from adding an admin', function (): void {
  $owner = User::factory()->withPersonalTeam()->create();
  $editor = User::factory()->create();
  $newMember = User::factory()->create();

  $owner->currentTeam->users()->attach($editor, ['role' => 'editor']);

  expect(fn () => (new AddTeamMember)->add($editor, $owner->currentTeam, $newMember->email, 'admin'))
    ->toThrow(ValidationException::class, 'You may not assign a role with more permissions than your own.');
  expect($newMember->fresh()->belongsToTeam($owner->currentTeam))->toBeFalse();
});

it('validates that email exists', function (): void {
  $owner = User::factory()->withPersonalTeam()->create();

  $action = new AddTeamMember;

  expect(fn () => $action->add($owner, $owner->currentTeam, 'nonexistent@example.com', 'admin'))
    ->toThrow(ValidationException::class);
});

it('validates that user is not already on team', function (): void {
  $owner = User::factory()->withPersonalTeam()->create();
  $existingMember = User::factory()->create();

  $owner->currentTeam->users()->attach($existingMember, ['role' => 'admin']);

  $action = new AddTeamMember;

  try {
    $action->add($owner, $owner->currentTeam, $existingMember->email, 'admin');
    $this->fail('Expected validation exception');
  } catch (ValidationException $e) {
    expect($e->errorBag)->toBe('addTeamMember');
    expect($e->errors())->toHaveKey('email');
  }
});

it('validates role when roles are enabled', function (): void {
  $owner = User::factory()->withPersonalTeam()->create();
  $newMember = User::factory()->create();

  $action = new AddTeamMember;

  expect(fn () => $action->add($owner, $owner->currentTeam, $newMember->email, 'invalid-role'))
    ->toThrow(ValidationException::class);
});

it('skips role validation when no roles are registered', function (): void {
  $owner = User::factory()->withPersonalTeam()->create();
  $newMember = User::factory()->create();

  // Clear registered roles so Teams::hasRoles() is false, restoring them afterwards
  $originalRoles = Teams::$roles;
  Teams::$roles = [];

  try {
    $action = new AddTeamMember;
    $action->add($owner, $owner->currentTeam, $newMember->email);

    expect($owner->currentTeam->fresh()->hasUser($newMember))->toBeTrue();
  } finally {
    Teams::$roles = $originalRoles;
  }
});
