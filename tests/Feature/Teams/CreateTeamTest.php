<?php

declare(strict_types=1);

use App\Models\User;

it('can create teams', function (): void {
  $this->actingAs($user = User::factory()->withPersonalTeam()->create());

  $this->post('/teams', [
    'name' => 'Test Team',
  ]);

  $this->assertCount(2, $user->fresh()->ownedTeams);
  $this->assertEquals('Test Team', $user->fresh()->ownedTeams()->latest('id')->first()->name);
});

it('lands on the new team settings page after creating a team', function (): void {
  $this->actingAs($user = User::factory()->withPersonalTeam()->create());

  $response = $this->post('/teams', ['name' => 'Launch Crew']);

  $team = $user->fresh()->ownedTeams()->where('name', 'Launch Crew')->firstOrFail();
  $response->assertRedirect(route('teams.show', $team->id));
});
