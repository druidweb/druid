<?php

declare(strict_types=1);

use App\Actions\Teams\InviteTeamMember;
use App\Events\InvitingTeamMember;
use App\Mail\TeamInvitation;
use App\Models\User;
use App\Teams\Teams;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;

beforeEach(function (): void {
  Event::fake([InvitingTeamMember::class]);
  Mail::fake();
});

it('skips role validation when inviting without registered roles', function (): void {
  $owner = User::factory()->withPersonalTeam()->create();

  // Clear registered roles so Teams::hasRoles() is false, restoring them afterwards
  $originalRoles = Teams::$roles;
  Teams::$roles = [];

  try {
    $action = new InviteTeamMember;
    $action->invite($owner, $owner->currentTeam, 'invitee@example.com');

    expect($owner->currentTeam->fresh()->teamInvitations)
      ->toHaveCount(1)
      ->first()->email->toBe('invitee@example.com');

    Event::assertDispatched(InvitingTeamMember::class);
    Mail::assertSent(TeamInvitation::class);
  } finally {
    Teams::$roles = $originalRoles;
  }
});
