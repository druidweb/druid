<?php

declare(strict_types=1);

it('displays the app logo on dashboard', function (): void {
  \App\Models\User::factory()->create([
    'email' => 'logo@example.com',
    'password' => bcrypt('password'),
  ]);

  $page = visit('/login');

  $page->type('email', 'logo@example.com')
    ->type('password', 'password')
    ->click('Log in')
    ->wait(1)
    ->assertPathIs('/dashboard')
    ->assertSee('Laravel Starter Kit');

});

it('displays navigation on dashboard', function (): void {
  \App\Models\User::factory()->create([
    'email' => 'nav@example.com',
    'password' => bcrypt('password'),
  ]);

  $page = visit('/login');

  $page->type('email', 'nav@example.com')
    ->type('password', 'password')
    ->click('Log in')
    ->wait(1)
    ->assertPathIs('/dashboard')
    ->assertSee('Dashboard')
    ->assertSee('Platform');

});

it('displays user info on dashboard', function (): void {
  \App\Models\User::factory()->create([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'password' => bcrypt('password'),
  ]);

  $page = visit('/login');

  $page->type('email', 'john@example.com')
    ->type('password', 'password')
    ->click('Log in')
    ->wait(1)
    ->assertPathIs('/dashboard')
    ->assertSee('John Doe');

});

it('displays welcome page correctly', function (): void {
  $page = visit('/');

  $page->assertSee("Let's get started")
    ->assertSee('Log in')
    ->assertSee('Register');

});

it('can navigate from welcome to login', function (): void {
  $page = visit('/');

  $page->click('Log in')
    ->wait(1)
    ->assertPathIs('/login')
    ->assertSee('Log in to your account');

});

it('can navigate from welcome to register', function (): void {
  $page = visit('/');

  $page->click('Register')
    ->wait(1)
    ->assertPathIs('/register')
    ->assertSee('Create an account');

});
