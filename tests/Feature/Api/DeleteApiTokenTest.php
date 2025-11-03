<?php

declare(strict_types=1);

use App\Models\User;
use App\Teams\Features;
use Illuminate\Support\Str;

it('can delete api tokens', function (): void {
  if (! Features::hasApiFeatures()) {
    $this->markTestSkipped('API support is not enabled.');
  }

  $this->actingAs($user = User::factory()->withPersonalTeam()->create());

  $token = $user->tokens()->create([
    'name' => 'Test Token',
    'token' => Str::random(40),
    'abilities' => ['create', 'read'],
  ]);

  $this->delete('/user/api-tokens/'.$token->id);

  $this->assertCount(0, $user->fresh()->tokens);
});
