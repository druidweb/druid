<?php

use App\Models\User;
use Laravel\Dusk\Browser;

test('guest navigation works correctly', function () {
  $this->browse(function (Browser $browser) {
    $browser->visit('/')
      ->assertSeeLink('Login')
      ->assertSeeLink('Register')
      ->clickLink('Login')
      ->waitForRoute('login')
      ->assertRouteIs('login')
      ->assertSee('Sign in to your account');
  });
});

test('authenticated navigation works correctly', function () {
  $user = User::factory()->create();

  $this->browse(function (Browser $browser) use ($user) {
    $browser->loginAs($user)
      ->visit('/dashboard')
      ->assertSeeLink('Dashboard')
      ->assertSeeLink('Settings')
      ->assertPresent('button[data-testid="user-menu-trigger"]');
  });
});

test('breadcrumb navigation works', function () {
  $user = User::factory()->create();

  $this->browse(function (Browser $browser) use ($user) {
    $browser->loginAs($user)
      ->visit('/dashboard')
      ->assertPresent('[data-testid="breadcrumb"]')
      ->visit('/settings/profile')
      ->assertPresent('[data-testid="breadcrumb"]')
      ->assertSeeIn('[data-testid="breadcrumb"]', 'Settings')
      ->assertSeeIn('[data-testid="breadcrumb"]', 'Profile');
  });
});

test('mobile navigation menu works', function () {
  $user = User::factory()->create();

  $this->browse(function (Browser $browser) use ($user) {
    $browser->loginAs($user)
      ->resize(375, 667) // Mobile size
      ->visit('/dashboard')
      ->assertPresent('button[data-testid="mobile-menu-trigger"]')
      ->click('button[data-testid="mobile-menu-trigger"]')
      ->waitFor('[data-testid="mobile-menu"]')
      ->assertSeeIn('[data-testid="mobile-menu"]', 'Dashboard')
      ->assertSeeIn('[data-testid="mobile-menu"]', 'Settings');
  });
});

test('theme toggle works correctly', function () {
  $user = User::factory()->create();

  $this->browse(function (Browser $browser) use ($user) {
    $browser->loginAs($user)
      ->visit('/dashboard')
      ->assertPresent('button[data-testid="theme-toggle"]')
      ->click('button[data-testid="theme-toggle"]')
      ->pause(500) // Wait for theme transition
      ->assertAttribute('html', 'class', 'dark');
  });
});

test('search functionality works', function () {
  $user = User::factory()->create();

  $this->browse(function (Browser $browser) use ($user) {
    $browser->loginAs($user)
      ->visit('/dashboard')
      ->assertPresent('input[data-testid="search-input"]')
      ->type('input[data-testid="search-input"]', 'test search')
      ->keys('input[data-testid="search-input"]', '{enter}')
      ->pause(1000); // Wait for search results
  });
});

test('keyboard navigation works', function () {
  $user = User::factory()->create();

  $this->browse(function (Browser $browser) use ($user) {
    $browser->loginAs($user)
      ->visit('/dashboard')
      ->keys('body', '{tab}') // Tab through focusable elements
      ->keys('body', '{tab}')
      ->keys('body', '{tab}')
      ->assertFocused('button[data-testid="user-menu-trigger"]');
  });
});

test('back button navigation works', function () {
  $user = User::factory()->create();

  $this->browse(function (Browser $browser) use ($user) {
    $browser->loginAs($user)
      ->visit('/dashboard')
      ->clickLink('Settings')
      ->waitForRoute('profile.edit')
      ->back()
      ->waitForRoute('dashboard')
      ->assertRouteIs('dashboard');
  });
});

test('direct URL navigation works', function () {
  $user = User::factory()->create();

  $this->browse(function (Browser $browser) use ($user) {
    $browser->loginAs($user)
      ->visit('/settings/password')
      ->assertRouteIs('password.edit')
      ->assertSee('Update Password')
      ->visit('/settings/appearance')
      ->assertRouteIs('appearance')
      ->assertSee('Appearance');
  });
});

test('navigation preserves state', function () {
  $user = User::factory()->create();

  $this->browse(function (Browser $browser) use ($user) {
    $browser->loginAs($user)
      ->visit('/settings/profile')
      ->type('name', 'Test Name')
      ->clickLink('Password')
      ->waitForRoute('password.edit')
      ->clickLink('Profile')
      ->waitForRoute('profile.edit')
      ->assertInputValue('name', 'Test Name');
  });
});

test('error pages are accessible', function () {
  $this->browse(function (Browser $browser) {
    $browser->visit('/nonexistent-page')
      ->assertSee('404')
      ->assertSee('Page Not Found');
  });
});

test('navigation is accessible', function () {
  $user = User::factory()->create();

  $this->browse(function (Browser $browser) use ($user) {
    $browser->loginAs($user)
      ->visit('/dashboard')
      ->assertPresent('nav[role="navigation"]')
      ->assertPresent('button[aria-expanded]')
      ->assertPresent('a[aria-current]');
  });
});
