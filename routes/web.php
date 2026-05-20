<?php

declare(strict_types=1);

/* @chisel-registration */
use App\Http\Controllers\DashboardController;
/* @end-chisel-registration */
use App\Http\Controllers\IndexController;
use Illuminate\Support\Facades\Route;

Route::get('/', IndexController::class)->name('home');

/* @chisel-registration */
Route::get('dashboard', DashboardController::class)->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/settings.php';
//require __DIR__.'/auth.php';
require __DIR__.'/teams.php';
/* @end-chisel-registration */
