<?php

declare(strict_types=1);

use App\Models\User;

it('renders the create team page', function (): void {
  $user = User::factory()->withPersonalTeam()->create();
  $this->actingAs($user);

  visit('/teams/create')
    ->assertPresent('name')
    ->assertNoJavaScriptErrors();
});

it('creates a new team', function (): void {
  $user = User::factory()->withPersonalTeam()->create();
  $this->actingAs($user);

  // Wait for the settings page before reading the DB, so the create request has finished.
  $page = visit('/teams/create')
    ->fill('name', 'Engineering')
    ->click('Create')
    ->assertSee('Team Settings')
    ->assertValue('#name', 'Engineering');

  // CreateTeam::redirectTo() lands the creator on the new team's settings, switched onto it.
  $team = $user->fresh()->ownedTeams()->where('name', 'Engineering')->firstOrFail();
  $page->assertPathIs('/teams/'.$team->id)
    ->assertNoJavaScriptErrors();

  expect((string) $user->fresh()->current_team_id)->toBe((string) $team->id);
});
