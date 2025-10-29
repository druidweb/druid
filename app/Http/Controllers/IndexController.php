<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;
use Laravel\Fortify\Features;
use Spatie\RouteDiscovery\Attributes\Route;

final class IndexController
{
  /**
   * Show the welcome page.
   */
  #[Route('/', name: 'home')]
  public function __invoke(): Response
  {
    return Inertia::render('Welcome', [
      'canRegister' => Features::enabled(Features::registration()),
    ]);
  }
}
