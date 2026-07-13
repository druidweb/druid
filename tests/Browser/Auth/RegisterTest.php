<?php

declare(strict_types=1);

use App\Models\User;

it('renders the registration page', function (): void {
  visit('/register')
    ->assertPresent('name')
    ->assertPresent('email')
    ->assertPresent('password')
    ->assertNoJavaScriptErrors();
});

it('registers a new user', function (): void {
  visit('/register')
    ->fill('name', 'Ada Lovelace')
    ->fill('email', 'ada@example.com')
    ->fill('password', 'password')
    ->fill('password_confirmation', 'password')
    ->check('terms')
    ->click('@register-user-button')
    ->assertPathIsNot('/register');

  expect(User::query()->where('email', 'ada@example.com')->exists())->toBeTrue();
});
