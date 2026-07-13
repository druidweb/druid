<?php

declare(strict_types=1);

use App\Models\TeamInvitation;
use App\Models\User;
use Illuminate\Support\Facades\URL;

it('accepts a team invitation from a signed url', function (): void {
  $owner = User::factory()->withPersonalTeam()->create();
  $invited = User::factory()->withPersonalTeam()->create();
  $team = $owner->currentTeam;

  $invitation = $team->teamInvitations()->create([
    'email' => $invited->email,
    'role' => 'admin',
  ]);

  $this->actingAs($invited);

  // The accept route is protected by the "signed" middleware; the browser server
  // forces the URL generator origin to its own host, so this signature validates.
  $signedUrl = URL::signedRoute('team-invitations.accept', ['invitation' => $invitation->id]);

  visit($signedUrl)
    ->assertPathIs('/dashboard');

  expect($team->fresh()->hasUser($invited))->toBeTrue();
  expect(TeamInvitation::query()->find($invitation->id))->toBeNull();
});

it('cancels a pending team invitation', function (): void {
  $owner = User::factory()->withPersonalTeam()->create();
  $team = $owner->currentTeam;

  $invitation = $team->teamInvitations()->create([
    'email' => 'pending@example.com',
    'role' => 'editor',
  ]);

  $this->actingAs($owner);

  visit('/teams/'.$team->id)
    ->assertSee('pending@example.com')
    ->press('Cancel')
    ->assertDontSee('pending@example.com');

  expect(TeamInvitation::query()->find($invitation->id))->toBeNull();
});
