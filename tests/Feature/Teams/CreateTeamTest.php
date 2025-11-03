<?php

declare(strict_types=1);

use App\Models\User;

it('can create teams', function () {
  $this->actingAs($user = User::factory()->withPersonalTeam()->create());

  $this->post('/teams', [
    'name' => 'Test Team',
  ]);

  $this->assertCount(2, $user->fresh()->ownedTeams);
  $this->assertEquals('Test Team', $user->fresh()->ownedTeams()->latest('id')->first()->name);
});
