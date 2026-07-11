<?php

declare(strict_types=1);

use App\Models\User;

it('renders the login page without errors', function (): void {
  $page = visit('/login');

  $page->assertPresent('email')
    ->assertPresent('password')
    ->assertNoJavaScriptErrors();
});

it('logs a user in and redirects to the dashboard', function (): void {
  $user = User::factory()->withPersonalTeam()->create();

  $page = visit('/login');

  $page->fill('email', $user->email)
    ->fill('password', 'password')
    ->click('@login-button')
    ->assertPathIs('/dashboard');
});

it('rejects invalid credentials and stays on the login page', function (): void {
  $user = User::factory()->withPersonalTeam()->create();

  $page = visit('/login');

  $page->fill('email', $user->email)
    ->fill('password', 'wrong-password')
    ->click('@login-button')
    ->assertPathIs('/login')
    ->assertSee('These credentials do not match our records.');
});
