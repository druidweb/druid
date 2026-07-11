<?php

declare(strict_types=1);

use App\Models\User;

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

// NOTE: the API-token mutations that confirm through a reka-ui dialog — "update permissions" and
// "delete" — are intentionally NOT E2E tests. Both were attempted repeatedly (class, dialog-scoped,
// and data-test selectors; DB-poll and UI-reflection syncs) and both stay flaky: the confirm-button
// click inside the animating dialog intermittently fails to fire the in-place action, so the change
// never lands. A randomly-failing test poisons the suite and the coverage gate. `ApiTokenController@update`
// and `@destroy` are covered by the deterministic Feature suite. Create + validation are covered here.
