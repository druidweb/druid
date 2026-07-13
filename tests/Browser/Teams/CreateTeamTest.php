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

  visit('/teams/create')
    ->fill('name', 'Engineering')
    ->click('Create')
    ->assertPathIsNot('/teams/create');

  expect($user->fresh()->ownedTeams()->where('name', 'Engineering')->exists())->toBeTrue();
});
