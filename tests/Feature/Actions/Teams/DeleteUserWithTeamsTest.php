<?php

declare(strict_types=1);

use App\Actions\Teams\DeleteUserWithTeams;
use App\Models\Team;
use App\Models\User;

it('deletes user and their owned teams', function (): void {
  $user = User::factory()->withPersonalTeam()->create();
  $userId = $user->id;
  $teamId = $user->currentTeam->id;

  $action = resolve(DeleteUserWithTeams::class);
  $action->delete($user);

  // User should be soft-deleted
  $this->assertSoftDeleted('users', ['id' => $userId]);

  // Personal team should be deleted
  $this->assertDatabaseMissing('teams', ['id' => $teamId]);
});

it('detaches user from teams they belong to', function (): void {
  $owner = User::factory()->withPersonalTeam()->create();
  $member = User::factory()->create();

  $owner->currentTeam->users()->attach($member, ['role' => 'admin']);

  $action = resolve(DeleteUserWithTeams::class);
  $action->delete($member);

  // Member should be detached from the team
  expect($owner->currentTeam->fresh()->hasUser($member))->toBeFalse();

  // But team still exists because it's owned by someone else
  expect(Team::query()->find($owner->currentTeam->id))->not->toBeNull();
});

it('deletes user api tokens', function (): void {
  $user = User::factory()->withPersonalTeam()->create();
  $token = $user->createToken('test-token');
  $tokenId = $token->accessToken->id;

  $action = resolve(DeleteUserWithTeams::class);
  $action->delete($user);

  $this->assertDatabaseMissing('personal_access_tokens', ['id' => $tokenId]);
});
