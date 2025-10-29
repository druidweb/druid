<?php

declare(strict_types=1);

\Illuminate\Support\Facades\Route::get('settings/appearance',
  fn () => \Inertia\Inertia::render('settings/Appearance'))
  ->middleware('auth')
  ->name('appearance.edit');

\Illuminate\Support\Facades\Route::redirect('settings', '/settings/profile')
  ->middleware('auth');

\Spatie\RouteDiscovery\Discovery\Discover::controllers()
  ->in(app_path('Http/Controllers'));
