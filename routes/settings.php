<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware('auth')->group(function (): void {
  Route::redirect('settings', 'settings/profile');

  Route::get('settings/profile', [\App\Http\Controllers\Settings\ProfileController::class, 'edit'])->name('profile.edit');
  Route::patch('settings/profile', [\App\Http\Controllers\Settings\ProfileController::class, 'update'])->name('profile.update');
  Route::delete('settings/profile', [\App\Http\Controllers\Settings\ProfileController::class, 'destroy'])->name('profile.destroy');

  Route::get('settings/password', fn () => Inertia::render('settings/Password'))->name('password.edit');

  Route::get('settings/two-factor', [\App\Http\Controllers\Settings\TwoFactorAuthenticationController::class, 'show'])->name('two-factor.show');

  Route::get('settings/appearance', fn () => Inertia::render('settings/Appearance'))->name('appearance.edit');
});
