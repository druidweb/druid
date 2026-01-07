<?php

namespace App\Actions\Teams;

use App\Contracts\CreatesTeams;
use App\Events\AddingTeam;
use App\Models\Team;
use App\Models\User;
use App\Teams\Teams;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class CreateTeam implements CreatesTeams
{
  /**
   * Validate and create a new team for the given user.
   *
   * @param  array<string, string>  $input
   */
  public function create(User $user, array $input): Team
  {
    Gate::forUser($user)->authorize('create', Teams::newTeamModel());

    Validator::make($input, [
      'name' => ['required', 'string', 'max:255'],
    ])->validateWithBag('createTeam');

    event(new AddingTeam($user));

    $user->switchTeam($team = $user->ownedTeams()->create([
      'name' => $input['name'],
      'personal_team' => false,
    ]));

    return $team;
  }
}
