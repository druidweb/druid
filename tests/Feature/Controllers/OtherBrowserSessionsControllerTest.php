<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Hash;

beforeEach(function (): void {
  $this->withoutVite();
});

it('displays the browser sessions page', function (): void {
  $user = User::factory()->withPersonalTeam()->create();

  $this->actingAs($user);

  $response = $this->get('/user/other-browser-sessions');

  $response->assertOk();
  $response->assertInertia(fn ($page) => $page->component('settings/Sessions'));
});

it('can logout other browser sessions', function (): void {
  $user = User::factory()->withPersonalTeam()->create([
    'password' => Hash::make('password'),
  ]);

  $this->actingAs($user);

  $response = $this->delete('/user/other-browser-sessions', [
    'password' => 'password',
  ]);

  $response->assertRedirect();
});

it('requires correct password to logout other sessions', function (): void {
  $user = User::factory()->withPersonalTeam()->create([
    'password' => Hash::make('password'),
  ]);

  $this->actingAs($user);

  $response = $this->delete('/user/other-browser-sessions', [
    'password' => 'wrong-password',
  ]);

  $response->assertSessionHasErrors('password');
});
