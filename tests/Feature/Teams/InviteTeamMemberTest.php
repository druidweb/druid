<?php

declare(strict_types=1);

use App\Mail\TeamInvitation;
use App\Models\User;
use App\Teams\Features;
use Illuminate\Support\Facades\Mail;

it('can invite team members to team', function () {
  if (! Features::sendsTeamInvitations()) {
    $this->markTestSkipped('Team invitations not enabled.');
  }

  Mail::fake();

  $this->actingAs($user = User::factory()->withPersonalTeam()->create());

  $this->post('/teams/'.$user->currentTeam->id.'/members', [
    'email' => 'test@example.com',
    'role' => 'admin',
  ]);

  Mail::assertSent(TeamInvitation::class);

  $this->assertCount(1, $user->currentTeam->fresh()->teamInvitations);
});

it('can cancel team member invitations', function () {
  if (! Features::sendsTeamInvitations()) {
    $this->markTestSkipped('Team invitations not enabled.');
  }

  Mail::fake();

  $this->actingAs($user = User::factory()->withPersonalTeam()->create());

  $invitation = $user->currentTeam->teamInvitations()->create([
    'email' => 'test@example.com',
    'role' => 'admin',
  ]);

  $this->delete('/team-invitations/'.$invitation->id);

  $this->assertCount(0, $user->currentTeam->fresh()->teamInvitations);
});
