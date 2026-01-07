<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;

class TeamMemberUpdated
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
     * The team member that was updated.
     */
    public mixed $user
  ) {}
}
