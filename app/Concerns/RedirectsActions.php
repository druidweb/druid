<?php

declare(strict_types=1);

namespace App\Concerns;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;

trait RedirectsActions
{
  /**
   * Get the redirect response for the given action.
   */
  public function redirectPath(object $action): Response|Redirector|RedirectResponse
  {
    // An action may choose where to land by defining redirectTo(); otherwise go home.
    $response = method_exists($action, 'redirectTo') ? $action->redirectTo() : config('fortify.home');

    /** @var string|null $redirectPath */
    $redirectPath = $response;

    return $response instanceof Response ? $response : redirect($redirectPath);
  }
}
