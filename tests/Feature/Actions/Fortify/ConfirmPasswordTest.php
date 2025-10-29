<?php

declare(strict_types=1);

use App\Actions\Fortify\ConfirmPassword;
use App\Models\User;

test('confirms password for authenticated user with correct password', function (): void {
  $user = User::factory()->create([
    'password' => bcrypt('password'),
  ]);

  $this->actingAs($user);

  $confirmPassword = new ConfirmPassword;
  $result = $confirmPassword('password');

  expect($result)->toBeTrue();
});

test('rejects incorrect password for authenticated user', function (): void {
  $user = User::factory()->create([
    'password' => bcrypt('password'),
  ]);

  $this->actingAs($user);

  $confirmPassword = new ConfirmPassword;
  $result = $confirmPassword('wrong-password');

  expect($result)->toBeFalse();
});

test('returns false when no user is authenticated', function (): void {
  $confirmPassword = new ConfirmPassword;
  $result = $confirmPassword('password');

  expect($result)->toBeFalse();
});

test('returns false when password is null', function (): void {
  $user = User::factory()->create([
    'password' => bcrypt('password'),
  ]);

  $this->actingAs($user);

  $confirmPassword = new ConfirmPassword;
  $result = $confirmPassword(null);

  expect($result)->toBeFalse();
});
