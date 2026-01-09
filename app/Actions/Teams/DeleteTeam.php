<?php

declare(strict_types=1);

namespace App\Actions\Teams;

use App\Contracts\DeletesTeams;
use App\Models\Team;

final class DeleteTeam implements DeletesTeams
{
  /**
   * Delete the given team.
   */
  public function delete(Team $team): void
  {
    $team->purge();
  }
}
