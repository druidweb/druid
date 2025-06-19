<?php

use App\Models\User;
use Laravel\Dusk\Browser;

test('login page loads correctly', function () {
  $this->browse(function (Browser $browser) {
    $browser->visit('/login')
      ->pause(2000) // Wait for Vue app to mount
      ->assertSee('Log in to your account')
      ->assertPresent('input[id="email"]')
      ->assertPresent('input[id="password"]')
      ->assertPresent('button[type="submit"]')
      ->assertSeeLink('Sign up')
      ->assertSeeLink('Forgot password?');
  });
});

test('user can login with valid credentials', function () {
  $user = User::factory()->create([
    'email' => 'test@example.com',
  ]);

  // Verify user was created and check password
  expect($user->email)->toBe('test@example.com');
  expect($user->exists)->toBeTrue();
  expect(\Illuminate\Support\Facades\Hash::check('password', $user->password))->toBeTrue();

  $this->browse(function (Browser $browser) use ($user) {
    $browser->visit('/login')
      ->pause(2000) // Wait for Vue app to mount
      ->type('#email', $user->email)
      ->type('#password', 'password') // Default factory password
      ->press('Log in')
      ->pause(5000) // Wait for form submission
      ->screenshot('login-debug');

    // Check current URL and page content
    dump('Current URL: '.$browser->driver->getCurrentURL());

    $browser->assertPathIs('/dashboard');
  });
});

test('user cannot login with invalid credentials', function () {
  $this->browse(function (Browser $browser) {
    $browser->visit('/login')
      ->pause(2000) // Wait for Vue app to mount
      ->type('#email', 'invalid@example.com')
      ->type('#password', 'wrongpassword')
      ->press('Log in')
      ->waitForText('These credentials do not match our records.')
      ->assertSee('These credentials do not match our records.')
      ->assertRouteIs('login');
  });
});

test('user can access registration page', function () {
  $this->browse(function (Browser $browser) {
    $browser->visit('/register')
      ->assertSee('Create your account')
      ->assertPresent('input[name="name"]')
      ->assertPresent('input[name="email"]')
      ->assertPresent('input[name="password"]')
      ->assertPresent('input[name="password_confirmation"]')
      ->assertPresent('button[type="submit"]')
      ->assertSeeLink('Already registered?');
  });
});

test('user can register with valid information', function () {
  $this->browse(function (Browser $browser) {
    $browser->visit('/register')
      ->type('name', 'Test User')
      ->type('email', 'newuser@example.com')
      ->type('password', 'password')
      ->type('password_confirmation', 'password')
      ->press('Register')
      ->waitForRoute('dashboard')
      ->assertRouteIs('dashboard')
      ->assertSee('Dashboard');
  });

  // Verify user was created
  $this->assertDatabaseHas('users', [
    'name' => 'Test User',
    'email' => 'newuser@example.com',
  ]);
});

test('user cannot register with invalid information', function () {
  $this->browse(function (Browser $browser) {
    $browser->visit('/register')
      ->type('name', '')
      ->type('email', 'invalid-email')
      ->type('password', '123')
      ->type('password_confirmation', '456')
      ->press('Register')
      ->waitForText('The name field is required.')
      ->assertSee('The name field is required.')
      ->assertSee('The email field must be a valid email address.')
      ->assertSee('The password field must be at least 8 characters.')
      ->assertSee('The password field confirmation does not match.')
      ->assertRouteIs('register');
  });
});

test('user can access forgot password page', function () {
  $this->browse(function (Browser $browser) {
    $browser->visit('/forgot-password')
      ->assertSee('Forgot your password?')
      ->assertPresent('input[name="email"]')
      ->assertPresent('button[type="submit"]')
      ->assertSeeLink('Back to login');
  });
});

test('authenticated user can logout', function () {
  $user = User::factory()->create();

  $this->browse(function (Browser $browser) use ($user) {
    $browser->loginAs($user)
      ->visit('/dashboard')
      ->assertSee('Dashboard')
      ->click('button[data-testid="user-menu-trigger"]')
      ->waitFor('button[data-testid="logout-button"]')
      ->click('button[data-testid="logout-button"]')
      ->waitForRoute('home')
      ->assertRouteIs('home')
      ->assertGuest();
  });
});

test('guest user is redirected to login when accessing protected routes', function () {
  $this->browse(function (Browser $browser) {
    $browser->visit('/dashboard')
      ->waitForRoute('login')
      ->assertRouteIs('login')
      ->assertSee('Sign in to your account');
  });
});
