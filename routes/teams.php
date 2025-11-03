<?php

use App\Teams\Teams;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => config('teams.middleware', ['web'])], function () {
  if (Teams::hasTermsAndPrivacyPolicyFeature()) {
    Route::get('/terms-of-service', [\App\Http\Controllers\TermsOfServiceController::class, 'show'])->name('terms.show');
    Route::get('/privacy-policy', [\App\Http\Controllers\PrivacyPolicyController::class, 'show'])->name('policy.show');
  }

  $authMiddleware = config('teams.guard')
      ? 'auth:'.config('teams.guard')
      : 'auth';

  $authSessionMiddleware = config('teams.auth_session', false)
      ? config('teams.auth_session')
      : null;

  Route::group(['middleware' => array_values(array_filter([$authMiddleware, $authSessionMiddleware]))], function () {
    // User & Profile...
    Route::get('/user/profile', [\App\Http\Controllers\Settings\UserProfileController::class, 'show'])
      ->name('profile.show');

    Route::delete('/user/other-browser-sessions', [\App\Http\Controllers\Settings\OtherBrowserSessionsController::class, 'destroy'])
      ->name('other-browser-sessions.destroy');

    Route::delete('/user/profile-photo', [\App\Http\Controllers\Settings\ProfilePhotoController::class, 'destroy'])
      ->name('current-user-photo.destroy');

    if (Teams::hasAccountDeletionFeatures()) {
      Route::delete('/user', [\App\Http\Controllers\Settings\CurrentUserController::class, 'destroy'])
        ->name('current-user.destroy');
    }

    Route::group(['middleware' => 'verified'], function () {
      // API...
      if (Teams::hasApiFeatures()) {
        Route::get('/user/api-tokens', [\App\Http\Controllers\Api\ApiTokenController::class, 'index'])->name('api-tokens.index');
        Route::post('/user/api-tokens', [\App\Http\Controllers\Api\ApiTokenController::class, 'store'])->name('api-tokens.store');
        Route::put('/user/api-tokens/{token}', [\App\Http\Controllers\Api\ApiTokenController::class, 'update'])->name('api-tokens.update');
        Route::delete('/user/api-tokens/{token}', [\App\Http\Controllers\Api\ApiTokenController::class, 'destroy'])->name('api-tokens.destroy');
      }

      // Teams...
      if (Teams::hasTeamFeatures()) {
        Route::get('/teams/create', [\App\Http\Controllers\Teams\TeamController::class, 'create'])->name('teams.create');
        Route::post('/teams', [\App\Http\Controllers\Teams\TeamController::class, 'store'])->name('teams.store');
        Route::get('/teams/{team}', [\App\Http\Controllers\Teams\TeamController::class, 'show'])->name('teams.show');
        Route::put('/teams/{team}', [\App\Http\Controllers\Teams\TeamController::class, 'update'])->name('teams.update');
        Route::delete('/teams/{team}', [\App\Http\Controllers\Teams\TeamController::class, 'destroy'])->name('teams.destroy');
        Route::put('/current-team', [\App\Http\Controllers\Teams\CurrentTeamController::class, 'update'])->name('current-team.update');
        Route::post('/teams/{team}/members', [\App\Http\Controllers\Teams\TeamMemberController::class, 'store'])->name('team-members.store');
        Route::put('/teams/{team}/members/{user}', [\App\Http\Controllers\Teams\TeamMemberController::class, 'update'])->name('team-members.update');
        Route::delete('/teams/{team}/members/{user}', [\App\Http\Controllers\Teams\TeamMemberController::class, 'destroy'])->name('team-members.destroy');

        Route::get('/team-invitations/{invitation}', [\App\Http\Controllers\Teams\TeamInvitationController::class, 'accept'])
          ->middleware(['signed'])
          ->name('team-invitations.accept');

        Route::delete('/team-invitations/{invitation}', [\App\Http\Controllers\Teams\TeamInvitationController::class, 'destroy'])
          ->name('team-invitations.destroy');
      }
    });
  });
});
