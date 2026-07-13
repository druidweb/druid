<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\User;

// Drives a same-origin request from the already-authenticated browser session,
// carrying its cookies + CSRF token, and resolves to the HTTP status. Used to hit
// server branches that the UI never exposes a button for (personal-team deletion,
// a non-owner attempting a destructive action). Guarded so the whole Browser suite
// can load every file that defines it without a redeclaration fatal.
if (! function_exists('teamApiRequest')) {
  function teamApiRequest(string $method, string $url, ?array $body = null): string
  {
    $bodyJs = $body === null ? 'undefined' : json_encode($body);

    return <<<JS
      (function () {
        const match = document.cookie.match(/XSRF-TOKEN=([^;]+)/);
        const token = match ? decodeURIComponent(match[1]) : '';
        const options = {
          method: '{$method}',
          headers: {
            'X-XSRF-TOKEN': token,
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          }
        };
        const requestBody = {$bodyJs};
        if (requestBody !== undefined) { options.body = JSON.stringify(requestBody); }
        return fetch('{$url}', options).then(function (response) { return response.status; });
      })()
    JS;
  }
}

it('lets the owner delete a non-personal team', function (): void {
  $user = User::factory()->withPersonalTeam()->create();
  $team = Team::factory()->create([
    'user_id' => $user->id,
    'personal_team' => false,
  ]);
  $this->actingAs($user);

  visit('/teams/'.$team->id)
    ->assertSee('Delete Team')
    // The card's destructive button is the only one on the page until the dialog opens.
    ->click('button.bg-destructive')
    // Confirm inside the portal-rendered dialog.
    ->click('[data-slot="dialog-content"] button.bg-destructive')
    ->assertPathIs('/dashboard');

  expect(Team::query()->find($team->id))->toBeNull();
});

it('does not offer deletion for a personal team', function (): void {
  $user = User::factory()->withPersonalTeam()->create();
  $this->actingAs($user);

  visit('/teams/'.$user->currentTeam->id)
    ->assertSee('Team Settings')
    ->assertDontSee('Delete Team')
    ->assertNoJavaScriptErrors();
});

it('hides team deletion from a non-owner member', function (): void {
  $owner = User::factory()->withPersonalTeam()->create();
  $member = User::factory()->withPersonalTeam()->create();
  $team = Team::factory()->create([
    'user_id' => $owner->id,
    'personal_team' => false,
  ]);
  $team->users()->attach($member, ['role' => 'admin']);
  $this->actingAs($member);

  visit('/teams/'.$team->id)
    ->assertSee('Team Settings')
    ->assertDontSee('Delete Team')
    ->assertNoJavaScriptErrors();
});

it('rejects deleting a personal team via a direct request', function (): void {
  $user = User::factory()->withPersonalTeam()->create();
  $this->actingAs($user);
  $team = $user->currentTeam;

  // The owner passes the delete gate, so ValidateTeamDeletion is what stops this:
  // a personal team may not be deleted, surfaced as a validation error (422).
  $status = visit('/dashboard')->script(teamApiRequest('DELETE', '/teams/'.$team->id));

  expect((int) $status)->toBe(422);
  expect(Team::query()->find($team->id))->not->toBeNull();
});

it('forbids a non-owner member from deleting the team via a direct request', function (): void {
  $owner = User::factory()->withPersonalTeam()->create();
  $member = User::factory()->withPersonalTeam()->create();
  $team = Team::factory()->create([
    'user_id' => $owner->id,
    'personal_team' => false,
  ]);
  $team->users()->attach($member, ['role' => 'admin']);
  $this->actingAs($member);

  $status = visit('/dashboard')->script(teamApiRequest('DELETE', '/teams/'.$team->id));

  expect((int) $status)->toBe(403);
  expect(Team::query()->find($team->id))->not->toBeNull();
});
