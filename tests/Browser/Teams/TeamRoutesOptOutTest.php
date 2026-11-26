<?php

declare(strict_types=1);

use App\Models\User;
use App\Teams\Teams;

// The opt-out is read when the app boots, so it has to be in the environment before each test's app is
// created; beforeAll runs ahead of every setUp in this file. afterAll puts the defaults back so later files
// boot with team routes again (Teams::$registersRoutes is static and outlives the app).
beforeAll(function (): void {
  putenv('TEAMS_ROUTES=false');
  $_ENV['TEAMS_ROUTES'] = $_SERVER['TEAMS_ROUTES'] = 'false';
});

afterAll(function (): void {
  putenv('TEAMS_ROUTES');
  unset($_ENV['TEAMS_ROUTES'], $_SERVER['TEAMS_ROUTES']);
  Teams::$registersRoutes = true;
});

it('leaves the team, api token and session routes to the app when TEAMS_ROUTES is false', function (): void {
  $this->actingAs(User::factory()->withPersonalTeam()->create());

  expect(config('teams.routes'))->toBeFalse()
    ->and(Teams::$registersRoutes)->toBeFalse();

  foreach (['/teams/create', '/user/api-tokens', '/user/other-browser-sessions'] as $path) {
    visit($path)->assertSee('404');
  }
});

it('keeps the core app routes when the team routes are off', function (): void {
  $this->actingAs(User::factory()->withPersonalTeam()->create());

  visit('/settings/profile')
    ->assertSee('Profile')
    ->assertNoJavaScriptErrors();
});
