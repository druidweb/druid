<?php

declare(strict_types=1);

use App\Models\User;

it('renders the welcome page without errors', function (): void {
  visit('/')->assertNoJavaScriptErrors();
});

it('shows the auth entry points to guests', function (): void {
  visit('/')
    ->assertPresent('a[href="/login"]')
    ->assertPresent('a[href="/register"]')
    ->assertSee('Sign in')
    ->assertSee('Get started');
});

it('points every external link at the correct destination in a new tab', function (): void {
  $page = visit('/');

  $externalLinks = [
    'https://github.com/druidweb/druid',
    'https://laravel.com',
    'https://vuejs.org',
    'https://inertiajs.com',
    'https://www.typescriptlang.org',
    'https://tailwindcss.com',
    'https://www.shadcn-vue.com',
    'https://jetstreamlabs.com',
    'https://laravel.com/docs/13.x/starter-kits',
    'https://github.com/druidweb/druid/blob/main/LICENSE.md',
  ];

  foreach ($externalLinks as $href) {
    $page->assertPresent('a[href="'.$href.'"][target="_blank"]');
  }
});

it('navigates an authenticated user to the dashboard', function (): void {
  $user = User::factory()->withPersonalTeam()->create();
  $this->actingAs($user);

  visit('/')
    ->assertPresent('a[href="/dashboard"]')
    ->click('Dashboard')
    ->assertPathIs('/dashboard');
});

it('copies the install command and shows a confirmation toast', function (): void {
  visit('/')
    ->click('@copy-install')
    ->assertSee('Copied to clipboard');
});
