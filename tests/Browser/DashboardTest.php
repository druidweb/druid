<?php

use App\Models\User;
use Laravel\Dusk\Browser;

test('authenticated user can access dashboard', function () {
  $user = User::factory()->create();

  $this->browse(function (Browser $browser) use ($user) {
    $browser->loginAs($user)
      ->visit('/dashboard')
      ->assertSee('Dashboard')
      ->assertSee($user->name)
      ->assertPresent('[data-testid="app-layout"]')
      ->assertPresent('[data-testid="placeholder-pattern"]');
  });
});

test('dashboard displays correct breadcrumbs', function () {
  $user = User::factory()->create();

  $this->browse(function (Browser $browser) use ($user) {
    $browser->loginAs($user)
      ->visit('/dashboard')
      ->assertSee('Dashboard')
      ->assertPresent('[data-testid="breadcrumb"]');
  });
});

test('dashboard has proper navigation menu', function () {
  $user = User::factory()->create();

  $this->browse(function (Browser $browser) use ($user) {
    $browser->loginAs($user)
      ->visit('/dashboard')
      ->assertSeeLink('Dashboard')
      ->assertSeeLink('Settings')
      ->assertPresent('button[data-testid="user-menu-trigger"]');
  });
});

test('user can navigate to settings from dashboard', function () {
  $user = User::factory()->create();

  $this->browse(function (Browser $browser) use ($user) {
    $browser->loginAs($user)
      ->visit('/dashboard')
      ->clickLink('Settings')
      ->waitForRoute('profile.edit')
      ->assertRouteIs('profile.edit')
      ->assertSee('Profile');
  });
});

test('user menu dropdown works correctly', function () {
  $user = User::factory()->create();

  $this->browse(function (Browser $browser) use ($user) {
    $browser->loginAs($user)
      ->visit('/dashboard')
      ->click('button[data-testid="user-menu-trigger"]')
      ->waitFor('button[data-testid="logout-button"]')
      ->assertSee('Profile')
      ->assertSee('Settings')
      ->assertSee('Sign out');
  });
});

test('dashboard is responsive', function () {
  $user = User::factory()->create();

  $this->browse(function (Browser $browser) use ($user) {
    $browser->loginAs($user)
      ->visit('/dashboard')
      ->resize(768, 1024) // Tablet size
      ->assertSee('Dashboard')
      ->resize(375, 667) // Mobile size
      ->assertSee('Dashboard')
      ->resize(1920, 1080); // Desktop size
  });
});

test('dashboard placeholder patterns are displayed', function () {
  $user = User::factory()->create();

  $this->browse(function (Browser $browser) use ($user) {
    $browser->loginAs($user)
      ->visit('/dashboard')
      ->assertPresent('[data-testid="placeholder-pattern"]')
      ->assertElementCount('[data-testid="placeholder-pattern"]', 4);
  });
});

test('dashboard grid layout is correct', function () {
  $user = User::factory()->create();

  $this->browse(function (Browser $browser) use ($user) {
    $browser->loginAs($user)
      ->visit('/dashboard')
      ->assertPresent('.grid.auto-rows-min.gap-4.md\\:grid-cols-3');
  });
});
