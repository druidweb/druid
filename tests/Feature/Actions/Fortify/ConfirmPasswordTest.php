<?php

declare(strict_types=1);

use App\Actions\Fortify\ConfirmPassword;
use App\Models\User;
use Laravel\Fortify\Fortify;

test('confirms the correct password', function (): void {
  $user = User::factory()->create(['password' => bcrypt('password')]);

  expect((new ConfirmPassword)($user, 'password'))->toBeTrue();
});

test('rejects an incorrect password', function (): void {
  $user = User::factory()->create(['password' => bcrypt('password')]);

  expect((new ConfirmPassword)($user, 'wrong-password'))->toBeFalse();
});

test('rejects a null password', function (): void {
  $user = User::factory()->create(['password' => bcrypt('password')]);

  expect((new ConfirmPassword)($user, null))->toBeFalse();
});

test('is registered as the fortify password confirmation callback', function (): void {
  expect(Fortify::$confirmPasswordsUsingCallback)->toBeInstanceOf(ConfirmPassword::class);
});

test('fortify confirm-password route delegates to the action', function (): void {
  $user = User::factory()->create(['password' => bcrypt('password')]);

  $this->actingAs($user)
    ->post('/user/confirm-password', ['password' => 'wrong-password'])
    ->assertSessionHasErrors('password');

  $this->actingAs($user)
    ->post('/user/confirm-password', ['password' => 'password'])
    ->assertSessionHasNoErrors();
});
