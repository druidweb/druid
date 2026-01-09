<?php

declare(strict_types=1);

namespace App\Http\Controllers\Teams;

use App\Actions\Teams\UpdateTeamMemberRole;
use App\Contracts\AddsTeamMembers;
use App\Contracts\InvitesTeamMembers;
use App\Contracts\RemovesTeamMembers;
use App\Models\Team;
use App\Models\User;
use App\Teams\Features;
use App\Teams\Teams;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Redirector;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

final class TeamMemberController implements HasMiddleware
{
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
   * Add a new team member to a team.
   */
  public function store(Request $request, int $teamId): RedirectResponse
  {
    /** @var Team $team */
    $team = Teams::newTeamModel()->findOrFail($teamId);

    /** @var User $user */
    $user = $request->user();

    /** @var string $email */
    $email = $request->email ?: '';

    /** @var string|null $role */
    $role = $request->role;

    if (Features::sendsTeamInvitations()) {
      /** @var InvitesTeamMembers $inviter */
      $inviter = resolve(InvitesTeamMembers::class);
      $inviter->invite(
        $user,
        $team,
        $email,
        $role
      );
    } else {
      /** @var AddsTeamMembers $adder */
      $adder = resolve(AddsTeamMembers::class);
      $adder->add(
        $user,
        $team,
        $email,
        $role
      );
    }

    return back(303);
  }

  /**
   * Update the given team member's role.
   */
  public function update(Request $request, int $teamId, int $userId): RedirectResponse
  {
    /** @var User $user */
    $user = $request->user();

    /** @var Team $team */
    $team = Teams::newTeamModel()->findOrFail($teamId);

    /** @var UpdateTeamMemberRole $updater */
    $updater = resolve(UpdateTeamMemberRole::class);

    /** @var string $role */
    $role = $request->role;

    $updater->update(
      $user,
      $team,
      $userId,
      $role
    );

    return back(303);
  }

  /**
   * Remove the given user from the given team.
   */
  public function destroy(Request $request, int $teamId, int $userId): Redirector|RedirectResponse
  {
    /** @var Team $team */
    $team = Teams::newTeamModel()->findOrFail($teamId);

    /** @var User $currentUser */
    $currentUser = $request->user();

    /** @var User $user */
    $user = Teams::findUserByIdOrFail($userId);

    /** @var RemovesTeamMembers $remover */
    $remover = resolve(RemovesTeamMembers::class);
    $remover->remove(
      $currentUser,
      $team,
      $user
    );

    if ((int) $currentUser->id === (int) $user->id) {
      /** @var string|null $home */
      $home = config('fortify.home');

      return redirect($home);
    }

    return back(303);
  }
}
