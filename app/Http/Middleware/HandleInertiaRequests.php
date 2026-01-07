<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Teams\Teams;
use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Session;
use Inertia\Middleware;
use Laravel\Fortify\Features;

class HandleInertiaRequests extends Middleware
{
  /**
   * The root template that's loaded on the first page visit.
   *
   * @see https://inertiajs.com/server-side-setup#root-template
   *
   * @var string
   */
  protected $rootView = 'app';

  /**
   * Determines the current asset version.
   *
   * @see https://inertiajs.com/asset-versioning
   */
  public function version(Request $request): ?string
  {
    return parent::version($request);
  }

  /**
   * Define the props that are shared by default.
   *
   * @see https://inertiajs.com/shared-data
   *
   * @return array<string, mixed>
   */
  public function share(Request $request): array
  {
    /** @var string $randomQuote */
    $randomQuote = Inspiring::quotes()->random();
    $quote = str($randomQuote)->explode('-');
    $message = $quote[0] ?? '';
    $author = $quote[1] ?? '';

    return array_filter([
      ...parent::share($request),
      'name' => config('app.name'),
      'quote' => ['message' => trim((string) $message), 'author' => trim((string) $author)],
      'teams' => function () use ($request): array {
        $user = $request->user();

        return [
          'canCreateTeams' => $user &&
                              Teams::userHasTeamFeatures($user) &&
                              Gate::forUser($user)->check('create', Teams::newTeamModel()),
          'canManageTwoFactorAuthentication' => Features::canManageTwoFactorAuthentication(),
          'canUpdatePassword' => Features::enabled(Features::updatePasswords()),
          'canUpdateProfileInformation' => Features::canUpdateProfileInformation(),
          'hasEmailVerification' => Features::enabled(Features::emailVerification()),
          'flash' => $request->session()->get('flash', []),
          'hasAccountDeletionFeatures' => Teams::hasAccountDeletionFeatures(),
          'hasApiFeatures' => Teams::hasApiFeatures(),
          'hasTeamFeatures' => Teams::hasTeamFeatures(),
          'hasTermsAndPrivacyPolicyFeature' => Teams::hasTermsAndPrivacyPolicyFeature(),
          'managesProfilePhotos' => Teams::managesProfilePhotos(),
        ];
      },
      'auth' => [
        'user' => function () use ($request) {
          if (! $user = $request->user()?->fresh()) {
            return;
          }

          $userHasTeamFeatures = Teams::userHasTeamFeatures($user);

          if ($userHasTeamFeatures) {
            $user->currentTeam;
          }

          return array_merge($user->toArray(), array_filter([
            'all_teams' => $userHasTeamFeatures ? $user->allTeams()->values() : null,
          ]), [
            'two_factor_enabled' => Features::enabled(Features::twoFactorAuthentication())
                && ! is_null($user->two_factor_secret),
          ]);
        },
      ],
      'errorBags' => fn () => collect(Session::get('errors')?->getBags() ?: [])->mapWithKeys(fn ($bag, $key): array => [$key => $bag->messages()])->all(),
      'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
    ]);
  }
}
