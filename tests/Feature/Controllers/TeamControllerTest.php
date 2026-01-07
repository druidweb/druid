<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\User;

it('displays create team page', function (): void {
  $user = User::factory()->withPersonalTeam()->create();

  $this->actingAs($user);

  $response = $this->get('/teams/create');

  $response->assertOk();
  $response->assertInertia(fn ($page) => $page->component('teams/Create'));
});

it('can create a new team', function (): void {
  $user = User::factory()->withPersonalTeam()->create();

  $this->actingAs($user);

  $response = $this->post('/teams', [
    'name' => 'Test Team',
  ]);

  $response->assertRedirect();

  expect(Team::query()->where('name', 'Test Team')->exists())->toBeTrue();
});

it('displays team settings page', function (): void {
  $user = User::factory()->withPersonalTeam()->create();

  $this->actingAs($user);

  $response = $this->get('/teams/'.$user->currentTeam->id);

  $response->assertOk();
  $response->assertInertia(fn ($page) => $page->component('teams/Show'));
});

it('can update team name', function (): void {
  $user = User::factory()->withPersonalTeam()->create();

  $this->actingAs($user);

  $response = $this->put('/teams/'.$user->currentTeam->id, [
    'name' => 'Updated Team Name',
  ]);

  $response->assertRedirect();
  expect($user->currentTeam->fresh()->name)->toBe('Updated Team Name');
});

it('can delete a team', function (): void {
  $user = User::factory()->withPersonalTeam()->create();

  // Create a non-personal team to delete
  $team = Team::factory()->create([
    'user_id' => $user->id,
    'personal_team' => false,
  ]);

  $this->actingAs($user);

  $response = $this->delete('/teams/'.$team->id);

  $response->assertRedirect();
  expect(Team::query()->find($team->id))->toBeNull();
});

it('cannot delete personal team', function (): void {
  $user = User::factory()->withPersonalTeam()->create();

  $this->actingAs($user);

  $response = $this->delete('/teams/'.$user->currentTeam->id);

  $response->assertSessionHasErrors();
  expect(Team::query()->find($user->currentTeam->id))->not->toBeNull();
});
