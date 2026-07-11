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

it('invites a new team member via email', function (): void {
  $user = User::factory()->withPersonalTeam()->create();
  $this->actingAs($user);
  $team = $user->currentTeam;

  visit('/teams/'.$team->id)
    ->fill('email', 'newcomer@example.com')
    // Selects the "Administrator" role option in the add-member card.
    ->click('Administrator')
    ->press('Add')
    ->assertSee('Pending Team Invitations')
    ->assertSee('newcomer@example.com');

  expect($team->fresh()->teamInvitations()->where('email', 'newcomer@example.com')->exists())->toBeTrue();
});

it('requires a role when inviting a member', function (): void {
  $user = User::factory()->withPersonalTeam()->create();
  $this->actingAs($user);
  $team = $user->currentTeam;

  visit('/teams/'.$team->id)
    ->fill('email', 'norole@example.com')
    ->press('Add')
    ->assertSee('The role field is required.');

  expect($team->fresh()->teamInvitations()->count())->toBe(0);
});

it('updates a team member role', function (): void {
  $owner = User::factory()->withPersonalTeam()->create();
  $member = User::factory()->withPersonalTeam()->create(['name' => 'Marina Member']);
  $team = $owner->currentTeam;
  $team->users()->attach($member, ['role' => 'editor']);
  $this->actingAs($owner);

  visit('/teams/'.$team->id)
    ->assertSee('Marina Member')
    // The member's current-role button (a Button component) opens the manage-role dialog.
    ->click('button[data-slot="button"]:has-text("Editor")')
    // Pick the Administrator role inside the dialog, then save.
    ->click('[data-slot="dialog-content"] button:has-text("Administrator")')
    ->click('[data-slot="dialog-content"] button.bg-primary')
    ->assertNoJavaScriptErrors();

  expect($team->fresh()->users()->find($member->id)->membership->role)->toBe('admin');
});

it('removes a team member', function (): void {
  $owner = User::factory()->withPersonalTeam()->create();
  $member = User::factory()->withPersonalTeam()->create(['name' => 'Rex Removed']);
  $team = $owner->currentTeam;
  $team->users()->attach($member, ['role' => 'admin']);
  $this->actingAs($owner);

  visit('/teams/'.$team->id)
    ->assertSee('Rex Removed')
    ->press('Remove')
    ->click('[data-slot="dialog-content"] button.bg-destructive')
    ->assertDontSee('Rex Removed');

  expect($team->fresh()->users()->where('users.id', $member->id)->exists())->toBeFalse();
});

it('lets a member leave the team', function (): void {
  $owner = User::factory()->withPersonalTeam()->create();
  $member = User::factory()->withPersonalTeam()->create();
  $team = $owner->currentTeam;
  $team->users()->attach($member, ['role' => 'editor']);
  $this->actingAs($member);

  visit('/teams/'.$team->id)
    ->press('Leave')
    ->click('[data-slot="dialog-content"] button.bg-destructive')
    ->assertPathIs('/dashboard');

  expect($team->fresh()->users()->where('users.id', $member->id)->exists())->toBeFalse();
});

it('forbids a non-owner member from removing another member via a direct request', function (): void {
  $owner = User::factory()->withPersonalTeam()->create();
  $memberA = User::factory()->withPersonalTeam()->create();
  $memberB = User::factory()->withPersonalTeam()->create();
  $team = Team::factory()->create([
    'user_id' => $owner->id,
    'personal_team' => false,
  ]);
  $team->users()->attach($memberA, ['role' => 'editor']);
  $team->users()->attach($memberB, ['role' => 'editor']);
  $this->actingAs($memberA);

  // memberA is neither the owner nor memberB, so RemoveTeamMember's authorize guard
  // rejects the attempt (AuthorizationException -> 403); memberB stays on the team.
  $status = visit('/dashboard')->script(teamApiRequest('DELETE', '/teams/'.$team->id.'/members/'.$memberB->id));

  expect((int) $status)->toBe(403);
  expect($team->fresh()->users()->where('users.id', $memberB->id)->exists())->toBeTrue();
});

it('prevents the owner from leaving a team they created', function (): void {
  $owner = User::factory()->withPersonalTeam()->create();
  $member = User::factory()->withPersonalTeam()->create();
  $team = Team::factory()->create([
    'user_id' => $owner->id,
    'personal_team' => false,
  ]);
  $team->users()->attach($member, ['role' => 'editor']);
  $this->actingAs($owner);

  // The owner passes the removeTeamMember gate but RemoveTeamMember blocks self-removal
  // of the creator with a validation error (422); the team and its owner are untouched.
  $status = visit('/dashboard')->script(teamApiRequest('DELETE', '/teams/'.$team->id.'/members/'.$owner->id));

  expect((int) $status)->toBe(422);
  expect(Team::find($team->id))->not->toBeNull();
  expect((string) $team->fresh()->user_id)->toBe((string) $owner->id);
});

it('forbids a non-owner member from adding a member via a direct request', function (): void {
  $owner = User::factory()->withPersonalTeam()->create();
  $member = User::factory()->withPersonalTeam()->create();
  $team = Team::factory()->create([
    'user_id' => $owner->id,
    'personal_team' => false,
  ]);
  $team->users()->attach($member, ['role' => 'editor']);
  $this->actingAs($member);

  $status = visit('/dashboard')->script(teamApiRequest('POST', '/teams/'.$team->id.'/members', [
    'email' => 'intruder@example.com',
    'role' => 'admin',
  ]));

  expect((int) $status)->toBe(403);
  expect($team->fresh()->teamInvitations()->where('email', 'intruder@example.com')->exists())->toBeFalse();
});

it('forbids a non-owner member from changing another member role via a direct request', function (): void {
  $owner = User::factory()->withPersonalTeam()->create();
  $memberA = User::factory()->withPersonalTeam()->create();
  $memberB = User::factory()->withPersonalTeam()->create();
  $team = Team::factory()->create([
    'user_id' => $owner->id,
    'personal_team' => false,
  ]);
  $team->users()->attach($memberA, ['role' => 'editor']);
  $team->users()->attach($memberB, ['role' => 'editor']);
  $this->actingAs($memberA);

  $status = visit('/dashboard')->script(teamApiRequest('PUT', '/teams/'.$team->id.'/members/'.$memberB->id, [
    'role' => 'admin',
  ]));

  expect((int) $status)->toBe(403);
  expect($team->fresh()->users()->find($memberB->id)->membership->role)->toBe('editor');
});
