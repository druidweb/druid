<?php

declare(strict_types=1);

use App\Models\User;

it('allows users to leave teams', function () {
  $user = User::factory()->withPersonalTeam()->create();

  $user->currentTeam->users()->attach(
    $otherUser = User::factory()->create(), ['role' => 'admin']
  );

  $this->actingAs($otherUser);

  $this->delete('/teams/'.$user->currentTeam->id.'/members/'.$otherUser->id);

  $this->assertCount(0, $user->currentTeam->fresh()->users);
});

it('prevents team owners from leaving their own team', function () {
  $this->actingAs($user = User::factory()->withPersonalTeam()->create());

  $response = $this->delete('/teams/'.$user->currentTeam->id.'/members/'.$user->id);

  $response->assertSessionHasErrorsIn('removeTeamMember', ['team']);

  $this->assertNotNull($user->currentTeam->fresh());
});
