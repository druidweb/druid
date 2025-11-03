<?php

declare(strict_types=1);

use App\Models\User;

it('can update team names', function (): void {
  $this->actingAs($user = User::factory()->withPersonalTeam()->create());

  $this->put('/teams/'.$user->currentTeam->id, [
    'name' => 'Test Team',
  ]);

  $this->assertCount(1, $user->fresh()->ownedTeams);
  $this->assertEquals('Test Team', $user->currentTeam->fresh()->name);
});
