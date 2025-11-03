<?php

declare(strict_types=1);

use App\Models\User;

it('can remove team members from teams', function () {
  $this->actingAs($user = User::factory()->withPersonalTeam()->create());

  $user->currentTeam->users()->attach(
    $otherUser = User::factory()->create(), ['role' => 'admin']
  );

  $this->delete('/teams/'.$user->currentTeam->id.'/members/'.$otherUser->id);

  $this->assertCount(0, $user->currentTeam->fresh()->users);
});

it('only allows team owner to remove team members', function () {
  $user = User::factory()->withPersonalTeam()->create();

  $user->currentTeam->users()->attach(
    $otherUser = User::factory()->create(), ['role' => 'admin']
  );

  $this->actingAs($otherUser);

  $response = $this->delete('/teams/'.$user->currentTeam->id.'/members/'.$user->id);

  $response->assertStatus(403);
});
