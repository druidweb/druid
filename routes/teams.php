<?php

use App\Http\Controllers\Api\ApiTokenController;
use App\Http\Controllers\PrivacyPolicyController;
use App\Http\Controllers\Settings\OtherBrowserSessionsController;
use App\Http\Controllers\Settings\ProfilePhotoController;
use App\Http\Controllers\Teams\CurrentTeamController;
use App\Http\Controllers\Teams\TeamController;
use App\Http\Controllers\Teams\TeamInvitationController;
use App\Http\Controllers\Teams\TeamMemberController;
use App\Http\Controllers\TermsOfServiceController;
use App\Teams\Teams;
use Illuminate\Support\Facades\Route;

// Only register routes if Teams route registration is enabled
if (! Teams::$registersRoutes) {
  return;
}

Route::group(['middleware' => config('teams.middleware', ['web'])], function (): void {
  if (Teams::hasTermsAndPrivacyPolicyFeature()) {
    Route::get('/terms-of-service', [TermsOfServiceController::class, 'show'])->name('terms.show');
    Route::get('/privacy-policy', [PrivacyPolicyController::class, 'show'])->name('policy.show');
  }

  /** @var string|null $guard */
  $guard = config('teams.guard');

  $authMiddleware = $guard
      ? 'auth:'.$guard
      : 'auth';

  /** @var string|false $authSession */
  $authSession = config('teams.auth_session', false);

  $authSessionMiddleware = $authSession !== false
      ? $authSession
      : null;

  /** @var array<int, string> $middleware */
  $middleware = array_filter([$authMiddleware, $authSessionMiddleware]);

  Route::group(['middleware' => $middleware], function (): void {
    // Profile photo management...
    if (Teams::managesProfilePhotos()) {
      Route::delete('/user/profile-photo', [ProfilePhotoController::class, 'destroy'])
        ->name('current-user-photo.destroy');
    }

    // Browser sessions management...
    Route::get('/user/other-browser-sessions', [OtherBrowserSessionsController::class, 'index'])
      ->name('other-browser-sessions.index');
    Route::delete('/user/other-browser-sessions', [OtherBrowserSessionsController::class, 'destroy'])
      ->name('other-browser-sessions.destroy');

    Route::group(['middleware' => 'verified'], function (): void {
      // API...
      if (Teams::hasApiFeatures()) {
        Route::get('/user/api-tokens', [ApiTokenController::class, 'index'])->name('api-tokens.index');
        Route::post('/user/api-tokens', [ApiTokenController::class, 'store'])->name('api-tokens.store');
        Route::put('/user/api-tokens/{token}', [ApiTokenController::class, 'update'])->name('api-tokens.update');
        Route::delete('/user/api-tokens/{token}', [ApiTokenController::class, 'destroy'])->name('api-tokens.destroy');
      }

      // Teams...
      if (Teams::hasTeamFeatures()) {
        Route::get('/teams/create', [TeamController::class, 'create'])->name('teams.create');
        Route::post('/teams', [TeamController::class, 'store'])->name('teams.store');
        Route::get('/teams/{team}', [TeamController::class, 'show'])->name('teams.show');
        Route::put('/teams/{team}', [TeamController::class, 'update'])->name('teams.update');
        Route::delete('/teams/{team}', [TeamController::class, 'destroy'])->name('teams.destroy');
        Route::put('/current-team', [CurrentTeamController::class, 'update'])->name('current-team.update');
        Route::post('/teams/{team}/members', [TeamMemberController::class, 'store'])->name('team-members.store');
        Route::put('/teams/{team}/members/{user}', [TeamMemberController::class, 'update'])->name('team-members.update');
        Route::delete('/teams/{team}/members/{user}', [TeamMemberController::class, 'destroy'])->name('team-members.destroy');

        Route::get('/team-invitations/{invitation}', [TeamInvitationController::class, 'accept'])
          ->middleware(['signed'])
          ->name('team-invitations.accept');

        Route::delete('/team-invitations/{invitation}', [TeamInvitationController::class, 'destroy'])
          ->name('team-invitations.destroy');
      }
    });
  });
});
