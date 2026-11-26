<?php

declare(strict_types=1);

use App\Models\User;
use App\Teams\Teams;

// TEAMS_ROUTES is read at boot, so it is set before each test's app exists and restored after the file
// (Teams::$registersRoutes is static and would otherwise leak into later files in this worker).
beforeAll(function (): void {
  putenv('TEAMS_ROUTES=false');
  $_ENV['TEAMS_ROUTES'] = $_SERVER['TEAMS_ROUTES'] = 'false';
});

afterAll(function (): void {
  putenv('TEAMS_ROUTES');
  unset($_ENV['TEAMS_ROUTES'], $_SERVER['TEAMS_ROUTES']);
  Teams::$registersRoutes = true;
});

it('does not register the team routes when TEAMS_ROUTES is false', function (): void {
  expect(Teams::$registersRoutes)->toBeFalse();

  $this->actingAs(User::factory()->withPersonalTeam()->create());

  $this->get('/teams/create')->assertNotFound();
  $this->get('/user/api-tokens')->assertNotFound();
  $this->get('/settings/profile')->assertOk();
});
