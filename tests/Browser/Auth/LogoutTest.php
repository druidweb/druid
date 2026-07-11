<?php

declare(strict_types=1);

use App\Models\User;

it('logs the user out from the account menu', function (): void {
  $user = User::factory()->withPersonalTeam()->create();
  $this->actingAs($user);

  visit('/dashboard')
    ->click('@user-menu-button')
    ->click('@logout-button')
    ->assertPathIs('/');
});
