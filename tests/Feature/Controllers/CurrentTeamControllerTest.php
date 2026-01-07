<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\User;

it('allows user to switch current team', function (): void {
  $user = User::factory()->withPersonalTeam()->create();

  // Create another team for the user
  $anotherTeam = Team::factory()->create([
    'user_id' => $user->id,
    'personal_team' => false,
  ]);

  $this->actingAs($user);

  $response = $this->put('/current-team', [
    'team_id' => $anotherTeam->id,
  ]);

  $response->assertRedirect();
  expect((string) $user->fresh()->current_team_id)->toBe((string) $anotherTeam->id);
});

it('prevents switching to team user does not belong to', function (): void {
  $user = User::factory()->withPersonalTeam()->create();
  $otherUser = User::factory()->withPersonalTeam()->create();

  $this->actingAs($user);

  $response = $this->put('/current-team', [
    'team_id' => $otherUser->currentTeam->id,
  ]);

  $response->assertForbidden();
});
