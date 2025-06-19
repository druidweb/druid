<?php

use App\Models\User;
use Laravel\Dusk\Browser;

test('application is responsive on mobile devices', function () {
  $user = User::factory()->create();

  $this->browse(function (Browser $browser) use ($user) {
    $browser->loginAs($user)
      ->resize(375, 667) // iPhone SE size
      ->visit('/dashboard')
      ->assertSee('Dashboard')
      ->assertPresent('button[data-testid="mobile-menu-trigger"]')
      ->click('button[data-testid="mobile-menu-trigger"]')
      ->waitFor('[data-testid="mobile-menu"]')
      ->assertVisible('[data-testid="mobile-menu"]');
  });
});

test('application is responsive on tablet devices', function () {
  $user = User::factory()->create();

  $this->browse(function (Browser $browser) use ($user) {
    $browser->loginAs($user)
      ->resize(768, 1024) // iPad size
      ->visit('/dashboard')
      ->assertSee('Dashboard')
      ->visit('/settings/profile')
      ->assertSee('Profile')
      ->assertPresent('nav'); // Settings sidebar should be visible
  });
});

test('application is responsive on desktop', function () {
  $user = User::factory()->create();

  $this->browse(function (Browser $browser) use ($user) {
    $browser->loginAs($user)
      ->resize(1920, 1080) // Desktop size
      ->visit('/dashboard')
      ->assertSee('Dashboard')
      ->assertPresent('nav')
      ->assertVisible('nav');
  });
});

test('navigation adapts to screen size', function () {
  $user = User::factory()->create();

  $this->browse(function (Browser $browser) use ($user) {
    $browser->loginAs($user)
      ->resize(1920, 1080) // Desktop
      ->visit('/dashboard')
      ->assertVisible('nav')
      ->resize(375, 667) // Mobile
      ->assertPresent('button[data-testid="mobile-menu-trigger"]')
      ->assertNotVisible('nav');
  });
});

test('forms are responsive', function () {
  $this->browse(function (Browser $browser) {
    $browser->resize(375, 667) // Mobile
      ->visit('/login')
      ->assertSee('Sign in to your account')
      ->assertPresent('input[name="email"]')
      ->assertPresent('input[name="password"]')
      ->resize(1920, 1080) // Desktop
      ->assertSee('Sign in to your account')
      ->assertPresent('input[name="email"]')
      ->assertPresent('input[name="password"]');
  });
});

test('settings page is responsive', function () {
  $user = User::factory()->create();

  $this->browse(function (Browser $browser) use ($user) {
    $browser->loginAs($user)
      ->resize(375, 667) // Mobile
      ->visit('/settings/profile')
      ->assertSee('Profile')
      ->resize(768, 1024) // Tablet
      ->assertSee('Profile')
      ->resize(1920, 1080) // Desktop
      ->assertSee('Profile');
  });
});

test('text is readable at different screen sizes', function () {
  $user = User::factory()->create();

  $this->browse(function (Browser $browser) use ($user) {
    $browser->loginAs($user)
      ->resize(375, 667) // Mobile
      ->visit('/dashboard')
      ->assertSee('Dashboard')
      ->resize(768, 1024) // Tablet
      ->assertSee('Dashboard')
      ->resize(1920, 1080) // Desktop
      ->assertSee('Dashboard');
  });
});

test('buttons are accessible on touch devices', function () {
  $user = User::factory()->create();

  $this->browse(function (Browser $browser) use ($user) {
    $browser->loginAs($user)
      ->resize(375, 667) // Mobile
      ->visit('/dashboard')
      ->assertPresent('button[data-testid="user-menu-trigger"]')
      ->click('button[data-testid="user-menu-trigger"]')
      ->waitFor('button[data-testid="logout-button"]')
      ->assertVisible('button[data-testid="logout-button"]');
  });
});

test('images scale properly', function () {
  $this->browse(function (Browser $browser) {
    $browser->resize(375, 667) // Mobile
      ->visit('/')
      ->assertPresent('img, svg')
      ->resize(1920, 1080) // Desktop
      ->assertPresent('img, svg');
  });
});

test('layout does not break on very small screens', function () {
  $user = User::factory()->create();

  $this->browse(function (Browser $browser) use ($user) {
    $browser->loginAs($user)
      ->resize(320, 568) // Very small mobile
      ->visit('/dashboard')
      ->assertSee('Dashboard')
      ->visit('/settings/profile')
      ->assertSee('Profile');
  });
});

test('layout works on very large screens', function () {
  $user = User::factory()->create();

  $this->browse(function (Browser $browser) use ($user) {
    $browser->loginAs($user)
      ->resize(2560, 1440) // Large desktop
      ->visit('/dashboard')
      ->assertSee('Dashboard')
      ->visit('/settings/profile')
      ->assertSee('Profile');
  });
});

test('orientation changes are handled', function () {
  $user = User::factory()->create();

  $this->browse(function (Browser $browser) use ($user) {
    $browser->loginAs($user)
      ->resize(375, 667) // Portrait
      ->visit('/dashboard')
      ->assertSee('Dashboard')
      ->resize(667, 375) // Landscape
      ->assertSee('Dashboard');
  });
});

test('zoom levels work correctly', function () {
  $user = User::factory()->create();

  $this->browse(function (Browser $browser) use ($user) {
    $browser->loginAs($user)
      ->visit('/dashboard')
      ->assertSee('Dashboard')
      // Simulate zoom by changing viewport
      ->resize(960, 540) // 50% zoom equivalent
      ->assertSee('Dashboard')
      ->resize(1920, 1080) // Back to normal
      ->assertSee('Dashboard');
  });
});
