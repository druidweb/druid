<?php

declare(strict_types=1);

use App\Events\AddingTeamMember;
use App\Events\RemovingTeamMember;
use App\Events\TeamMemberAdded;
use App\Models\Team;
use App\Models\User;

it('can create AddingTeamMember event', function (): void {
  $team = Team::factory()->create();
  $user = User::factory()->create();

  $event = new AddingTeamMember($team, $user);

  expect($event->team)->toBe($team);
  expect($event->user)->toBe($user);
});

it('can create RemovingTeamMember event', function (): void {
  $team = Team::factory()->create();
  $user = User::factory()->create();

  $event = new RemovingTeamMember($team, $user);

  expect($event->team)->toBe($team);
  expect($event->user)->toBe($user);
});

it('can create TeamMemberAdded event', function (): void {
  $team = Team::factory()->create();
  $user = User::factory()->create();

  $event = new TeamMemberAdded($team, $user);

  expect($event->team)->toBe($team);
  expect($event->user)->toBe($user);
});
