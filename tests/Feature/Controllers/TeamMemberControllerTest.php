<?php

declare(strict_types=1);

use App\Models\TeamInvitation;
use App\Models\User;
use App\Teams\Features;

it('can add a team member via invitation', function (): void {
  $owner = User::factory()->withPersonalTeam()->create();
  $newMember = User::factory()->create();

  $this->actingAs($owner);

  $response = $this->post('/teams/'.$owner->currentTeam->id.'/members', [
    'email' => $newMember->email,
    'role' => 'admin',
  ]);

  $response->assertRedirect();

  if (Features::sendsTeamInvitations()) {
    // Invitation should be created
    expect(TeamInvitation::query()->where('email', $newMember->email)->exists())->toBeTrue();
  } else {
    // User should be added directly
    expect($owner->currentTeam->fresh()->hasUser($newMember))->toBeTrue();
  }
});

it('can update team member role', function (): void {
  $owner = User::factory()->withPersonalTeam()->create();
  $member = User::factory()->create();

  $owner->currentTeam->users()->attach($member, ['role' => 'editor']);

  $this->actingAs($owner);

  $response = $this->put('/teams/'.$owner->currentTeam->id.'/members/'.$member->id, [
    'role' => 'admin',
  ]);

  $response->assertRedirect();
  expect($owner->currentTeam->fresh()->users->find($member->id)->membership->role)->toBe('admin');
});

it('can remove a team member', function (): void {
  $owner = User::factory()->withPersonalTeam()->create();
  $member = User::factory()->create();

  $owner->currentTeam->users()->attach($member, ['role' => 'admin']);

  $this->actingAs($owner);

  $response = $this->delete('/teams/'.$owner->currentTeam->id.'/members/'.$member->id);

  $response->assertRedirect();
  expect($owner->currentTeam->fresh()->hasUser($member))->toBeFalse();
});

it('allows member to leave team', function (): void {
  $owner = User::factory()->withPersonalTeam()->create();
  $member = User::factory()->withPersonalTeam()->create();

  $owner->currentTeam->users()->attach($member, ['role' => 'admin']);

  $this->actingAs($member);

  $response = $this->delete('/teams/'.$owner->currentTeam->id.'/members/'.$member->id);

  // When user removes themselves, they should be redirected to fortify.home
  $response->assertRedirect();
  expect($owner->currentTeam->fresh()->hasUser($member))->toBeFalse();
});

it('can add a team member directly when invitations are disabled', function (): void {
  // Temporarily disable team invitations
  config(['teams-options.teams.invitations' => false]);

  $owner = User::factory()->withPersonalTeam()->create();
  $newMember = User::factory()->create();

  $this->actingAs($owner);

  $response = $this->post('/teams/'.$owner->currentTeam->id.'/members', [
    'email' => $newMember->email,
    'role' => 'admin',
  ]);

  $response->assertRedirect();
  expect($owner->currentTeam->fresh()->hasUser($newMember))->toBeTrue();
});
