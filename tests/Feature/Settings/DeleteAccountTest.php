<?php

declare(strict_types=1);

use App\Models\User;
use App\Teams\Features;

it('can delete user accounts', function (): void {
  if (! Features::hasAccountDeletionFeatures()) {
    $this->markTestSkipped('Account deletion is not enabled.');
  }

  $this->actingAs($user = User::factory()->create());

  $this->delete('/settings/profile', [
    'password' => 'password',
  ]);

  // User model uses SoftDeletes, so fresh() returns the soft-deleted user
  $this->assertNotNull($user->fresh()?->deleted_at);
});

it('requires correct password before account can be deleted', function (): void {
  if (! Features::hasAccountDeletionFeatures()) {
    $this->markTestSkipped('Account deletion is not enabled.');
  }

  $this->actingAs($user = User::factory()->create());

  $this->delete('/settings/profile', [
    'password' => 'wrong-password',
  ]);

  $this->assertNotNull($user->fresh());
});

it('returns forbidden when account deletion feature is disabled', function (): void {
  // Get current features and remove account deletion
  $features = config('teams.features', []);
  $featuresWithoutDeletion = array_values(array_filter($features, fn ($f): bool => $f !== Features::accountDeletion()));
  config(['teams.features' => $featuresWithoutDeletion]);

  $this->actingAs($user = User::factory()->create());

  $response = $this->delete('/settings/profile', [
    'password' => 'password',
  ]);

  $response->assertForbidden();
  $this->assertNull($user->fresh()?->deleted_at);

  // Restore features
  config(['teams.features' => $features]);
});
