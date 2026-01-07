<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;

class AddingTeam
{
  use Dispatchable;

  /**
   * Create a new event instance.
   */
  public function __construct(
    /**
     * The team owner.
     */
    public mixed $owner
  ) {}
}
