<?php

namespace App\Http\Controllers\Teams;

use App\Contracts\AddsTeamMembers;
use App\Teams\Teams;
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
   * Accept a team invitation.
   *
   * @param  int  $invitationId
   * @return RedirectResponse
   */
  public function accept(Request $request, $invitationId)
  {
    $model = Teams::teamInvitationModel();

    $invitation = $model::whereKey($invitationId)->firstOrFail();

    app(AddsTeamMembers::class)->add(
      $invitation->team->owner,
      $invitation->team,
      $invitation->email,
      $invitation->role
    );

    $invitation->delete();

    return redirect(config('fortify.home'))->banner(
      __('Great! You have accepted the invitation to join the :team team.', ['team' => $invitation->team->name]),
    );
  }

  /**
   * Cancel the given team invitation.
   *
   * @param  int  $invitationId
   */
  public function destroy(Request $request, $invitationId): RedirectResponse
  {
    $model = Teams::teamInvitationModel();

    $invitation = $model::whereKey($invitationId)->firstOrFail();

    throw_unless(Gate::forUser($request->user())->check('removeTeamMember', $invitation->team), AuthorizationException::class);

    $invitation->delete();

    return back(303);
  }
}
