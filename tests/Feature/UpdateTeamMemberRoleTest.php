<?php

declare(strict_types=1);

use App\Models\User;

it('updates team member roles', function (): void {
  $this->actingAs($user = User::factory()->withPersonalTeam()->create());

  $user->currentTeam->users()->attach(
    $otherUser = User::factory()->create(), ['role' => 'admin']
  );

  $this->put('/teams/'.$user->currentTeam->id.'/members/'.$otherUser->id, [
    'role' => 'editor',
  ]);

  expect($otherUser->fresh()->hasTeamRole($user->currentTeam->fresh(), 'editor'))->toBeTrue();
});

it('only lets the team owner update team member roles', function (): void {
  $user = User::factory()->withPersonalTeam()->create();

  $user->currentTeam->users()->attach(
    $otherUser = User::factory()->create(), ['role' => 'admin']
  );

  $this->actingAs($otherUser);

  $this->put('/teams/'.$user->currentTeam->id.'/members/'.$otherUser->id, [
    'role' => 'editor',
  ]);

  expect($otherUser->fresh()->hasTeamRole($user->currentTeam->fresh(), 'admin'))->toBeTrue();
});
