<?php

declare(strict_types=1);

use App\Models\TeamInvitation;
use App\Models\User;
use Illuminate\Support\Facades\URL;

it('can accept team invitation with signed url', function (): void {
  $owner = User::factory()->withPersonalTeam()->create();
  $invitedUser = User::factory()->create();

  $invitation = $owner->currentTeam->teamInvitations()->create([
    'email' => $invitedUser->email,
    'role' => 'admin',
  ]);

  $this->actingAs($invitedUser);

  // Use a signed URL as required by the route
  $signedUrl = URL::signedRoute('team-invitations.accept', ['invitation' => $invitation->id]);

  $response = $this->get($signedUrl);

  $response->assertRedirect();
  expect($owner->currentTeam->fresh()->hasUser($invitedUser))->toBeTrue();
  expect(TeamInvitation::query()->find($invitation->id))->toBeNull();
});

it('can cancel team invitation', function (): void {
  $owner = User::factory()->withPersonalTeam()->create();

  $invitation = $owner->currentTeam->teamInvitations()->create([
    'email' => 'test@example.com',
    'role' => 'admin',
  ]);

  $this->actingAs($owner);

  $response = $this->delete('/team-invitations/'.$invitation->id);

  $response->assertRedirect();
  expect(TeamInvitation::query()->find($invitation->id))->toBeNull();
});

it('prevents non-owner from canceling invitation', function (): void {
  $owner = User::factory()->withPersonalTeam()->create();
  $member = User::factory()->create();

  $owner->currentTeam->users()->attach($member, ['role' => 'editor']);

  $invitation = $owner->currentTeam->teamInvitations()->create([
    'email' => 'test@example.com',
    'role' => 'admin',
  ]);

  $this->actingAs($member);

  $response = $this->delete('/team-invitations/'.$invitation->id);

  $response->assertForbidden();
  expect(TeamInvitation::query()->find($invitation->id))->not->toBeNull();
});
