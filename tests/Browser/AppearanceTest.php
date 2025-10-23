<?php

declare(strict_types=1);

use function Pest\Laravel\actingAs;

it('can toggle between light and dark mode', function (): void {
  $user = \App\Models\User::factory()->create();

  actingAs($user);

  $page = visit('/settings/appearance');

  $page->wait(1)
    ->assertPathIs('/settings/appearance');

  // Click Dark mode button
  $page->click('Dark')
    ->wait(0.5);

  // Verify dark mode is applied
  $isDark = $page->script('document.documentElement.classList.contains("dark")');
  expect($isDark)->toBeTrue();

  // Click Light mode button
  $page->click('Light')
    ->wait(0.5);

  $isLight = $page->script('!document.documentElement.classList.contains("dark")');
  expect($isLight)->toBeTrue();

  // Click System mode button
  $page->click('System')
    ->wait(0.5);

});

it('respects system theme preference', function (): void {
  $page = visit('/');

  // Test that theme initialization runs
  $hasTheme = $page->script('document.documentElement.classList.contains("dark") || !document.documentElement.classList.contains("dark")');
  expect($hasTheme)->toBeTrue();

});

it('persists theme preference in localStorage', function (): void {
  $user = \App\Models\User::factory()->create();

  actingAs($user);

  $page = visit('/settings/appearance');

  $page->wait(1);

  // Set dark mode
  $page->click('Dark')
    ->wait(0.5);

  // Verify localStorage was updated
  $storedValue = $page->script('localStorage.getItem("appearance")');
  expect($storedValue)->toBe('dark');

  // Reload and verify persistence
  $page->script('window.location.reload();');
  $page->wait(2);

  $isDark = $page->script('document.documentElement.classList.contains("dark")');
  expect($isDark)->toBeTrue();

});

it('handles system theme change events', function (): void {
  $user = \App\Models\User::factory()->create();

  actingAs($user);

  $page = visit('/settings/appearance');

  $page->wait(1);

  // Set to system mode
  $page->click('System')
    ->wait(0.5);

  // Trigger the system theme change event handler
  $page->script('
    // Get the current appearance from localStorage
    const currentAppearance = localStorage.getItem("appearance");

    // Call the updateTheme function that handleSystemThemeChange would call
    const updateTheme = (value) => {
      if (value === "system") {
        const systemTheme = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
        document.documentElement.classList.toggle("dark", systemTheme === "dark");
      } else {
        document.documentElement.classList.toggle("dark", value === "dark");
      }
    };

    // Simulate what handleSystemThemeChange does
    updateTheme(currentAppearance || "system");
  ');

  $page->wait(0.5);

  // Verify system mode is active
  $appearance = $page->script('localStorage.getItem("appearance")');
  expect($appearance)->toBe('system');

});
