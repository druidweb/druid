<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::get('/', \App\Http\Controllers\IndexController::class)->name('home');

Route::get('dashboard', \App\Http\Controllers\DashboardController::class)->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/settings.php';
//require __DIR__.'/auth.php';
require __DIR__.'/teams.php';
