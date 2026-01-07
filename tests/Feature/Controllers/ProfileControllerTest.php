<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function (): void {
  $this->withoutVite();
  Storage::fake('public');
});

it('displays profile page', function (): void {
  $user = User::factory()->withPersonalTeam()->create();

  $this->actingAs($user);

  $response = $this->get('/settings/profile');

  $response->assertOk();
  $response->assertInertia(fn ($page) => $page->component('settings/Profile'));
});

it('can update profile information', function (): void {
  $user = User::factory()->withPersonalTeam()->create();

  $this->actingAs($user);

  $response = $this->put('/user/profile-information', [
    'name' => 'Updated Name',
    'email' => $user->email,
  ]);

  $response->assertRedirect();
  expect($user->fresh()->name)->toBe('Updated Name');
});

it('can update profile with photo', function (): void {
  $user = User::factory()->withPersonalTeam()->create();

  $this->actingAs($user);

  $photo = UploadedFile::fake()->image('avatar.jpg');

  $response = $this->put('/user/profile-information', [
    'name' => 'Updated Name',
    'email' => $user->email,
    'photo' => $photo,
  ]);

  $response->assertRedirect();
  expect($user->fresh()->name)->toBe('Updated Name');
  expect($user->fresh()->profile_photo_path)->not->toBeNull();
});

it('can update profile via settings route', function (): void {
  $user = User::factory()->withPersonalTeam()->create();

  $this->actingAs($user);

  $photo = UploadedFile::fake()->image('avatar.jpg');

  $response = $this->patch('/settings/profile', [
    'name' => 'Updated Name',
    'email' => $user->email,
    'photo' => $photo,
  ]);

  $response->assertRedirect();
  expect($user->fresh()->name)->toBe('Updated Name');
  expect($user->fresh()->profile_photo_path)->not->toBeNull();
});
