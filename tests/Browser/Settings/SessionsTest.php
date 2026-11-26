<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Hash;

it('shows the browser sessions page and opens the logout confirmation', function (): void {
  $user = User::factory()->withPersonalTeam()->create();
  $this->actingAs($user);

  visit('/user/other-browser-sessions')
    ->assertSee('Browser Sessions')
    ->assertSee('This device')
    ->assertSee('Chrome')
    ->assertNoJavaScriptErrors()
    ->click('Log Out Other Browser Sessions')
    ->assertPresent('[data-slot="dialog-content"] input[name="password"]');
});

it('logs out other browser sessions after confirming the password', function (): void {
  $user = User::factory()->withPersonalTeam()->create();
  $this->actingAs($user);

  $originalHash = $user->fresh()->password;

  visit('/user/other-browser-sessions')
    ->click('Log Out Other Browser Sessions')
    ->fill('password', 'password')
    ->click('[data-slot="dialog-content"] button[type="submit"]')
    ->assertMissing('[data-slot="dialog-content"]');

  // logoutOtherDevices re-hashes the current password (invalidating other
  // sessions), so the stored hash changes but still verifies the password.
  $fresh = $user->fresh();
  expect($fresh->password)->not->toBe($originalHash);
  expect(Hash::check('password', $fresh->password))->toBeTrue();
});

it('rejects logging out other sessions with a wrong password', function (): void {
  $user = User::factory()->withPersonalTeam()->create();
  $this->actingAs($user);

  $originalHash = $user->fresh()->password;

  visit('/user/other-browser-sessions')
    ->click('Log Out Other Browser Sessions')
    ->fill('password', 'wrong-password')
    ->click('[data-slot="dialog-content"] button[type="submit"]')
    ->assertSee('password is incorrect');

  expect($user->fresh()->password)->toBe($originalHash);
});

it('describes a phone session with its platform, browser and a phone icon', function (): void {
  $this->actingAs(User::factory()->withPersonalTeam()->create());

  visit('/user/other-browser-sessions')
    ->withUserAgent('Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1')
    ->assertSee('iOS - Safari')
    ->assertPresent('svg.lucide-smartphone')
    ->assertMissing('svg.lucide-monitor')
    ->assertNoJavaScriptErrors();
});

it('falls back to unknown for an unrecognised client', function (): void {
  $this->actingAs(User::factory()->withPersonalTeam()->create());

  visit('/user/other-browser-sessions')
    ->withUserAgent('acme-kiosk/2.1')
    ->assertSee('Unknown - Unknown')
    ->assertSee('This device');
});
