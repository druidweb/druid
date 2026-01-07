<?php

declare(strict_types=1);

use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\Settings\TwoFactorAuthenticationController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware('auth')->group(function (): void {
  Route::redirect('settings', 'settings/profile');

  Route::get('settings/profile', [ProfileController::class, 'edit'])->name('profile.edit');
  Route::patch('settings/profile', [ProfileController::class, 'update'])->name('profile.update');
  Route::delete('settings/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

  Route::get('settings/password', fn () => Inertia::render('settings/Password'))->name('password.edit');

  Route::get('settings/two-factor', [TwoFactorAuthenticationController::class, 'show'])->name('two-factor.show');

  Route::get('settings/appearance', fn () => Inertia::render('settings/Appearance'))->name('appearance.edit');
});
