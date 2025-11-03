<?php

namespace App\Http\Controllers;

use App\Teams\Teams;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Inertia\Response;

class TermsOfServiceController extends Controller
{
  /**
   * Show the terms of service for the application.
   *
   * @return Response
   */
  public function show(Request $request)
  {
    $termsFile = Teams::localizedMarkdownPath('terms.md');

    return Inertia::render('TermsOfService', [
      'terms' => Str::markdown(file_get_contents($termsFile)),
    ]);
  }
}
