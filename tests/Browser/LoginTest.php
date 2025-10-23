<?php

declare(strict_types=1);

it('displays the login page', function (): void {
  $page = visit('/login');

  $page->assertSee('Log in to your account')
    ->assertSee('Email address')
    ->assertSee('Password')
    ->assertSee('Remember me')
    ->assertSee('Log in')
    ->assertSee("Don't have an account?")
    ->assertSee('Sign up');

});

it('can log in with valid credentials', function (): void {
  \App\Models\User::factory()->create([
    'email' => 'test@example.com',
    'password' => bcrypt('password'),
  ]);

  $page = visit('/login');

  $page->type('email', 'test@example.com')
    ->type('password', 'password')
    ->click('Log in')
    ->wait(1)
    ->assertPathIs('/dashboard');

});

it('shows forgot password link', function (): void {
  $page = visit('/login');

  $page->assertSee('Forgot password?')
    ->click('Forgot password?')
    ->wait(1)
    ->assertPathIs('/forgot-password');

});

it('shows sign up link', function (): void {
  $page = visit('/login');

  $page->click('Sign up')
    ->wait(1)
    ->assertPathIs('/register');

});
