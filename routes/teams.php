<?php

use App\Http\Controllers\Api\ApiTokenController;
use App\Http\Controllers\PrivacyPolicyController;
use App\Http\Controllers\Settings\CurrentUserController;
use App\Http\Controllers\Settings\OtherBrowserSessionsController;
use App\Http\Controllers\Settings\ProfilePhotoController;
use App\Http\Controllers\Settings\UserProfileController;
use App\Http\Controllers\Teams\CurrentTeamController;
use App\Http\Controllers\Teams\TeamController;
use App\Http\Controllers\Teams\TeamInvitationController;
use App\Http\Controllers\Teams\TeamMemberController;
use App\Http\Controllers\TermsOfServiceController;
use App\Teams\Teams;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => config('teams.middleware', ['web'])], function (): void {
  if (Teams::hasTermsAndPrivacyPolicyFeature()) {
    Route::get('/terms-of-service', [TermsOfServiceController::class, 'show'])->name('terms.show');
    Route::get('/privacy-policy', [PrivacyPolicyController::class, 'show'])->name('policy.show');
  }

  $authMiddleware = config('teams.guard')
      ? 'auth:'.config('teams.guard')
      : 'auth';

  $authSessionMiddleware = config('teams.auth_session', false)
      ? config('teams.auth_session')
      : null;

  Route::group(['middleware' => array_values(array_filter([$authMiddleware, $authSessionMiddleware]))], function (): void {
    // User & Profile...
    Route::get('/user/profile', [UserProfileController::class, 'show'])
      ->name('profile.show');

    Route::delete('/user/other-browser-sessions', [OtherBrowserSessionsController::class, 'destroy'])
      ->name('other-browser-sessions.destroy');

    Route::delete('/user/profile-photo', [ProfilePhotoController::class, 'destroy'])
      ->name('current-user-photo.destroy');

    if (Teams::hasAccountDeletionFeatures()) {
      Route::delete('/user', [CurrentUserController::class, 'destroy'])
        ->name('current-user.destroy');
    }

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
