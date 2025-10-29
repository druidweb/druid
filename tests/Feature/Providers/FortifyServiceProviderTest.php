<?php

declare(strict_types=1);

use App\Models\User;
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

test('login view is configured', function (): void {
  $this->get('/login')
    ->assertInertia(fn ($page) => $page
      ->component('auth/Login')
      ->has('canResetPassword')
      ->has('canRegister'));
});

test('reset password view is configured', function (): void {
  $this->get('/reset-password/token123?email=test@example.com')
    ->assertInertia(fn ($page) => $page
      ->component('auth/ResetPassword')
      ->where('email', 'test@example.com')
      ->where('token', 'token123'));
});

test('request password reset link view is configured', function (): void {
  $this->get('/forgot-password')
    ->assertInertia(fn ($page) => $page
      ->component('auth/ForgotPassword'));
});

test('verify email view is configured', function (): void {
  $user = User::factory()->unverified()->create();

  $this->actingAs($user)
    ->get('/email/verify')
    ->assertInertia(fn ($page) => $page
      ->component('auth/VerifyEmail'));
});

test('register view is configured', function (): void {
  $this->get('/register')
    ->assertInertia(fn ($page) => $page
      ->component('auth/Register'));
});

test('two factor challenge view is configured', function (): void {
  $user = User::factory()->create();
  $user->forceFill([
    'two_factor_secret' => encrypt('secret'),
    'two_factor_recovery_codes' => encrypt(json_encode(['code1', 'code2'])),
  ])->save();

  $this->post('/login', [
    'email' => $user->email,
    'password' => 'password',
  ])->assertRedirect('/two-factor-challenge');

  $this->get('/two-factor-challenge')
    ->assertInertia(fn ($page) => $page
      ->component('auth/TwoFactorChallenge'));
});

test('confirm password view is configured', function (): void {
  $user = User::factory()->create();

  $this->actingAs($user)
    ->get('/user/confirm-password')
    ->assertInertia(fn ($page) => $page
      ->component('auth/ConfirmPassword')
      ->where('intended', '/dashboard'));
});

test('password confirmed response redirects to intended url', function (): void {
  $user = User::factory()->create();

  $this->actingAs($user)
    ->withSession(['url.intended' => '/settings/two-factor'])
    ->post('/user/confirm-password', [
      'password' => 'password',
    ])
    ->assertRedirect('/settings/two-factor');
});

test('password confirmed response defaults to dashboard', function (): void {
  $user = User::factory()->create();

  $this->actingAs($user)
    ->post('/user/confirm-password', [
      'password' => 'password',
    ])
    ->assertRedirect('/dashboard');
});
