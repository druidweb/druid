<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Hash;

it('updates the password', function (): void {
  $user = User::factory()->withPersonalTeam()->create();
  $this->actingAs($user);

  visit('/settings/password')
    ->fill('current_password', 'password')
    ->fill('password', 'new-password-123')
    ->fill('password_confirmation', 'new-password-123')
    ->click('@update-password-button')
    ->assertSee('Saved.');

  expect(Hash::check('new-password-123', $user->fresh()->password))->toBeTrue();
});

it('rejects a wrong current password', function (): void {
  $user = User::factory()->withPersonalTeam()->create();
  $this->actingAs($user);

  visit('/settings/password')
    ->fill('current_password', 'not-the-password')
    ->fill('password', 'new-password-123')
    ->fill('password_confirmation', 'new-password-123')
    ->click('@update-password-button')
    ->assertSee('does not match your current password');
});
