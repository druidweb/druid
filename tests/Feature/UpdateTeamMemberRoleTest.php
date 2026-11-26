<?php

declare(strict_types=1);

use App\Events\TeamMemberUpdated;
use App\Models\User;
use Illuminate\Support\Facades\Event;

it('updates team member roles', function (): void {
  $this->actingAs($user = User::factory()->withPersonalTeam()->create());

  $user->currentTeam->users()->attach(
    $otherUser = User::factory()->create(), ['role' => 'admin']
  );

  $this->put('/teams/'.$user->currentTeam->id.'/members/'.$otherUser->id, [
    'role' => 'editor',
  ]);

  expect($otherUser->fresh()->hasTeamRole($user->currentTeam->fresh(), 'editor'))->toBeTrue();
});

it('stops a member from changing their own role', function (): void {
  $user = User::factory()->withPersonalTeam()->create();

  $user->currentTeam->users()->attach(
    $otherUser = User::factory()->create(), ['role' => 'admin']
  );

  $this->actingAs($otherUser);

  $this->put('/teams/'.$user->currentTeam->id.'/members/'.$otherUser->id, [
    'role' => 'editor',
  ]);

  expect($otherUser->fresh()->hasTeamRole($user->currentTeam->fresh(), 'admin'))->toBeTrue();
});

it('treats re-selecting the current role as a no-op', function (): void {
  Event::fake([TeamMemberUpdated::class]);

  $this->actingAs($owner = User::factory()->withPersonalTeam()->create());
  $owner->currentTeam->users()->attach($member = User::factory()->create(), ['role' => 'editor']);

  $this->put('/teams/'.$owner->currentTeam->id.'/members/'.$member->id, ['role' => 'editor'])
    ->assertSessionHasNoErrors();

  Event::assertNotDispatched(TeamMemberUpdated::class);
  expect($member->fresh()->hasTeamRole($owner->currentTeam->fresh(), 'editor'))->toBeTrue();
});

it('announces a real role change', function (): void {
  Event::fake([TeamMemberUpdated::class]);

  $this->actingAs($owner = User::factory()->withPersonalTeam()->create());
  $owner->currentTeam->users()->attach($member = User::factory()->create(), ['role' => 'editor']);

  $this->put('/teams/'.$owner->currentTeam->id.'/members/'.$member->id, ['role' => 'admin']);

  Event::assertDispatched(TeamMemberUpdated::class);
});
