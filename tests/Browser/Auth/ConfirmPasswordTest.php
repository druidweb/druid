<?php

declare(strict_types=1);

use App\Models\User;

it('shows the confirm-password screen to an authenticated user', function (): void {
  $user = User::factory()->withPersonalTeam()->create();
  $this->actingAs($user);

  visit('/user/confirm-password')
    ->assertPathIs('/user/confirm-password')
    ->assertSee('Confirm your password')
    ->assertSee('This is a secure area of the application')
    ->assertPresent('#password')
    ->assertPresent('@confirm-password-button')
    ->assertNoJavaScriptErrors();
});

it('confirms with the correct password and proceeds to the intended page', function (): void {
  $user = User::factory()->withPersonalTeam()->create();
  $this->actingAs($user);

  // The correct password confirms the secure area; Fortify sets the
  // auth.password_confirmed_at session flag and redirects to the intended
  // destination (defaults to /dashboard). Landing there proves the
  // ConfirmPassword action returned true and the redirect fired.
  visit('/user/confirm-password')
    ->fill('password', 'password')
    ->click('@confirm-password-button')
    ->assertPathIs('/dashboard')
    ->assertNoJavaScriptErrors();

  // Session state is now confirmed — the status endpoint reflects it.
  visit('/user/confirmed-password-status')
    ->assertSee('"confirmed":true');
});

it('rejects the wrong password and does not confirm', function (): void {
  $user = User::factory()->withPersonalTeam()->create();
  $this->actingAs($user);

  // The wrong password must not confirm: the error surfaces and the user
  // stays on the confirm-password screen.
  visit('/user/confirm-password')
    ->fill('password', 'not-the-password')
    ->click('@confirm-password-button')
    ->assertSee('provided password')
    ->assertPathIs('/user/confirm-password');

  // And the session is still unconfirmed.
  visit('/user/confirmed-password-status')
    ->assertSee('"confirmed":false');
});
