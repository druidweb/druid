<?php

namespace App\Http\Controllers;

use App\Teams\Teams;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class PrivacyPolicyController implements HasMiddleware
{
  /**
   * Get the middleware that should be assigned to the controller.
   *
   * @return array<int, Middleware|Closure|string>
   */
  public static function middleware(): array
  {
    return [
      static function (Request $request, callable $next): HttpResponse {
        abort_unless(Teams::hasTermsAndPrivacyPolicyFeature(), HttpResponse::HTTP_NOT_FOUND);

        /** @var HttpResponse $response */
        $response = $next($request);

        return $response;
      },
    ];
  }

  /**
   * Show the privacy policy for the application.
   */
  public function show(Request $request): Response
  {
    $policyFile = Teams::localizedMarkdownPath('policy.md');

    abort_if($policyFile === null, HttpResponse::HTTP_NOT_FOUND);

    $content = file_get_contents($policyFile);

    return Inertia::render('PrivacyPolicy', [
      'policy' => Str::markdown($content !== false ? $content : ''),
    ]);
  }
}
