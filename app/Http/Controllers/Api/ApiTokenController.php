<?php

namespace App\Http\Controllers\Api;

use App\Teams\Teams;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;

class ApiTokenController extends Controller
{
  /**
   * Show the user API token screen.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Inertia\Response
   */
  public function index(Request $request)
  {
    return Inertia::render($request, 'API/Index', [
      'tokens' => $request->user()->tokens->map(function ($token) {
        return $token->toArray() + [
          'last_used_ago' => optional($token->last_used_at)->diffForHumans(),
        ];
      }),
      'availablePermissions' => Teams::$permissions,
      'defaultPermissions' => Teams::$defaultPermissions,
    ]);
  }

  /**
   * Create a new API token.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\RedirectResponse
   */
  public function store(Request $request)
  {
    $request->validate([
      'name' => ['required', 'string', 'max:255'],
    ]);

    $token = $request->user()->createToken(
      $request->name,
      Teams::validPermissions($request->input('permissions', []))
    );

    return back()->with('flash', [
      'token' => explode('|', $token->plainTextToken, 2)[1],
    ]);
  }

  /**
   * Update the given API token's permissions.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  string  $tokenId
   * @return \Illuminate\Http\RedirectResponse
   */
  public function update(Request $request, $tokenId)
  {
    $request->validate([
      'permissions' => 'array',
      'permissions.*' => 'string',
    ]);

    $token = $request->user()->tokens()->where('id', $tokenId)->firstOrFail();

    $token->forceFill([
      'abilities' => Teams::validPermissions($request->input('permissions', [])),
    ])->save();

    return back(303);
  }

  /**
   * Delete the given API token.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  string  $tokenId
   * @return \Illuminate\Http\RedirectResponse
   */
  public function destroy(Request $request, $tokenId)
  {
    $request->user()->tokens()->where('id', $tokenId)->first()->delete();

    return back(303);
  }
}
