<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Http\UploadedFile;

it('updates the profile name and reports success', function (): void {
  $user = User::factory()->withPersonalTeam()->create(['name' => 'Old Name']);
  $this->actingAs($user);

  visit('/settings/profile')
    ->fill('name', 'New Name')
    ->click('@update-profile-button')
    ->assertSee('Saved.');

  expect($user->fresh()->name)->toBe('New Name');
});

it('shows a validation error when the email is already taken', function (): void {
  User::factory()->withPersonalTeam()->create(['email' => 'taken@example.com']);
  $user = User::factory()->withPersonalTeam()->create();
  $this->actingAs($user);

  visit('/settings/profile')
    ->fill('email', 'taken@example.com')
    ->click('@update-profile-button')
    ->assertSee('has already been taken');
});

it('shows the initials immediately after the avatar is removed', function (): void {
  // A real (served) photo so the browser loads the image before removal — this is the
  // exact reka-ui lifecycle bug where the fallback failed to render after the image unmounted.
  $user = User::factory()->withPersonalTeam()->create(['name' => 'Zeta Quux']);
  $user->updateProfilePhoto(UploadedFile::fake()->image('avatar.jpg', 200, 200));
  $this->actingAs($user);

  $page = visit('/settings/profile');
  $page->assertSee('Remove Photo');

  $page->click('Remove Photo')
    ->assertSee('ZQ')
    ->assertDontSee('Remove Photo');
});

// NOTE: uploading a photo through the form is NOT E2E-drivable here — Playwright rejects local file
// paths ("localPaths are not allowed when the client is not local"), so `attach()` on a file input
// can't be used in this setup. The upload branch (ProfileController@update photo path) stays covered
// by the Feature suite. Avatar *removal* is covered above (that photo is set via the model).

it('changes the email and resets email verification', function (): void {
  $user = User::factory()->withPersonalTeam()->create();
  $this->actingAs($user);

  expect($user->email_verified_at)->not->toBeNull();

  $newEmail = 'changed-'.uniqid().'@example.com';

  visit('/settings/profile')
    ->fill('email', $newEmail)
    ->click('@update-profile-button')
    ->assertSee('Saved.');

  $fresh = $user->fresh();
  expect($fresh->email)->toBe($newEmail);
  expect($fresh->email_verified_at)->toBeNull();
});
