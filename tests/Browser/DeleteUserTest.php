<?php

declare(strict_types=1);

use App\Models\User;

it('displays delete account section on profile page', function (): void {
  $user = User::factory()->create();

  $page = $this->actingAs($user)->visit('/profile');

  $page->assertSee('Delete account')
    ->assertSee('Warning')
    ->assertSee('Please proceed with caution, this cannot be undone.')
    ->assertNoJavascriptErrors();
});

it('opens delete account dialog when clicking delete button', function (): void {
  $user = User::factory()->create();

  $page = $this->actingAs($user)->visit('/profile');

  $page->click('Delete account')
    ->assertSee('Are you sure you want to delete your account?')
    ->assertSee('Once your account is deleted, all of its resources and data will also be permanently deleted.')
    ->assertNoJavascriptErrors();
});

it('closes delete account dialog when clicking cancel', function (): void {
  $user = User::factory()->create();

  $page = $this->actingAs($user)->visit('/profile');

  $page->click('Delete account')
    ->assertSee('Are you sure you want to delete your account?')
    ->click('Cancel')
    ->assertNoJavascriptErrors();
});

it('requires password to delete account', function (): void {
  $user = User::factory()->create();

  $page = $this->actingAs($user)->visit('/profile');

  $page->click('Delete account')
    ->assertSee('Are you sure you want to delete your account?')
    ->fill('password', '')
    ->click('Delete my account')
    ->assertSee('The password field is required.')
    ->assertNoJavascriptErrors();
});

it('shows error when password is incorrect', function (): void {
  $user = User::factory()->create();

  $page = $this->actingAs($user)->visit('/profile');

  $page->click('Delete account')
    ->fill('password', 'wrong-password')
    ->click('Delete my account')
    ->assertSee('The provided password is incorrect.')
    ->assertNoJavascriptErrors();
});
