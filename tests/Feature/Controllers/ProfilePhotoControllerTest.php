<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function (): void {
  Storage::fake('public');
});

it('can delete profile photo', function (): void {
  $user = User::factory()->withPersonalTeam()->create();

  // Upload a profile photo first
  $file = UploadedFile::fake()->image('avatar.jpg');
  $user->updateProfilePhoto($file);

  expect($user->fresh()->profile_photo_path)->not->toBeNull();

  $this->actingAs($user);

  $response = $this->delete('/user/profile-photo');

  $response->assertRedirect();
  $response->assertSessionHas('status', 'profile-photo-deleted');
  expect($user->fresh()->profile_photo_path)->toBeNull();
});
