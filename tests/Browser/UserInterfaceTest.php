<?php

declare(strict_types=1);

it('displays user initials correctly', function (): void {
  \App\Models\User::factory()->create([
    'name' => 'John Doe',
    'email' => 'initials@example.com',
    'password' => bcrypt('password'),
  ]);

  $page = visit('/login');

  $page->type('email', 'initials@example.com')
    ->type('password', 'password')
    ->click('Log in')
    ->wait(1)
    ->assertPathIs('/dashboard')
    ->assertSee('JD'); // User initials should be displayed

});

it('redirects to login when accessing protected pages', function (): void {
  $page = visit('/settings/profile');

  $page->wait(1)
    ->assertPathIs('/login'); // Should redirect to login

});

it('displays breadcrumbs on dashboard', function (): void {
  \App\Models\User::factory()->create([
    'email' => 'breadcrumbs@example.com',
    'password' => bcrypt('password'),
  ]);

  $page = visit('/login');

  $page->type('email', 'breadcrumbs@example.com')
    ->type('password', 'password')
    ->click('Log in')
    ->wait(1)
    ->assertPathIs('/dashboard')
    ->assertSee('Dashboard');

});
