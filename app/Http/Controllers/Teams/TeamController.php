<?php

namespace App\Http\Controllers\Teams;

use App\Actions\Teams\ValidateTeamDeletion;
use App\Concerns\RedirectsActions;
use App\Contracts\CreatesTeams;
use App\Contracts\DeletesTeams;
use App\Contracts\UpdatesTeamNames;
use App\Models\Team;
use App\Models\User;
use App\Teams\Teams;
use Closure;
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
   * @return array<int, Middleware|Closure|string>
   */
  public static function middleware(): array
  {
    return [
      static function (Request $request, callable $next): HttpResponse {
        abort_unless(Teams::hasTeamFeatures(), HttpResponse::HTTP_FORBIDDEN);

        /** @var HttpResponse $response */
        $response = $next($request);

        return $response;
      },
    ];
  }

  /**
   * Show the team management screen.
   */
  public function show(Request $request, int $teamId): \Inertia\Response
  {
    /** @var Team $team */
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
   */
  public function create(Request $request): \Inertia\Response
  {
    Gate::authorize('create', Teams::newTeamModel());

    return Inertia::render('teams/Create');
  }

  /**
   * Create a new team.
   */
  public function store(Request $request): Response|Redirector|RedirectResponse
  {
    /** @var CreatesTeams $creator */
    $creator = resolve(CreatesTeams::class);

    /** @var User $user */
    $user = $request->user();

    /** @var array<string, mixed> $input */
    $input = $request->all();
    $creator->create($user, $input);

    return $this->redirectPath($creator);
  }

  /**
   * Update the given team's name.
   */
  public function update(Request $request, int $teamId): RedirectResponse
  {
    /** @var Team $team */
    $team = Teams::newTeamModel()->findOrFail($teamId);

    /** @var User $user */
    $user = $request->user();

    /** @var UpdatesTeamNames $updater */
    $updater = resolve(UpdatesTeamNames::class);

    /** @var array<string, mixed> $input */
    $input = $request->all();
    $updater->update($user, $team, $input);

    return back(303);
  }

  /**
   * Delete the given team.
   */
  public function destroy(Request $request, int $teamId): Response|Redirector|RedirectResponse
  {
    /** @var Team $team */
    $team = Teams::newTeamModel()->findOrFail($teamId);

    /** @var User $user */
    $user = $request->user();

    /** @var ValidateTeamDeletion $validator */
    $validator = resolve(ValidateTeamDeletion::class);
    $validator->validate($user, $team);

    /** @var DeletesTeams $deleter */
    $deleter = resolve(DeletesTeams::class);

    $deleter->delete($team);

    return $this->redirectPath($deleter);
  }
}
