<?php

declare(strict_types=1);

use App\Rules\OwnerRole;
use App\Rules\Role;

it('validates valid role', function (): void {
  $rule = new Role;
  $failed = false;
  $failMessage = '';

  $rule->validate('role', 'admin', function (string $message) use (&$failed, &$failMessage): void {
    $failed = true;
    $failMessage = $message;
  });

  expect($failed)->toBeFalse();
});

it('fails validation for invalid role', function (): void {
  $rule = new Role;
  $failed = false;
  $failMessage = '';

  $rule->validate('role', 'invalid-role', function (string $message) use (&$failed, &$failMessage): void {
    $failed = true;
    $failMessage = $message;
  });

  expect($failed)->toBeTrue();
  expect($failMessage)->toBe('The :attribute must be a valid role.');
});

it('creates owner role with correct properties', function (): void {
  $role = new OwnerRole;

  expect($role->key)->toBe('owner');
  expect($role->name)->toBe('Owner');
  expect($role->permissions)->toBe(['*']);
});
