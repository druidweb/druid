<?php

namespace App\Events;

use App\Models\Team;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

abstract class TeamEvent
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  /**
   * Create a new event instance.
   *
   * @param  Team  $team
   * @return void
   */
  public function __construct(
    /**
     * The team instance.
     */
    public $team
  ) {}
}
