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
