<?php

declare(strict_types=1);

use App\Models\User;

it('displays the app header on dashboard', function (): void {
  $user = User::factory()->create();

  $page = $this->actingAs($user)->visit('/dashboard');

  $page->assertSee('Dashboard')
    ->assertNoJavascriptErrors();
});

it('opens mobile menu when clicking menu button', function (): void {
  $user = User::factory()->create();

  $page = $this->actingAs($user)->visit('/dashboard');

  // Click the mobile menu button (only visible on mobile)
  $page->click('text=Menu')
    ->assertNoJavascriptErrors();
})->skip('Mobile menu requires viewport resize');

it('displays navigation items in mobile menu', function (): void {
  $user = User::factory()->create();

  $page = $this->actingAs($user)->visit('/dashboard');

  $page->click('text=Menu')
    ->assertSee('Dashboard')
    ->assertSee('Profile')
    ->assertNoJavascriptErrors();
})->skip('Mobile menu requires viewport resize');

it('displays right nav items with external links', function (): void {
  $user = User::factory()->create();

  $page = $this->actingAs($user)->visit('/dashboard');

  // Check that external links are present
  $page->assertSee('Github Repo')
    ->assertSee('Documentation')
    ->assertNoJavascriptErrors();
});

it('shows tooltip on hover over right nav icons', function (): void {
  $user = User::factory()->create();

  $page = $this->actingAs($user)->visit('/dashboard');

  // Hover over the Github Repo link to show tooltip
  $page->hover('text=Github Repo')
    ->assertSee('Repository')
    ->assertNoJavascriptErrors();
});
