<?php

declare(strict_types=1);

use App\Models\User;
use App\Teams\Features;

it('can delete user accounts', function (): void {
  if (! Features::hasAccountDeletionFeatures()) {
    $this->markTestSkipped('Account deletion is not enabled.');
  }

  $this->actingAs($user = User::factory()->create());

  $this->delete('/user', [
    'password' => 'password',
  ]);

  $this->assertNull($user->fresh());
});

it('requires correct password before account can be deleted', function (): void {
  if (! Features::hasAccountDeletionFeatures()) {
    $this->markTestSkipped('Account deletion is not enabled.');
  }

  $this->actingAs($user = User::factory()->create());

  $this->delete('/user', [
    'password' => 'wrong-password',
  ]);

  $this->assertNotNull($user->fresh());
});
