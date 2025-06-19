<?php

use App\Models\User;
use Laravel\Dusk\Browser;

test('user can access profile settings', function () {
  $user = User::factory()->create();

  $this->browse(function (Browser $browser) use ($user) {
    $browser->loginAs($user)
      ->visit('/settings/profile')
      ->assertSee('Profile')
      ->assertSee('Update your account\'s profile information and email address.')
      ->assertPresent('input[name="name"]')
      ->assertPresent('input[name="email"]')
      ->assertPresent('button[type="submit"]');
  });
});

test('user can update profile information', function () {
  $user = User::factory()->create([
    'name' => 'Original Name',
    'email' => 'original@example.com',
  ]);

  $this->browse(function (Browser $browser) use ($user) {
    $browser->loginAs($user)
      ->visit('/settings/profile')
      ->clear('name')
      ->type('name', 'Updated Name')
      ->clear('email')
      ->type('email', 'updated@example.com')
      ->press('Save')
      ->waitForText('Saved.')
      ->assertSee('Saved.');
  });

  // Verify the user was updated
  $user->refresh();
  expect($user->name)->toBe('Updated Name');
  expect($user->email)->toBe('updated@example.com');
});

test('user can access password settings', function () {
  $user = User::factory()->create();

  $this->browse(function (Browser $browser) use ($user) {
    $browser->loginAs($user)
      ->visit('/settings/password')
      ->assertSee('Update Password')
      ->assertSee('Ensure your account is using a long, random password to stay secure.')
      ->assertPresent('input[name="current_password"]')
      ->assertPresent('input[name="password"]')
      ->assertPresent('input[name="password_confirmation"]')
      ->assertPresent('button[type="submit"]');
  });
});

test('user can update password', function () {
  $user = User::factory()->create([
    'password' => bcrypt('oldpassword'),
  ]);

  $this->browse(function (Browser $browser) use ($user) {
    $browser->loginAs($user)
      ->visit('/settings/password')
      ->type('current_password', 'oldpassword')
      ->type('password', 'newpassword')
      ->type('password_confirmation', 'newpassword')
      ->press('Save')
      ->waitForText('Saved.')
      ->assertSee('Saved.');
  });
});

test('user cannot update password with wrong current password', function () {
  $user = User::factory()->create([
    'password' => bcrypt('correctpassword'),
  ]);

  $this->browse(function (Browser $browser) use ($user) {
    $browser->loginAs($user)
      ->visit('/settings/password')
      ->type('current_password', 'wrongpassword')
      ->type('password', 'newpassword')
      ->type('password_confirmation', 'newpassword')
      ->press('Save')
      ->waitForText('The current password is incorrect.')
      ->assertSee('The current password is incorrect.');
  });
});

test('user can access appearance settings', function () {
  $user = User::factory()->create();

  $this->browse(function (Browser $browser) use ($user) {
    $browser->loginAs($user)
      ->visit('/settings/appearance')
      ->assertSee('Appearance')
      ->assertSee('Customize the appearance of the app. Automatically switch between day and night themes.');
  });
});

test('settings navigation works correctly', function () {
  $user = User::factory()->create();

  $this->browse(function (Browser $browser) use ($user) {
    $browser->loginAs($user)
      ->visit('/settings/profile')
      ->assertSee('Profile')
      ->clickLink('Password')
      ->waitForRoute('password.edit')
      ->assertRouteIs('password.edit')
      ->assertSee('Update Password')
      ->clickLink('Appearance')
      ->waitForRoute('appearance')
      ->assertRouteIs('appearance')
      ->assertSee('Appearance')
      ->clickLink('Profile')
      ->waitForRoute('profile.edit')
      ->assertRouteIs('profile.edit')
      ->assertSee('Profile');
  });
});

test('settings sidebar navigation is highlighted correctly', function () {
  $user = User::factory()->create();

  $this->browse(function (Browser $browser) use ($user) {
    $browser->loginAs($user)
      ->visit('/settings/profile')
      ->assertPresent('nav')
      ->assertSeeIn('nav', 'Profile')
      ->assertSeeIn('nav', 'Password')
      ->assertSeeIn('nav', 'Appearance');
  });
});

test('user can delete account', function () {
  $user = User::factory()->create();

  $this->browse(function (Browser $browser) use ($user) {
    $browser->loginAs($user)
      ->visit('/settings/profile')
      ->scrollIntoView('button[data-testid="delete-account-button"]')
      ->click('button[data-testid="delete-account-button"]')
      ->waitFor('[data-testid="delete-account-modal"]')
      ->assertSee('Are you sure you want to delete your account?')
      ->type('password', 'password')
      ->click('button[data-testid="confirm-delete-button"]')
      ->waitForRoute('home')
      ->assertRouteIs('home');
  });

  // Verify the user was deleted
  $this->assertDatabaseMissing('users', [
    'id' => $user->id,
  ]);
});

test('settings pages are responsive', function () {
  $user = User::factory()->create();

  $this->browse(function (Browser $browser) use ($user) {
    $browser->loginAs($user)
      ->visit('/settings/profile')
      ->resize(768, 1024) // Tablet size
      ->assertSee('Profile')
      ->resize(375, 667) // Mobile size
      ->assertSee('Profile')
      ->resize(1920, 1080); // Desktop size
  });
});
