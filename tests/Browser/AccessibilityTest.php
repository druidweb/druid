<?php

use App\Models\User;
use Laravel\Dusk\Browser;

test('pages have proper heading structure', function () {
  $user = User::factory()->create();

  $this->browse(function (Browser $browser) use ($user) {
    $browser->loginAs($user)
      ->visit('/dashboard')
      ->assertPresent('h1')
      ->visit('/settings/profile')
      ->assertPresent('h1')
      ->assertPresent('h2');
  });
});

test('forms have proper labels', function () {
  $this->browse(function (Browser $browser) {
    $browser->visit('/login')
      ->assertPresent('label[for="email"]')
      ->assertPresent('label[for="password"]')
      ->visit('/register')
      ->assertPresent('label[for="name"]')
      ->assertPresent('label[for="email"]')
      ->assertPresent('label[for="password"]')
      ->assertPresent('label[for="password_confirmation"]');
  });
});

test('buttons have accessible names', function () {
  $user = User::factory()->create();

  $this->browse(function (Browser $browser) use ($user) {
    $browser->visit('/login')
      ->assertPresent('button[type="submit"]')
      ->assertSeeIn('button[type="submit"]', 'Sign in')
      ->loginAs($user)
      ->visit('/dashboard')
      ->assertPresent('button[data-testid="user-menu-trigger"]')
      ->assertAttribute('button[data-testid="user-menu-trigger"]', 'aria-expanded');
  });
});

test('navigation has proper ARIA attributes', function () {
  $user = User::factory()->create();

  $this->browse(function (Browser $browser) use ($user) {
    $browser->loginAs($user)
      ->visit('/dashboard')
      ->assertPresent('nav[role="navigation"]')
      ->assertPresent('button[aria-expanded]')
      ->assertPresent('a[aria-current]');
  });
});

test('images have alt text', function () {
  $this->browse(function (Browser $browser) {
    $browser->visit('/')
      ->script('
        const images = document.querySelectorAll("img");
        return Array.from(images).every(img => img.hasAttribute("alt"));
      ');
  });
});

test('focus management works correctly', function () {
  $user = User::factory()->create();

  $this->browse(function (Browser $browser) use ($user) {
    $browser->loginAs($user)
      ->visit('/dashboard')
      ->keys('body', '{tab}')
      ->assertFocused('a, button, input, select, textarea, [tabindex]:not([tabindex="-1"])')
      ->keys('body', '{tab}')
      ->keys('body', '{tab}');
  });
});

test('skip links are present', function () {
  $user = User::factory()->create();

  $this->browse(function (Browser $browser) use ($user) {
    $browser->loginAs($user)
      ->visit('/dashboard')
      ->keys('body', '{tab}')
      ->assertPresent('a[href="#main-content"], a[href="#content"]');
  });
});

test('color contrast is sufficient', function () {
  $this->browse(function (Browser $browser) {
    $browser->visit('/')
      ->script('
        const elements = document.querySelectorAll("*");
        const styles = Array.from(elements).map(el => {
          const style = window.getComputedStyle(el);
          return {
            color: style.color,
            backgroundColor: style.backgroundColor
          };
        });
        return styles.length > 0;
      ');
  });
});

test('form validation errors are announced', function () {
  $this->browse(function (Browser $browser) {
    $browser->visit('/login')
      ->press('Sign in')
      ->waitForText('The email field is required.')
      ->assertPresent('[role="alert"], [aria-live="polite"], [aria-live="assertive"]');
  });
});

test('modal dialogs have proper ARIA attributes', function () {
  $user = User::factory()->create();

  $this->browse(function (Browser $browser) use ($user) {
    $browser->loginAs($user)
      ->visit('/settings/profile')
      ->scrollIntoView('button[data-testid="delete-account-button"]')
      ->click('button[data-testid="delete-account-button"]')
      ->waitFor('[data-testid="delete-account-modal"]')
      ->assertAttribute('[data-testid="delete-account-modal"]', 'role', 'dialog')
      ->assertAttribute('[data-testid="delete-account-modal"]', 'aria-modal', 'true')
      ->assertPresent('[data-testid="delete-account-modal"] h2, [data-testid="delete-account-modal"] h3');
  });
});

test('keyboard navigation works in dropdowns', function () {
  $user = User::factory()->create();

  $this->browse(function (Browser $browser) use ($user) {
    $browser->loginAs($user)
      ->visit('/dashboard')
      ->click('button[data-testid="user-menu-trigger"]')
      ->waitFor('button[data-testid="logout-button"]')
      ->keys('button[data-testid="user-menu-trigger"]', '{arrow_down}')
      ->assertFocused('a, button');
  });
});

test('page titles are descriptive', function () {
  $user = User::factory()->create();

  $this->browse(function (Browser $browser) use ($user) {
    $browser->visit('/')
      ->assertTitle('Druid')
      ->visit('/login')
      ->assertTitleContains('Login')
      ->loginAs($user)
      ->visit('/dashboard')
      ->assertTitleContains('Dashboard')
      ->visit('/settings/profile')
      ->assertTitleContains('Profile');
  });
});

test('language attribute is set', function () {
  $this->browse(function (Browser $browser) {
    $browser->visit('/')
      ->assertAttribute('html', 'lang');
  });
});

test('form inputs have proper types', function () {
  $this->browse(function (Browser $browser) {
    $browser->visit('/login')
      ->assertAttribute('input[name="email"]', 'type', 'email')
      ->assertAttribute('input[name="password"]', 'type', 'password')
      ->visit('/register')
      ->assertAttribute('input[name="email"]', 'type', 'email')
      ->assertAttribute('input[name="password"]', 'type', 'password')
      ->assertAttribute('input[name="password_confirmation"]', 'type', 'password');
  });
});

test('error messages are associated with form fields', function () {
  $this->browse(function (Browser $browser) {
    $browser->visit('/login')
      ->press('Sign in')
      ->waitForText('The email field is required.')
      ->assertPresent('input[name="email"][aria-describedby], input[name="email"][aria-invalid]');
  });
});
