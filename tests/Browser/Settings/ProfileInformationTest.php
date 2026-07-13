<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;

// Same-origin JSON request from the authenticated browser session, resolving to the HTTP
// status. The Fortify PUT /user/profile-information endpoint (backed by
// App\Actions\Fortify\UpdateUserProfileInformation) has NO UI in this app — the Profile page
// posts to ProfileController@update instead — so a same-origin fetch is the only browser way
// to reach it. Guarded against redeclaration when the whole Browser suite loads every file.
if (! function_exists('profileInfoRequest')) {
  function profileInfoRequest(string $method, string $url, ?array $body = null): string
  {
    $bodyJs = $body === null ? 'undefined' : json_encode($body);

    return <<<JS
      (function () {
        const match = document.cookie.match(/XSRF-TOKEN=([^;]+)/);
        const token = match ? decodeURIComponent(match[1]) : '';
        const options = {
          method: '{$method}',
          headers: {
            'X-XSRF-TOKEN': token,
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          }
        };
        const requestBody = {$bodyJs};
        if (requestBody !== undefined) { options.body = JSON.stringify(requestBody); }
        return fetch('{$url}', options).then(function (response) { return response.status; });
      })()
    JS;
  }
}

it('updates name via the fortify profile-information endpoint', function (): void {
  $user = User::factory()->withPersonalTeam()->create(['name' => 'Old Name']);
  $this->actingAs($user);

  // Email unchanged -> the else/forceFill branch of UpdateUserProfileInformation@update.
  $status = visit('/settings/profile')->script(profileInfoRequest('PUT', '/user/profile-information', [
    'name' => 'New Name',
    'email' => $user->email,
  ]));

  expect((int) $status)->toBe(200);

  $fresh = $user->fresh();
  expect($fresh->name)->toBe('New Name');
  // Email was untouched, so verification must NOT have been reset.
  expect($fresh->email_verified_at)->not->toBeNull();
});

it('resets email verification when the email changes via profile-information', function (): void {
  Notification::fake();

  $user = User::factory()->withPersonalTeam()->create();
  $this->actingAs($user);

  expect($user->email_verified_at)->not->toBeNull();

  $newEmail = 'new-'.uniqid().'@example.com';

  // Email changed -> the updateVerifiedUser branch: nulls email_verified_at and
  // sends a verification notification.
  $status = visit('/settings/profile')->script(profileInfoRequest('PUT', '/user/profile-information', [
    'name' => 'Renamed User',
    'email' => $newEmail,
  ]));

  expect((int) $status)->toBe(200);

  $fresh = $user->fresh();
  expect($fresh->email)->toBe($newEmail);
  expect($fresh->name)->toBe('Renamed User');
  expect($fresh->email_verified_at)->toBeNull();

  Notification::assertSentTo($fresh, VerifyEmail::class);
});

it('rejects invalid input on the profile-information endpoint', function (): void {
  $user = User::factory()->withPersonalTeam()->create(['name' => 'Keep Me']);
  $this->actingAs($user);
  $originalEmail = $user->email;

  // Blank name + malformed email fail validateWithBag('updateProfileInformation') -> 422.
  $status = visit('/settings/profile')->script(profileInfoRequest('PUT', '/user/profile-information', [
    'name' => '',
    'email' => 'not-an-email',
  ]));

  expect((int) $status)->toBe(422);

  $fresh = $user->fresh();
  expect($fresh->name)->toBe('Keep Me');
  expect($fresh->email)->toBe($originalEmail);
});
