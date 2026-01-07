<?php

declare(strict_types=1);

use App\Models\User;
use App\Teams\Features;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function (): void {
  Storage::fake('public');
});

it('can update profile photo', function (): void {
  $user = User::factory()->withPersonalTeam()->create();

  $photo = UploadedFile::fake()->image('avatar.jpg');
  $user->updateProfilePhoto($photo);

  expect($user->profile_photo_path)->not->toBeNull();
  Storage::disk('public')->assertExists($user->profile_photo_path);
});

it('deletes old photo when updating', function (): void {
  $user = User::factory()->withPersonalTeam()->create();

  $photo1 = UploadedFile::fake()->image('avatar1.jpg');
  $user->updateProfilePhoto($photo1);
  $oldPath = $user->profile_photo_path;

  $photo2 = UploadedFile::fake()->image('avatar2.jpg');
  $user->updateProfilePhoto($photo2);

  Storage::disk('public')->assertMissing($oldPath);
  Storage::disk('public')->assertExists($user->profile_photo_path);
});

it('can delete profile photo', function (): void {
  $user = User::factory()->withPersonalTeam()->create();

  $photo = UploadedFile::fake()->image('avatar.jpg');
  $user->updateProfilePhoto($photo);
  $photoPath = $user->profile_photo_path;

  $user->deleteProfilePhoto();

  expect($user->profile_photo_path)->toBeNull();
  Storage::disk('public')->assertMissing($photoPath);
});

it('returns default profile photo url when no photo uploaded', function (): void {
  $user = User::factory()->withPersonalTeam()->create(['name' => 'John Doe']);

  $url = $user->profile_photo_url;

  expect($url)->toContain('ui-avatars.com');
  expect($url)->toContain('name=');
});

it('returns storage url when photo is uploaded', function (): void {
  $user = User::factory()->withPersonalTeam()->create();

  $photo = UploadedFile::fake()->image('avatar.jpg');
  $user->updateProfilePhoto($photo);

  $url = $user->profile_photo_url;

  expect($url)->toContain($user->profile_photo_path);
});
it('does nothing when deleting profile photo and feature is disabled', function (): void {
  $user = User::factory()->withPersonalTeam()->create();

  $photo = UploadedFile::fake()->image('avatar.jpg');
  $user->updateProfilePhoto($photo);
  $photoPath = $user->profile_photo_path;

  // Disable profile photos feature via config
  $features = config('teams.features', []);
  $featuresWithoutPhotos = array_values(array_filter($features, fn ($f): bool => $f !== Features::profilePhotos()));
  config(['teams.features' => $featuresWithoutPhotos]);

  // Try to delete - should do nothing since feature is disabled
  $user->deleteProfilePhoto();

  // Photo path should still exist on user (not cleared)
  expect($user->profile_photo_path)->toBe($photoPath);
  Storage::disk('public')->assertExists($photoPath);

  // Restore features
  config(['teams.features' => $features]);
});
