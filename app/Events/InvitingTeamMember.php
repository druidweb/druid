<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;

class InvitingTeamMember
{
  use Dispatchable;

  /**
   * Create a new event instance.
   */
  public function __construct(
    /**
     * The team instance.
     */
    public mixed $team,
    /**
     * The email address of the invitee.
     */
    public mixed $email,
    /**
     * The role of the invitee.
     */
    public mixed $role
  ) {}
}
