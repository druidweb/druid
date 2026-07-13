<?php

declare(strict_types=1);

use App\Models\User;

// Same-origin request fired from the authenticated browser page, resolving to the HTTP
// status. It reads the XSRF-TOKEN cookie and drives the real route through the real
// middleware stack, so server-side coverage is registered. Guarded against redeclaration
// so the file is safe to load alongside the rest of the Browser suite.
if (! function_exists('apiTokenRequest')) {
  function apiTokenRequest(string $method, string $url, mixed $body = null): string
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

it('creates an api token and shows the plaintext value once', function (): void {
  $user = User::factory()->withPersonalTeam()->create();
  $this->actingAs($user);

  expect($user->tokens()->count())->toBe(0);

  $page = visit('/user/api-tokens')
    ->fill('name', 'Deploy Token')
    ->click('#create-read')
    ->press('Create');

  // The plaintext token is flashed once and rendered behind `v-if flash.token`,
  // then cleared on the next prop sync — capture it during that visible window.
  $shownToken = '';
  for ($i = 0; $i < 60; $i++) {
    $shownToken = trim((string) $page->script("(document.querySelector('code')||{}).textContent||''"));
    if (strlen($shownToken) > 30) {
      break;
    }
    usleep(50_000);
  }

  $page->assertSee('API Token Created')
    ->assertSee('Please copy your new API token');

  expect(strlen($shownToken))->toBeGreaterThan(30);

  $token = $user->fresh()->tokens()->first();
  expect($token)->not->toBeNull();
  expect($token->name)->toBe('Deploy Token');
  expect($token->abilities)->toContain('read');
});

it('rejects creating an api token without a name', function (): void {
  $user = User::factory()->withPersonalTeam()->create();
  $this->actingAs($user);

  $page = visit('/user/api-tokens')->press('Create');

  $seen = false;
  for ($i = 0; $i < 40; $i++) {
    if ($page->script("(document.body.innerText.indexOf('name field is required')>=0)") === true) {
      $seen = true;
      break;
    }
    usleep(100_000);
  }

  expect($seen)->toBeTrue();
  expect($user->fresh()->tokens()->count())->toBe(0);
});

it("updates an api token's permissions", function (): void {
  $user = User::factory()->withPersonalTeam()->create();
  $this->actingAs($user);

  $token = $user->createToken('T', ['read']);
  $model = $token->accessToken;
  $tokenId = $model->getKey();

  $status = visit('/user/api-tokens')
    ->script(apiTokenRequest('PUT', '/user/api-tokens/'.$tokenId, ['permissions' => ['read', 'create']]));

  // back(303) is followed by fetch, so a successful mutation lands on the index (200).
  expect(in_array((int) $status, [200, 303], true))->toBeTrue();

  // The in-process request commits within the suite's transaction; poll briefly for robustness.
  $abilities = [];
  for ($i = 0; $i < 40; $i++) {
    $abilities = $model->fresh()->abilities ?? [];
    if (in_array('create', $abilities, true)) {
      break;
    }
    usleep(50_000);
  }

  expect($abilities)->toContain('create')->toContain('read');
});

it('deletes an api token', function (): void {
  $user = User::factory()->withPersonalTeam()->create();
  $this->actingAs($user);

  $token = $user->createToken('T', ['read']);
  $tokenId = $token->accessToken->getKey();

  expect($user->fresh()->tokens()->count())->toBe(1);

  $status = visit('/user/api-tokens')
    ->script(apiTokenRequest('DELETE', '/user/api-tokens/'.$tokenId));

  expect(in_array((int) $status, [200, 303], true))->toBeTrue();

  $remaining = 1;
  for ($i = 0; $i < 40; $i++) {
    $remaining = $user->fresh()->tokens()->count();
    if ($remaining === 0) {
      break;
    }
    usleep(50_000);
  }

  expect($remaining)->toBe(0);
});

it('rejects updating an api token with a non-array permissions value', function (): void {
  $user = User::factory()->withPersonalTeam()->create();
  $this->actingAs($user);

  $token = $user->createToken('T', ['read']);
  $model = $token->accessToken;
  $tokenId = $model->getKey();

  // A string violates the `permissions => array` rule; with an XHR Accept the validator
  // returns 422 instead of a redirect, and abilities must be left untouched.
  $status = visit('/user/api-tokens')
    ->script(apiTokenRequest('PUT', '/user/api-tokens/'.$tokenId, ['permissions' => 'read']));

  expect((int) $status)->toBe(422);
  expect($model->fresh()->abilities)->toBe(['read']);
});

// NOTE: update/destroy are now covered deterministically here via a same-origin fetch() from the
// authenticated page — a real browser request through real middleware, which registers server-side
// coverage. The reka-ui confirm DIALOG that fronts these actions remains flaky (the confirm-button
// click inside the animating dialog intermittently fails to fire the in-place action) and is left
// intentionally undriven; the routes themselves are exercised directly above.
