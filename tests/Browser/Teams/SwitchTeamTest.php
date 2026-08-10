<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\User;

// PUT /current-team has no dedicated UI in this build (TeamSwitcher.vue is not
// mounted in any layout), so the switch is driven through a real in-page request
// that carries the authenticated session cookies and the CSRF token, and then the
// resulting effect (the persisted current_team_id) is asserted.
function switchCurrentTeamRequest(int $teamId): string
{
  // Wrapped in an IIFE so the script body is a single expression that resolves to
  // the response status (script() evals the string as an expression, not a function body).
  return <<<JS
    (function () {
      const match = document.cookie.match(/XSRF-TOKEN=([^;]+)/);
      const token = match ? decodeURIComponent(match[1]) : '';
      return fetch('/current-team', {
        method: 'PUT',
        headers: {
          'X-XSRF-TOKEN': token,
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ team_id: {$teamId} })
      }).then(function (response) { return response.status; });
    })()
  JS;
}

it('switches the authenticated user to another team they own', function (): void {
  $user = User::factory()->withPersonalTeam()->create();
  $another = Team::factory()->create([
    'user_id' => $user->id,
    'personal_team' => false,
  ]);
  $this->actingAs($user);

  $personalTeamId = (string) $user->currentTeam->id;

  $page = visit('/dashboard');
  $status = $page->script(switchCurrentTeamRequest((int) $another->id));

  expect((int) $status)->toBeLessThan(400);
  expect((string) $user->fresh()->current_team_id)->toBe((string) $another->id);
  expect((string) $user->fresh()->current_team_id)->not->toBe($personalTeamId);
});

it('resolves no current team for a user who has no teams at all', function (): void {
  // A user with no owned or joined teams: currentTeam() finds current_team_id null,
  // personalTeam() returns null, and switchTeam(null) falls through belongsToTeam's
  // null guard (HasTeams) instead of crashing the request.
  $user = User::factory()->create();
  $this->actingAs($user);

  visit('/dashboard')
    ->assertPathIs('/dashboard')
    ->assertNoJavaScriptErrors();

  // The failed personal-team fallback must leave the user without a current team.
  expect($user->fresh()->current_team_id)->toBeNull();
});

it('forbids switching to a team the user does not belong to', function (): void {
  $user = User::factory()->withPersonalTeam()->create();
  $stranger = User::factory()->withPersonalTeam()->create();
  $this->actingAs($user);

  $personalTeamId = (string) $user->currentTeam->id;

  $page = visit('/dashboard');
  $status = $page->script(switchCurrentTeamRequest((int) $stranger->currentTeam->id));

  expect((int) $status)->toBe(403);
  expect((string) $user->fresh()->current_team_id)->toBe($personalTeamId);
});
