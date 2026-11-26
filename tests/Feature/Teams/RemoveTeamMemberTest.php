<?php

declare(strict_types=1);

use App\Events\RemovingTeamMember;
use App\Events\TeamMemberRemoved;
use App\Models\User;
use Illuminate\Support\Facades\Event;

it('can remove team members from teams', function (): void {
  $this->actingAs($user = User::factory()->withPersonalTeam()->create());

  $user->currentTeam->users()->attach(
    $otherUser = User::factory()->create(), ['role' => 'admin']
  );

  $this->delete('/teams/'.$user->currentTeam->id.'/members/'.$otherUser->id);

  $this->assertCount(0, $user->currentTeam->fresh()->users);
});

it('dispatches removing and removed events around the detach', function (): void {
  Event::fake([RemovingTeamMember::class, TeamMemberRemoved::class]);

  $this->actingAs($user = User::factory()->withPersonalTeam()->create());

  $user->currentTeam->users()->attach(
    $otherUser = User::factory()->create(), ['role' => 'admin']
  );

  $this->delete('/teams/'.$user->currentTeam->id.'/members/'.$otherUser->id);

  Event::assertDispatched(RemovingTeamMember::class, fn (RemovingTeamMember $event): bool => (string) $event->user->id === (string) $otherUser->id);
  Event::assertDispatched(TeamMemberRemoved::class);
});

it('does not dispatch removing event when an owner tries to leave', function (): void {
  Event::fake([RemovingTeamMember::class]);

  $this->actingAs($user = User::factory()->withPersonalTeam()->create());

  $this->delete('/teams/'.$user->currentTeam->id.'/members/'.$user->id);

  Event::assertNotDispatched(RemovingTeamMember::class);
});

it('only allows team owner to remove team members', function (): void {
  $user = User::factory()->withPersonalTeam()->create();

  $user->currentTeam->users()->attach(
    $otherUser = User::factory()->create(), ['role' => 'admin']
  );

  $this->actingAs($otherUser);

  $response = $this->delete('/teams/'.$user->currentTeam->id.'/members/'.$user->id);

  $response->assertStatus(403);
});
