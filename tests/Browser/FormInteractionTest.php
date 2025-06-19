<?php

use App\Models\User;
use Laravel\Dusk\Browser;

test('form validation displays correctly', function () {
  $this->browse(function (Browser $browser) {
    $browser->visit('/register')
      ->press('Register')
      ->waitForText('The name field is required.')
      ->assertSee('The name field is required.')
      ->assertSee('The email field is required.')
      ->assertSee('The password field is required.');
  });
});

test('form fields accept input correctly', function () {
  $this->browse(function (Browser $browser) {
    $browser->visit('/register')
      ->type('name', 'Test User')
      ->type('email', 'test@example.com')
      ->type('password', 'password123')
      ->type('password_confirmation', 'password123')
      ->assertInputValue('name', 'Test User')
      ->assertInputValue('email', 'test@example.com')
      ->assertInputValue('password', 'password123')
      ->assertInputValue('password_confirmation', 'password123');
  });
});

test('form submission shows loading state', function () {
  $this->browse(function (Browser $browser) {
    $browser->visit('/register')
      ->type('name', 'Test User')
      ->type('email', 'test@example.com')
      ->type('password', 'password123')
      ->type('password_confirmation', 'password123')
      ->press('Register')
      ->assertPresent('svg[data-testid="loading-spinner"]'); // Loading spinner
  });
});

test('checkbox interactions work correctly', function () {
  $this->browse(function (Browser $browser) {
    $browser->visit('/login')
      ->assertPresent('input[name="remember"]')
      ->check('remember')
      ->assertChecked('remember')
      ->uncheck('remember')
      ->assertNotChecked('remember');
  });
});

test('form auto-focus works', function () {
  $this->browse(function (Browser $browser) {
    $browser->visit('/login')
      ->assertFocused('input[name="email"]');
  });
});

test('form keyboard navigation works', function () {
  $this->browse(function (Browser $browser) {
    $browser->visit('/login')
      ->keys('input[name="email"]', 'test@example.com')
      ->keys('input[name="email"]', '{tab}')
      ->assertFocused('input[name="password"]')
      ->keys('input[name="password"]', 'password')
      ->keys('input[name="password"]', '{tab}')
      ->assertFocused('input[name="remember"]');
  });
});

test('form error states clear on input', function () {
  $this->browse(function (Browser $browser) {
    $browser->visit('/login')
      ->press('Sign in')
      ->waitForText('The email field is required.')
      ->assertSee('The email field is required.')
      ->type('email', 'test@example.com')
      ->pause(500) // Wait for error to clear
      ->assertDontSee('The email field is required.');
  });
});

test('profile form updates work correctly', function () {
  $user = User::factory()->create([
    'name' => 'Original Name',
    'email' => 'original@example.com',
  ]);

  $this->browse(function (Browser $browser) use ($user) {
    $browser->loginAs($user)
      ->visit('/settings/profile')
      ->assertInputValue('name', 'Original Name')
      ->assertInputValue('email', 'original@example.com')
      ->clear('name')
      ->type('name', 'Updated Name')
      ->press('Save')
      ->waitForText('Saved.')
      ->assertSee('Saved.')
      ->refresh()
      ->assertInputValue('name', 'Updated Name');
  });
});

test('password form validation works', function () {
  $user = User::factory()->create();

  $this->browse(function (Browser $browser) use ($user) {
    $browser->loginAs($user)
      ->visit('/settings/password')
      ->type('password', 'short')
      ->type('password_confirmation', 'different')
      ->press('Save')
      ->waitForText('The password field must be at least 8 characters.')
      ->assertSee('The password field must be at least 8 characters.')
      ->assertSee('The password field confirmation does not match.');
  });
});

test('form submission prevents double submission', function () {
  $this->browse(function (Browser $browser) {
    $browser->visit('/register')
      ->type('name', 'Test User')
      ->type('email', 'test@example.com')
      ->type('password', 'password123')
      ->type('password_confirmation', 'password123')
      ->press('Register')
      ->assertAttribute('button[type="submit"]', 'disabled', 'true');
  });
});

test('form fields have proper labels and accessibility', function () {
  $this->browse(function (Browser $browser) {
    $browser->visit('/login')
      ->assertPresent('label[for="email"]')
      ->assertPresent('label[for="password"]')
      ->assertAttribute('input[name="email"]', 'aria-describedby')
      ->assertAttribute('input[name="password"]', 'aria-describedby');
  });
});

test('form autocomplete attributes are set', function () {
  $this->browse(function (Browser $browser) {
    $browser->visit('/login')
      ->assertAttribute('input[name="email"]', 'autocomplete', 'email')
      ->assertAttribute('input[name="password"]', 'autocomplete', 'current-password');
  });
});

test('form placeholders are displayed', function () {
  $this->browse(function (Browser $browser) {
    $browser->visit('/register')
      ->assertAttribute('input[name="name"]', 'placeholder')
      ->assertAttribute('input[name="email"]', 'placeholder');
  });
});

test('form reset functionality works', function () {
  $this->browse(function (Browser $browser) {
    $browser->visit('/register')
      ->type('name', 'Test User')
      ->type('email', 'test@example.com')
      ->type('password', 'password123')
      ->refresh() // Simulate form reset
      ->assertInputValue('name', '')
      ->assertInputValue('email', '')
      ->assertInputValue('password', '');
  });
});
