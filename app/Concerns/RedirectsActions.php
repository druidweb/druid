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
    if (method_exists($action, 'redirectTo')) {
      $response = $action->redirectTo();
    } elseif (property_exists($action, 'redirectTo')) {
      /** @var object{redirectTo: string|null} $action */
      $response = $action->redirectTo;
    } else {
      $response = config('fortify.home');
    }

    /** @var string|null $redirectPath */
    $redirectPath = $response;

    return $response instanceof Response ? $response : redirect($redirectPath);
  }
}
