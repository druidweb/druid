<?php

declare(strict_types=1);

use App\Models\User;

it('can log out other browser sessions', function (): void {
  $this->actingAs(User::factory()->create());

  $response = $this->delete('/user/other-browser-sessions', [
    'password' => 'password',
  ]);

  $response->assertSessionHasNoErrors();
});

it('describes the current device from the user agent', function (): void {
  $this->actingAs(User::factory()->create());

  $this->withHeader('User-Agent', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36')
    ->get('/user/other-browser-sessions')
    ->assertInertia(fn ($page) => $page
      ->component('settings/Sessions')
      ->where('device.platform', 'Windows')
      ->where('device.browser', 'Chrome')
      ->where('device.is_desktop', true)
      ->where('device.ip_address', '127.0.0.1'));
});

it('identifies a mobile device', function (): void {
  $this->actingAs(User::factory()->create());

  $this->withHeader('User-Agent', 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1')
    ->get('/user/other-browser-sessions')
    ->assertInertia(fn ($page) => $page
      ->where('device.platform', 'iOS')
      ->where('device.browser', 'Safari')
      ->where('device.is_desktop', false));
});

it('falls back to unknown for an unrecognised user agent', function (): void {
  $this->actingAs(User::factory()->create());

  $this->withHeader('User-Agent', 'curl/8.0')
    ->get('/user/other-browser-sessions')
    ->assertInertia(fn ($page) => $page
      ->where('device.platform', 'Unknown')
      ->where('device.browser', 'Unknown'));
});
