<?php

declare(strict_types=1);

use App\Actions\Teams\DeleteUserWithTeams;
use App\Contracts\DeletesUsers;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\DB;

it('deletes the account after confirming the password', function (): void {
  $user = User::factory()->withPersonalTeam()->create();
  $this->actingAs($user);

  visit('/settings/profile')
    ->click('@delete-user-button')
    ->fill('password', 'password')
    ->click('@confirm-delete-user-button')
    ->assertPathIs('/');

  expect(User::query()->find($user->id))->toBeNull();
});

it('does not delete the account with a wrong password', function (): void {
  $user = User::factory()->withPersonalTeam()->create();
  $this->actingAs($user);

  visit('/settings/profile')
    ->click('@delete-user-button')
    ->fill('password', 'wrong-password')
    ->click('@confirm-delete-user-button')
    ->assertSee('password is incorrect');

  expect(User::query()->find($user->id))->not->toBeNull();
});

it('deletes an owner of a non-personal team, purging the team and its memberships', function (): void {
  // The app binds DeletesUsers to DeleteUser (user-only). Swap in the teams-aware
  // implementation so account deletion also tears down owned teams + memberships,
  // exercising the otherwise-unwired App\Actions\Teams\DeleteUserWithTeams.
  $this->app->singleton(DeletesUsers::class, DeleteUserWithTeams::class);

  $owner = User::factory()->withPersonalTeam()->create();
  $team = Team::factory()->create([
    'user_id' => $owner->id,
    'personal_team' => false,
  ]);
  $member = User::factory()->withPersonalTeam()->create();
  $team->users()->attach($member, ['role' => 'editor']);
  $this->actingAs($owner);

  visit('/settings/profile')
    ->click('@delete-user-button')
    ->fill('password', 'password')
    ->click('@confirm-delete-user-button')
    ->assertPathIs('/');

  expect(User::query()->find($owner->id))->toBeNull();
  expect(Team::query()->find($team->id))->toBeNull();
  expect(DB::table('team_user')->where('team_id', $team->id)->exists())->toBeFalse();
  // Only the owner is gone; the member's own account survives the purge.
  expect(User::query()->find($member->id))->not->toBeNull();
});

it('takes the no-op photo-cleanup path on deletion when profile photos are disabled', function (): void {
  // Drop only the 'profile-photos' feature (keeping account-deletion enabled) so the served
  // delete request sees managesProfilePhotos() === false. DeleteUser::delete -> deleteProfilePhoto()
  // then takes its early-return guard (HasProfilePhoto line 47) instead of touching storage, and
  // the deletion still completes.
  config(['teams.features' => array_values(array_diff((array) config('teams.features'), ['profile-photos']))]);

  $user = User::factory()->withPersonalTeam()->create();
  $this->actingAs($user);

  visit('/settings/profile')
    ->click('@delete-user-button')
    ->fill('password', 'password')
    ->click('@confirm-delete-user-button')
    ->assertPathIs('/');

  expect(User::query()->find($user->id))->toBeNull();
});
