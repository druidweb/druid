<?php

namespace App\Http\Controllers\Teams;

use App\Contracts\AddsTeamMembers;
use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\User;
use App\Teams\Teams;
use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class TeamInvitationController implements HasMiddleware
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
   * Accept a team invitation.
   */
  public function accept(Request $request, int $invitationId): RedirectResponse
  {
    /** @var class-string<TeamInvitation> $model */
    $model = Teams::teamInvitationModel();

    /** @var TeamInvitation $invitation */
    $invitation = $model::query()->whereKey($invitationId)->firstOrFail();

    /** @var AddsTeamMembers $adder */
    $adder = resolve(AddsTeamMembers::class);

    /** @var Team $team */
    $team = $invitation->team;

    /** @var User $owner */
    $owner = $team->owner;

    $adder->add(
      $owner,
      $team,
      $invitation->email,
      $invitation->role
    );

    $invitation->delete();

    /** @var string $home */
    $home = config('fortify.home', '/');

    $request->session()->flash('flash.banner', __('Great! You have accepted the invitation to join the :team team.', ['team' => $team->name]));

    return redirect()->to($home);
  }

  /**
   * Cancel the given team invitation.
   */
  public function destroy(Request $request, int $invitationId): RedirectResponse
  {
    /** @var class-string<TeamInvitation> $model */
    $model = Teams::teamInvitationModel();

    /** @var TeamInvitation $invitation */
    $invitation = $model::query()->whereKey($invitationId)->firstOrFail();

    /** @var User $user */
    $user = $request->user();

    /** @var Team $team */
    $team = $invitation->team;

    throw_unless(Gate::forUser($user)->check('removeTeamMember', $team), AuthorizationException::class);

    $invitation->delete();

    return back(303);
  }
}
