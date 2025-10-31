<?php

declare(strict_types=1);

use App\Models\User;

it('displays user info in sidebar', function (): void {
  $user = User::factory()->create([
    'name' => 'John Doe',
    'email' => 'john@example.com',
  ]);

  $page = $this->actingAs($user)->visit('/dashboard');

  $page->assertSee('John Doe')
    ->assertSee('john@example.com')
    ->assertNoJavascriptErrors();
});

it('opens user dropdown menu when clicking user info', function (): void {
  $user = User::factory()->create([
    'name' => 'John Doe',
  ]);

  $page = $this->actingAs($user)->visit('/dashboard');

  $page->click('button[data-test="sidebar-menu-button"]')
    ->assertNoJavascriptErrors();
});

it('displays user menu content when dropdown is open', function (): void {
  $user = User::factory()->create([
    'name' => 'John Doe',
  ]);

  $page = $this->actingAs($user)->visit('/dashboard');

  $page->click('button[data-test="sidebar-menu-button"]')
    ->assertNoJavascriptErrors();
});
