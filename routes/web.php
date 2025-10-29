<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Spatie\RouteDiscovery\Discovery\Discover;

Route::get('settings/appearance',
  fn () => Inertia::render('settings/Appearance'))
  ->middleware('auth')
  ->name('appearance.edit');

Route::redirect('settings', '/settings/profile')
  ->middleware('auth');

Discover::controllers()
  ->in(app_path('Http/Controllers'));
