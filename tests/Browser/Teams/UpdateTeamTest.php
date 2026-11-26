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

it('does not let a member without the update permission edit the team name', function (): void {
  $owner = User::factory()->withPersonalTeam()->create();
  $member = User::factory()->withPersonalTeam()->create();
  $team = Team::factory()->create([
    'user_id' => $owner->id,
    'personal_team' => false,
  ]);
  // An undefined role key grants no permissions (editors and admins hold 'update' and may rename).
  $team->users()->attach($member, ['role' => 'retired-role']);
  $this->actingAs($member);

  visit('/teams/'.$team->id)
    ->assertSee('Team Settings')
    ->assertPresent('input#name[disabled]')
    ->assertDontSee('Delete Team')
    ->assertNoJavaScriptErrors();
});

it('forbids a member without the update permission from renaming the team via a direct request', function (): void {
  $owner = User::factory()->withPersonalTeam()->create();
  $member = User::factory()->withPersonalTeam()->create();
  $team = Team::factory()->create([
    'user_id' => $owner->id,
    'personal_team' => false,
    'name' => 'Locked Team',
  ]);
  // An undefined role key grants no permissions (editors and admins hold 'update' and may rename).
  $team->users()->attach($member, ['role' => 'retired-role']);
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

it('forbids a non-member from renaming the team via a direct request', function (): void {
  $owner = User::factory()->withPersonalTeam()->create();
  $stranger = User::factory()->withPersonalTeam()->create();
  $team = Team::factory()->create(['user_id' => $owner->id, 'personal_team' => false, 'name' => 'Not Yours']);
  $this->actingAs($stranger);

  $status = visit('/dashboard')->script(sameOriginRequest('PUT', '/teams/'.$team->id, ['name' => 'Taken Over']));

  expect((int) $status)->toBe(403);
  expect($team->fresh()->name)->toBe('Not Yours');
});

// One token per test: the in-process server keeps the guard's resolved user between requests, so a second
// bearer in the same test would be answered as the first.
it('refuses an owner api token that lacks the update ability', function (): void {
  $owner = User::factory()->withPersonalTeam()->create();
  $team = Team::factory()->create(['user_id' => $owner->id, 'personal_team' => false, 'name' => 'Token Team']);
  $readOnly = $owner->createToken('read-only', ['read'])->plainTextToken;

  // No session: the request authenticates purely with the bearer token.
  $status = visit('/')->script(sameOriginRequest('PUT', '/teams/'.$team->id, ['name' => 'Read Only Rename'], $readOnly));

  expect((int) $status)->toBe(403);
  expect($team->fresh()->name)->toBe('Token Team');
});

it('lets an owner api token with the update ability rename the team', function (): void {
  $owner = User::factory()->withPersonalTeam()->create();
  $team = Team::factory()->create(['user_id' => $owner->id, 'personal_team' => false, 'name' => 'Token Team']);
  $updater = $owner->createToken('updater', ['update'])->plainTextToken;

  visit('/')->script(sameOriginRequest('PUT', '/teams/'.$team->id, ['name' => 'Updated By Token'], $updater));

  expect($team->fresh()->name)->toBe('Updated By Token');
});
