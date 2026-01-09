<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Teams\Teams;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

final class ApiTokenController implements HasMiddleware
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
        abort_unless(Teams::hasApiFeatures(), HttpResponse::HTTP_FORBIDDEN);

        /** @var HttpResponse $response */
        $response = $next($request);

        return $response;
      },
    ];
  }

  /**
   * Show the user API token screen.
   */
  public function index(Request $request): Response
  {
    /** @var User $user */
    $user = $request->user();

    return Inertia::render('api/Index', [
      'tokens' => $user->tokens->map(fn (mixed $token): array => $token->toArray() + [
        'last_used_ago' => $token->last_used_at?->diffForHumans(),
      ]),
      'availablePermissions' => Teams::$permissions,
      'defaultPermissions' => Teams::$defaultPermissions,
    ]);
  }

  /**
   * Create a new API token.
   */
  public function store(Request $request): RedirectResponse
  {
    $request->validate([
      'name' => ['required', 'string', 'max:255'],
    ]);

    /** @var User $user */
    $user = $request->user();

    /** @var array<int|string, mixed> $permissions */
    $permissions = $request->input('permissions', []);

    /** @var string $name */
    $name = $request->name;

    $token = $user->createToken(
      $name,
      Teams::validPermissions($permissions)
    );

    return back()->with('flash', [
      'token' => explode('|', (string) $token->plainTextToken, 2)[1],
    ]);
  }

  /**
   * Update the given API token's permissions.
   */
  public function update(Request $request, string $tokenId): RedirectResponse
  {
    $request->validate([
      'permissions' => ['array'],
      'permissions.*' => ['string'],
    ]);

    /** @var User $user */
    $user = $request->user();

    /** @var PersonalAccessToken $token */
    $token = $user->tokens()->where('id', $tokenId)->firstOrFail();

    /** @var array<int|string, mixed> $permissions */
    $permissions = $request->input('permissions', []);

    $token->forceFill([
      'abilities' => Teams::validPermissions($permissions),
    ])->save();

    return back(303);
  }

  /**
   * Delete the given API token.
   */
  public function destroy(Request $request, string $tokenId): RedirectResponse
  {
    /** @var User $user */
    $user = $request->user();

    /** @var PersonalAccessToken|null $token */
    $token = $user->tokens()->where('id', $tokenId)->first();

    $token?->delete();

    return back(303);
  }
}
