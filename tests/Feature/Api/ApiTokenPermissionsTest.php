<?php

declare(strict_types=1);

use App\Models\User;
use App\Teams\Features;
use Illuminate\Support\Str;

it('can update api token permissions', function (): void {
  if (! Features::hasApiFeatures()) {
    $this->markTestSkipped('API support is not enabled.');
  }

  $this->actingAs($user = User::factory()->withPersonalTeam()->create());

  $token = $user->tokens()->create([
    'name' => 'Test Token',
    'token' => Str::random(40),
    'abilities' => ['create', 'read'],
  ]);

  $this->put('/user/api-tokens/'.$token->id, [
    'name' => $token->name,
    'permissions' => [
      'delete',
      'missing-permission',
    ],
  ]);

  $this->assertTrue($user->fresh()->tokens->first()->can('delete'));
  $this->assertFalse($user->fresh()->tokens->first()->can('read'));
  $this->assertFalse($user->fresh()->tokens->first()->can('missing-permission'));
});
