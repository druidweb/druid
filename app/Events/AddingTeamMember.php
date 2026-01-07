<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;

class AddingTeamMember
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
     * The team member being added.
     */
    public mixed $user
  ) {}
}
