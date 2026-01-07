<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;
use Laravel\Fortify\Features;

final class IndexController
{
  /**
   * Show the welcome page.
   */
  public function __invoke(): Response
  {
    return Inertia::render('Welcome', [
      'canRegister' => Features::enabled(Features::registration()),
    ]);
  }
}
