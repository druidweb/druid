<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\User;

// Same-origin request from the authenticated browser session, resolving to the HTTP
// status. Guarded against redeclaration when the whole Browser suite loads every
// file that defines it.
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

it('renders the team settings page for the owner', function (): void {
  $user = User::factory()->withPersonalTeam()->create();
  $this->actingAs($user);
  $team = $user->currentTeam;

  $page = visit('/teams/'.$team->id);

  $page->assertSee('Team Settings')
    ->assertSee('Team Owner')
    ->assertSee($user->name)
    ->assertNoJavaScriptErrors();

  expect($page->value('name'))->toBe($team->name);
});

it('updates the team name', function (): void {
  $user = User::factory()->withPersonalTeam()->create();
  $this->actingAs($user);
  $team = $user->currentTeam;

  visit('/teams/'.$team->id)
    ->fill('name', 'Renamed Team')
    ->press('Save')
    ->assertSee('Saved.');

  expect($team->fresh()->name)->toBe('Renamed Team');
});

it('rejects an empty team name', function (): void {
  $user = User::factory()->withPersonalTeam()->create();
  $this->actingAs($user);
  $team = $user->currentTeam;
  $original = $team->name;

  visit('/teams/'.$team->id)
    ->fill('name', '')
    ->press('Save')
    ->assertSee('The name field is required.');

  expect($team->fresh()->name)->toBe($original);
});

it('does not let a non-owner member edit the team name', function (): void {
  $owner = User::factory()->withPersonalTeam()->create();
  $member = User::factory()->withPersonalTeam()->create();
  $team = Team::factory()->create([
    'user_id' => $owner->id,
    'personal_team' => false,
  ]);
  $team->users()->attach($member, ['role' => 'editor']);
  $this->actingAs($member);

  visit('/teams/'.$team->id)
    ->assertSee('Team Settings')
    ->assertPresent('input#name[disabled]')
    ->assertDontSee('Delete Team')
    ->assertNoJavaScriptErrors();
});

it('forbids a non-owner member from updating the team name via a direct request', function (): void {
  $owner = User::factory()->withPersonalTeam()->create();
  $member = User::factory()->withPersonalTeam()->create();
  $team = Team::factory()->create([
    'user_id' => $owner->id,
    'personal_team' => false,
    'name' => 'Locked Team',
  ]);
  $team->users()->attach($member, ['role' => 'editor']);
  $this->actingAs($member);

  $status = visit('/dashboard')->script(teamApiRequest('PUT', '/teams/'.$team->id, ['name' => 'Hijacked Name']));

  expect((int) $status)->toBe(403);
  expect($team->fresh()->name)->toBe('Locked Team');
});

it('forbids a non-member from viewing the team management page', function (): void {
  $owner = User::factory()->withPersonalTeam()->create();
  $stranger = User::factory()->withPersonalTeam()->create();
  $team = Team::factory()->create([
    'user_id' => $owner->id,
    'personal_team' => false,
  ]);
  $this->actingAs($stranger);

  // A user who neither owns nor belongs to the team fails the view gate (TeamPolicy@view).
  $status = visit('/dashboard')->script(teamApiRequest('GET', '/teams/'.$team->id));

  expect((int) $status)->toBe(403);
});
