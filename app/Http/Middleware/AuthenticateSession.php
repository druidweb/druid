<?php

namespace App\Http\Middleware;

use Illuminate\Contracts\Auth\Factory;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Session\Middleware\AuthenticateSession as BaseAuthenticateSession;

class AuthenticateSession extends BaseAuthenticateSession
{
  /**
   * Get the guard instance that should be used by the middleware.
   *
   * @return Factory|Guard
   */
  protected function guard()
  {
    return app(StatefulGuard::class);
  }
}
