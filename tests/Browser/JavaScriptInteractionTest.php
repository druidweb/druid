<?php

use App\Models\User;
use Laravel\Dusk\Browser;

test('page loads without JavaScript errors', function () {
  $this->browse(function (Browser $browser) {
    $browser->visit('/')
      ->assertMissing('.js-error')
      ->script('return window.console.error.length === undefined || window.console.error.length === 0;');
  });
});

test('Vue.js application mounts correctly', function () {
  $this->browse(function (Browser $browser) {
    $browser->visit('/')
      ->waitFor('[data-v-app]', 5)
      ->assertPresent('[data-v-app]');
  });
});

test('Inertia.js navigation works', function () {
  $user = User::factory()->create();

  $this->browse(function (Browser $browser) use ($user) {
    $browser->loginAs($user)
      ->visit('/dashboard')
      ->clickLink('Settings')
      ->waitForRoute('profile.edit')
      ->assertRouteIs('profile.edit')
      ->assertSee('Profile')
      // Verify it's an SPA navigation (no full page reload)
      ->script('return window.performance.navigation.type === 0;');
  });
});

test('form submissions use AJAX', function () {
  $user = User::factory()->create();

  $this->browse(function (Browser $browser) use ($user) {
    $browser->loginAs($user)
      ->visit('/settings/profile')
      ->type('name', 'Updated Name')
      ->press('Save')
      ->waitForText('Saved.')
      ->assertSee('Saved.')
      // Verify no page reload occurred
      ->script('return window.performance.navigation.type === 0;');
  });
});

test('loading states are displayed during form submission', function () {
  $user = User::factory()->create();

  $this->browse(function (Browser $browser) use ($user) {
    $browser->loginAs($user)
      ->visit('/settings/profile')
      ->type('name', 'Updated Name')
      ->press('Save')
      ->assertPresent('svg[data-testid="loading-spinner"]')
      ->waitForText('Saved.')
      ->assertMissing('svg[data-testid="loading-spinner"]');
  });
});

test('client-side validation works', function () {
  $this->browse(function (Browser $browser) {
    $browser->visit('/register')
      ->type('email', 'invalid-email')
      ->type('password', '123')
      ->type('password_confirmation', '456')
      ->press('Register')
      ->waitForText('The email field must be a valid email address.')
      ->assertSee('The email field must be a valid email address.')
      ->assertSee('The password field must be at least 8 characters.');
  });
});

test('dynamic content updates work', function () {
  $user = User::factory()->create();

  $this->browse(function (Browser $browser) use ($user) {
    $browser->loginAs($user)
      ->visit('/dashboard')
      ->click('button[data-testid="user-menu-trigger"]')
      ->waitFor('button[data-testid="logout-button"]')
      ->assertVisible('button[data-testid="logout-button"]')
      ->click('button[data-testid="user-menu-trigger"]')
      ->waitUntilMissing('button[data-testid="logout-button"]')
      ->assertMissing('button[data-testid="logout-button"]');
  });
});

test('theme switching works with JavaScript', function () {
  $user = User::factory()->create();

  $this->browse(function (Browser $browser) use ($user) {
    $browser->loginAs($user)
      ->visit('/dashboard')
      ->assertAttribute('html', 'class', 'light')
      ->click('button[data-testid="theme-toggle"]')
      ->pause(500)
      ->assertAttribute('html', 'class', 'dark')
      ->click('button[data-testid="theme-toggle"]')
      ->pause(500)
      ->assertAttribute('html', 'class', 'light');
  });
});

test('search functionality works with JavaScript', function () {
  $user = User::factory()->create();

  $this->browse(function (Browser $browser) use ($user) {
    $browser->loginAs($user)
      ->visit('/dashboard')
      ->type('input[data-testid="search-input"]', 'test query')
      ->keys('input[data-testid="search-input"]', '{enter}')
      ->pause(1000)
      ->assertPresent('[data-testid="search-results"]');
  });
});

test('modal dialogs work with JavaScript', function () {
  $user = User::factory()->create();

  $this->browse(function (Browser $browser) use ($user) {
    $browser->loginAs($user)
      ->visit('/settings/profile')
      ->scrollIntoView('button[data-testid="delete-account-button"]')
      ->click('button[data-testid="delete-account-button"]')
      ->waitFor('[data-testid="delete-account-modal"]')
      ->assertVisible('[data-testid="delete-account-modal"]')
      ->click('button[data-testid="cancel-delete-button"]')
      ->waitUntilMissing('[data-testid="delete-account-modal"]')
      ->assertMissing('[data-testid="delete-account-modal"]');
  });
});

test('keyboard shortcuts work', function () {
  $user = User::factory()->create();

  $this->browse(function (Browser $browser) use ($user) {
    $browser->loginAs($user)
      ->visit('/dashboard')
      ->keys('body', ['{ctrl}', 'k']) // Ctrl+K for search
      ->waitFor('input[data-testid="search-input"]:focus')
      ->assertFocused('input[data-testid="search-input"]');
  });
});

test('error handling works correctly', function () {
  $this->browse(function (Browser $browser) {
    $browser->visit('/login')
      ->type('email', 'nonexistent@example.com')
      ->type('password', 'wrongpassword')
      ->press('Sign in')
      ->waitForText('These credentials do not match our records.')
      ->assertSee('These credentials do not match our records.')
      ->assertRouteIs('login');
  });
});

test('progress indicators work', function () {
  $user = User::factory()->create();

  $this->browse(function (Browser $browser) use ($user) {
    $browser->loginAs($user)
      ->visit('/settings/profile')
      ->type('name', 'Updated Name')
      ->press('Save')
      ->assertPresent('[data-testid="progress-indicator"]')
      ->waitForText('Saved.')
      ->assertMissing('[data-testid="progress-indicator"]');
  });
});

test('auto-save functionality works', function () {
  $user = User::factory()->create();

  $this->browse(function (Browser $browser) use ($user) {
    $browser->loginAs($user)
      ->visit('/settings/profile')
      ->type('name', 'Auto Saved Name')
      ->pause(3000) // Wait for auto-save
      ->assertSee('Auto-saved')
      ->refresh()
      ->assertInputValue('name', 'Auto Saved Name');
  });
});

test('real-time updates work', function () {
  $user = User::factory()->create();

  $this->browse(function (Browser $browser) use ($user) {
    $browser->loginAs($user)
      ->visit('/dashboard')
      ->assertPresent('[data-testid="real-time-indicator"]')
      ->waitFor('[data-testid="connection-status"]')
      ->assertSeeIn('[data-testid="connection-status"]', 'Connected');
  });
});

test('infinite scroll works', function () {
  $user = User::factory()->create();

  $this->browse(function (Browser $browser) use ($user) {
    $browser->loginAs($user)
      ->visit('/dashboard')
      ->scrollToBottom()
      ->pause(1000)
      ->assertPresent('[data-testid="load-more-content"]');
  });
});
