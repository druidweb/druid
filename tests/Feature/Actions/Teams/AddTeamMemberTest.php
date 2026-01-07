<?php

declare(strict_types=1);

use App\Actions\Teams\AddTeamMember;
use App\Events\AddingTeamMember;
use App\Events\TeamMemberAdded;
use App\Models\User;
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

it('throws authorization exception when user cannot add team members', function (): void {
  $owner = User::factory()->withPersonalTeam()->create();
  $nonOwner = User::factory()->create();
  $newMember = User::factory()->create();

  $owner->currentTeam->users()->attach($nonOwner, ['role' => 'editor']);

  $action = new AddTeamMember;

  expect(fn () => $action->add($nonOwner, $owner->currentTeam, $newMember->email, 'admin'))
    ->toThrow(AuthorizationException::class);
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
