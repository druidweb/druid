<?php

declare(strict_types=1);

use App\Models\User;
use App\Teams\Features;

it('displays api tokens page', function (): void {
  $user = User::factory()->withPersonalTeam()->create();

  $this->actingAs($user);

  $response = $this->get('/user/api-tokens');

  if (Features::hasApiFeatures()) {
    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->component('api/Index'));
  } else {
    $response->assertNotFound();
  }
});

it('can create api token', function (): void {
  $user = User::factory()->withPersonalTeam()->create();

  $this->actingAs($user);

  $response = $this->post('/user/api-tokens', [
    'name' => 'Test Token',
    'permissions' => ['read'],
  ]);

  if (Features::hasApiFeatures()) {
    $response->assertRedirect();
    expect($user->tokens()->where('name', 'Test Token')->exists())->toBeTrue();
  } else {
    $response->assertNotFound();
  }
});

it('can delete api token', function (): void {
  $user = User::factory()->withPersonalTeam()->create();
  $token = $user->createToken('Test Token');

  $this->actingAs($user);

  $response = $this->delete('/user/api-tokens/'.$token->accessToken->id);

  if (Features::hasApiFeatures()) {
    $response->assertRedirect();
    expect($user->tokens()->where('id', $token->accessToken->id)->exists())->toBeFalse();
  } else {
    $response->assertNotFound();
  }
});
