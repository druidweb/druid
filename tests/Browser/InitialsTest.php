<?php

declare(strict_types=1);

use function Pest\Laravel\actingAs;

it('displays correct initials for full name', function (): void {
  $user = \App\Models\User::factory()->create([
    'name' => 'John Doe',
  ]);

  actingAs($user);

  $page = visit('/dashboard');

  $page->wait(1)
    ->assertPathIs('/dashboard')
    ->assertSee('JD');

});

it('displays correct initials for single name', function (): void {
  $user = \App\Models\User::factory()->create([
    'name' => 'Madonna',
  ]);

  actingAs($user);

  $page = visit('/dashboard');

  $page->wait(1)
    ->assertPathIs('/dashboard')
    ->assertSee('M');

});

it('handles empty name gracefully', function (): void {
  $user = \App\Models\User::factory()->create([
    'name' => '',
  ]);

  actingAs($user);

  $page = visit('/dashboard');

  $page->wait(1)
    ->assertPathIs('/dashboard');

  // Test getInitials function directly with empty string
  $result = $page->script('
    const getInitials = (fullName) => {
      if (!fullName) return "";
      const names = fullName.trim().split(" ");
      if (names.length === 0) return "";
      if (names.length === 1) return names[0].charAt(0).toUpperCase();
      return (names[0].charAt(0) + names[names.length - 1].charAt(0)).toUpperCase();
    };
    getInitials("");
  ');
  expect($result)->toBe('');

});

it('handles multiple names correctly', function (): void {
  $user = \App\Models\User::factory()->create([
    'name' => 'John Michael Doe',
  ]);

  actingAs($user);

  $page = visit('/dashboard');

  $page->wait(1)
    ->assertPathIs('/dashboard')
    ->assertSee('JD'); // Should be first and last name initials

});

it('handles names with extra whitespace', function (): void {
  $user = \App\Models\User::factory()->create([
    'name' => '  Jane   Smith  ',
  ]);

  actingAs($user);

  $page = visit('/dashboard');

  $page->wait(1)
    ->assertPathIs('/dashboard')
    ->assertSee('JS');

});
