<?php

declare(strict_types=1);
use App\Models\User;

it('can check if user has permission on team', function (): void {
  $user = User::factory()->withPersonalTeam()->create();

  expect($user->currentTeam->userHasPermission($user, 'read'))->toBeTrue();
});

it('removes user from team and clears current team', function (): void {
  $owner = User::factory()->withPersonalTeam()->create();
  $member = User::factory()->withPersonalTeam()->create();

  $owner->currentTeam->users()->attach($member, ['role' => 'admin']);

  // Set the member's current team to the owner's team
  $member->forceFill(['current_team_id' => $owner->currentTeam->id])->save();
  $member = $member->fresh();

  expect($member->current_team_id)->toBe($owner->currentTeam->id);

  // Remove the user
  $owner->currentTeam->removeUser($member);

  $member = $member->fresh();
  expect($member->current_team_id)->toBeNull();
  expect($owner->currentTeam->fresh()->hasUser($member))->toBeFalse();
});

it('can get team invitations', function (): void {
  $owner = User::factory()->withPersonalTeam()->create();

  $invitations = $owner->currentTeam->teamInvitations;

  expect($invitations)->toBeCollection();
});
