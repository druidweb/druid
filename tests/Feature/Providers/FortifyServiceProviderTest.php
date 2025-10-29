<?php

declare(strict_types=1);

use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Laravel\Fortify\Fortify;

test('two factor rate limiter is configured', function (): void {
  $request = Request::create('/two-factor-challenge', 'POST');
  $session = app(Session::class);
  $session->put('login.id', 'test-login-id');
  $request->setLaravelSession($session);

  $limiter = RateLimiter::limiter('two-factor');

  expect($limiter)->not->toBeNull();

  $limit = $limiter($request);

  expect($limit->maxAttempts)->toBe(5);
  expect($limit->decaySeconds)->toBe(60);
  expect($limit->key)->toBe('test-login-id');
});

test('login rate limiter is configured', function (): void {
  $request = Request::create('/login', 'POST');
  $request->merge([
    Fortify::username() => 'test@example.com',
  ]);
  $request->server->set('REMOTE_ADDR', '192.168.1.1');

  $limiter = RateLimiter::limiter('login');

  expect($limiter)->not->toBeNull();

  $limit = $limiter($request);

  expect($limit->maxAttempts)->toBe(5);
  expect($limit->decaySeconds)->toBe(60);
});

test('login rate limiter handles special characters in email', function (): void {
  $request = Request::create('/login', 'POST');
  $request->merge([
    Fortify::username() => 'tëst@éxample.com',
  ]);
  $request->server->set('REMOTE_ADDR', '192.168.1.1');

  $limiter = RateLimiter::limiter('login');
  $limit = $limiter($request);

  expect($limit)->not->toBeNull();
  expect($limit->maxAttempts)->toBe(5);
});
