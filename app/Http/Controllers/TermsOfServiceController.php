<?php

namespace App\Http\Controllers;

use App\Teams\Teams;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Inertia\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class TermsOfServiceController implements HasMiddleware
{
  /**
   * Get the middleware that should be assigned to the controller.
   *
   * @return array<int, Middleware|string>
   */
  public static function middleware(): array
  {
    return [
      function (Request $request, callable $next): HttpResponse {
        if (! Teams::hasTermsAndPrivacyPolicyFeature()) {
          abort(HttpResponse::HTTP_NOT_FOUND);
        }

        return $next($request);
      },
    ];
  }

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
