<?php

declare(strict_types=1);

use App\Models\User;

it('can update profile information', function (): void {
  $this->actingAs($user = User::factory()->create());

  $this->put('/user/profile-information', [
    'name' => 'Test Name',
    'email' => 'test@example.com',
  ]);

  $this->assertEquals('Test Name', $user->fresh()->name);
  $this->assertEquals('test@example.com', $user->fresh()->email);
});
