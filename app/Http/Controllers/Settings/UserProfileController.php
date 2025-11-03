<?php

namespace App\Http\Controllers\Settings;

use App\Teams\Agent;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Inertia\Response;

class UserProfileController extends Controller
{
  /**
   * Show the general profile settings screen.
   *
   * @return Response
   */
  public function show(Request $request)
  {
    return Inertia::render($request, 'Profile/Show', [
      'sessions' => $this->sessions($request)->all(),
    ]);
  }

  /**
   * Get the current sessions.
   *
   * @return Collection
   */
  public function sessions(Request $request)
  {
    if (config('session.driver') !== 'database') {
      return collect();
    }

    return collect(
      DB::connection(config('session.connection'))->table(config('session.table', 'sessions'))
        ->where('user_id', $request->user()->getAuthIdentifier())
        ->orderBy('last_activity', 'desc')
        ->get()
    )->map(function ($session) use ($request) {
      $agent = $this->createAgent($session);

      return (object) [
        'agent' => [
          'is_desktop' => $agent->isDesktop(),
          'platform' => $agent->platform(),
          'browser' => $agent->browser(),
        ],
        'ip_address' => $session->ip_address,
        'is_current_device' => $session->id === $request->session()->getId(),
        'last_active' => Date::createFromTimestamp($session->last_activity)->diffForHumans(),
      ];
    });
  }

  /**
   * Create a new agent instance from the given session.
   *
   * @param  mixed  $session
   * @return Agent
   */
  protected function createAgent($session)
  {
    return tap(new Agent, fn ($agent): string => $agent->setUserAgent($session->user_agent));
  }
}
