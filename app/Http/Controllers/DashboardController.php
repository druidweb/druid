<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Inertia\Inertia;
use Spatie\RouteDiscovery\Attributes\Route;

final class DashboardController
{
  /**
   * Show the dashboard.
   */
  #[Route('dashboard', name: 'dashboard', middleware: ['auth', 'verified'])]
  public function __invoke()
  {
    return Inertia::render('Dashboard');
  }
}
