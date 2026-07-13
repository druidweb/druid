<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

it('renders the forgot password page', function (): void {
  visit('/forgot-password')
    ->assertPresent('email')
    ->assertNoJavaScriptErrors();
});

it('accepts a password reset request', function (): void {
  $user = User::factory()->withPersonalTeam()->create();

  visit('/forgot-password')
    ->fill('email', $user->email)
    ->click('@email-password-reset-link-button')
    ->assertPathIs('/forgot-password')
    ->assertSee('password reset link');
});

it('resets the password with a valid token', function (): void {
  $user = User::factory()->withPersonalTeam()->create();
  $token = Password::createToken($user);

  visit('/reset-password/'.$token.'?email='.urlencode((string) $user->email))
    ->fill('password', 'new-password-123')
    ->fill('password_confirmation', 'new-password-123')
    ->click('@reset-password-button')
    ->assertPathIs('/login');

  expect(Hash::check('new-password-123', $user->fresh()->password))->toBeTrue();
});

it('rejects a password reset that is too short', function (): void {
  $user = User::factory()->withPersonalTeam()->create();
  $token = Password::createToken($user);

  visit('/reset-password/'.$token.'?email='.urlencode((string) $user->email))
    ->fill('password', 'short')
    ->fill('password_confirmation', 'short')
    ->click('@reset-password-button')
    ->assertPathContains('/reset-password')
    ->assertSee('at least 8 characters');

  expect(Hash::check('short', $user->fresh()->password))->toBeFalse();
});
