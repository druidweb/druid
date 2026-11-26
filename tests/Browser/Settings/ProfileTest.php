<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

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

it('uploads a profile photo, then replaces it and deletes the old file', function (): void {
  Storage::fake('public');
  $user = User::factory()->withPersonalTeam()->create();
  $this->actingAs($user);
  // Keep the fakes in scope: their temp files are deleted when the UploadedFile is destroyed.
  $first = UploadedFile::fake()->image('first.png', 120, 120);
  $second = UploadedFile::fake()->image('second.jpg', 120, 120);

  visit('/settings/profile')
    ->attach('photo', $first->getPathname())
    ->click('@update-profile-button')
    ->assertSee('Saved.')
    ->assertNoJavaScriptErrors();

  $firstPath = $user->fresh()->profile_photo_path;
  expect($firstPath)->toStartWith($user->id.'/avatars/');
  Storage::disk('public')->assertExists($firstPath);

  visit('/settings/profile')
    ->assertSee('Remove Photo')
    ->attach('photo', $second->getPathname())
    ->click('@update-profile-button')
    ->assertSee('Saved.');

  $secondPath = $user->fresh()->profile_photo_path;
  expect($secondPath)->not->toBe($firstPath);
  Storage::disk('public')->assertExists($secondPath);
  Storage::disk('public')->assertMissing($firstPath);
});

it('rejects a file that is not an image', function (): void {
  Storage::fake('public');
  $user = User::factory()->withPersonalTeam()->create();
  $this->actingAs($user);
  $pdf = UploadedFile::fake()->create('notes.pdf', 10, 'application/pdf');

  visit('/settings/profile')
    ->attach('photo', $pdf->getPathname())
    ->click('@update-profile-button')
    ->assertSee('must be a file of type');

  expect($user->fresh()->profile_photo_path)->toBeNull();
});

it('changes the email and resets email verification', function (): void {
  $user = User::factory()->withPersonalTeam()->create();
  $this->actingAs($user);

  expect($user->email_verified_at)->not->toBeNull();

  Notification::fake();
  $newEmail = 'changed-'.uniqid().'@example.com';

  visit('/settings/profile')
    ->fill('email', $newEmail)
    ->click('@update-profile-button')
    ->assertSee('Saved.');

  $fresh = $user->fresh();
  expect($fresh->email)->toBe($newEmail);
  expect($fresh->email_verified_at)->toBeNull();
  // The new address has to be verifiable, so a verification link goes to it.
  Notification::assertSentTo($fresh, VerifyEmail::class);
});
