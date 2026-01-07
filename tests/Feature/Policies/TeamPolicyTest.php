<?php

declare(strict_types=1);

use App\Models\User;
use App\Policies\TeamPolicy;

it('allows any user to view any teams', function (): void {
  $user = User::factory()->withPersonalTeam()->create();
  $policy = new TeamPolicy;

  expect($policy->viewAny($user))->toBeTrue();
});

it('allows user to view team they belong to', function (): void {
  $user = User::factory()->withPersonalTeam()->create();
  $policy = new TeamPolicy;

  expect($policy->view($user, $user->currentTeam))->toBeTrue();
});

it('denies user from viewing team they do not belong to', function (): void {
  $user = User::factory()->withPersonalTeam()->create();
  $otherUser = User::factory()->withPersonalTeam()->create();
  $policy = new TeamPolicy;

  expect($policy->view($user, $otherUser->currentTeam))->toBeFalse();
});

it('allows user to create teams', function (): void {
  $user = User::factory()->withPersonalTeam()->create();
  $policy = new TeamPolicy;

  expect($policy->create($user))->toBeTrue();
});

it('allows team owner to update team', function (): void {
  $user = User::factory()->withPersonalTeam()->create();
  $policy = new TeamPolicy;

  expect($policy->update($user, $user->currentTeam))->toBeTrue();
});

it('allows team owner to add team members', function (): void {
  $user = User::factory()->withPersonalTeam()->create();
  $policy = new TeamPolicy;

  expect($policy->addTeamMember($user, $user->currentTeam))->toBeTrue();
});

it('allows team owner to update team member', function (): void {
  $user = User::factory()->withPersonalTeam()->create();
  $policy = new TeamPolicy;

  expect($policy->updateTeamMember($user, $user->currentTeam))->toBeTrue();
});

it('allows team owner to remove team members', function (): void {
  $user = User::factory()->withPersonalTeam()->create();
  $policy = new TeamPolicy;

  expect($policy->removeTeamMember($user, $user->currentTeam))->toBeTrue();
});

it('allows team owner to delete team', function (): void {
  $user = User::factory()->withPersonalTeam()->create();
  $policy = new TeamPolicy;

  expect($policy->delete($user, $user->currentTeam))->toBeTrue();
});
