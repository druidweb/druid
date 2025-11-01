<?php

declare(strict_types=1);

use Laravel\Fortify\Features;

it('renders the welcome page', function (): void {
  $response = $this->get(route('home'));

  $response->assertOk();
  $response->assertInertia(fn ($page) => $page->component('Welcome'));
});

it('passes canRegister prop when registration is enabled', function (): void {
  config(['fortify.features' => [Features::registration()]]);

  $response = $this->get(route('home'));

  $response->assertOk();
  $response->assertInertia(fn ($page) => $page
    ->component('Welcome')
    ->has('canRegister')
    ->where('canRegister', true));
});

it('passes canRegister as false when registration is disabled', function (): void {
  config(['fortify.features' => []]);

  $response = $this->get(route('home'));

  $response->assertOk();
  $response->assertInertia(fn ($page) => $page
    ->component('Welcome')
    ->has('canRegister')
    ->where('canRegister', false));
});
