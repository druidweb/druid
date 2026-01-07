<?php

namespace App\Http\Controllers\Teams;

use App\Actions\Teams\ValidateTeamDeletion;
use App\Concerns\RedirectsActions;
use App\Contracts\CreatesTeams;
use App\Contracts\DeletesTeams;
use App\Contracts\UpdatesTeamNames;
use App\Teams\Teams;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class TeamController implements HasMiddleware
{
  use RedirectsActions;

  /**
   * Get the middleware that should be assigned to the controller.
   *
   * @return array<int, Middleware|string>
   */
  public static function middleware(): array
  {
    return [
      function (Request $request, callable $next): HttpResponse {
        if (! Teams::hasTeamFeatures()) {
          abort(HttpResponse::HTTP_FORBIDDEN);
        }

        return $next($request);
      },
    ];
  }

  /**
   * Show the team management screen.
   *
   * @param  int  $teamId
   * @return \Inertia\Response
   */
  public function show(Request $request, $teamId)
  {
    $team = Teams::newTeamModel()->findOrFail($teamId);

    Gate::authorize('view', $team);

    return Inertia::render('teams/Show', [
      'team' => $team->load('owner', 'users', 'teamInvitations'),
      'availableRoles' => array_values(Teams::$roles),
      'availablePermissions' => Teams::$permissions,
      'defaultPermissions' => Teams::$defaultPermissions,
      'permissions' => [
        'canAddTeamMembers' => Gate::check('addTeamMember', $team),
        'canDeleteTeam' => Gate::check('delete', $team),
        'canRemoveTeamMembers' => Gate::check('removeTeamMember', $team),
        'canUpdateTeam' => Gate::check('update', $team),
        'canUpdateTeamMembers' => Gate::check('updateTeamMember', $team),
      ],
    ]);
  }

  /**
   * Show the team creation screen.
   *
   * @return \Inertia\Response
   */
  public function create(Request $request)
  {
    Gate::authorize('create', Teams::newTeamModel());

    return Inertia::render('teams/Create');
  }

  /**
   * Create a new team.
   *
   * @return RedirectResponse
   */
  public function store(Request $request): Response|Redirector|RedirectResponse
  {
    $creator = app(CreatesTeams::class);

    $creator->create($request->user(), $request->all());

    return $this->redirectPath($creator);
  }

  /**
   * Update the given team's name.
   *
   * @param  int  $teamId
   */
  public function update(Request $request, $teamId): RedirectResponse
  {
    $team = Teams::newTeamModel()->findOrFail($teamId);

    app(UpdatesTeamNames::class)->update($request->user(), $team, $request->all());

    return back(303);
  }

  /**
   * Delete the given team.
   *
   * @param  int  $teamId
   * @return RedirectResponse
   */
  public function destroy(Request $request, $teamId): Response|Redirector|RedirectResponse
  {
    $team = Teams::newTeamModel()->findOrFail($teamId);

    app(ValidateTeamDeletion::class)->validate($request->user(), $team);

    $deleter = app(DeletesTeams::class);

    $deleter->delete($team);

    return $this->redirectPath($deleter);
  }
}
