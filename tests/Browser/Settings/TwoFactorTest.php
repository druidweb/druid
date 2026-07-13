<?php

declare(strict_types=1);

use App\Models\User;
use Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider;
use Laravel\Fortify\Fortify;
use PragmaRX\Google2FA\Google2FA;

// Returns the current valid TOTP for a user whose two_factor_secret is set.
if (! function_exists('currentTwoFactorOtp')) {
  function currentTwoFactorOtp(User $user): string
  {
    $secret = Fortify::currentEncrypter()->decrypt($user->fresh()->two_factor_secret);

    return resolve(Google2FA::class)->getCurrentOtp($secret);
  }
}

// Force a user into a fully enabled + confirmed 2FA state with known recovery codes.
if (! function_exists('enableTwoFactorFor')) {
  function enableTwoFactorFor(User $user, array $recoveryCodes): void
  {
    $secret = resolve(TwoFactorAuthenticationProvider::class)->generateSecretKey();

    $user->forceFill([
      'two_factor_secret' => Fortify::currentEncrypter()->encrypt($secret),
      'two_factor_recovery_codes' => Fortify::currentEncrypter()->encrypt(json_encode($recoveryCodes)),
      'two_factor_confirmed_at' => now(),
    ])->save();
  }
}

it('gates the two-factor page behind password confirmation and rejects a wrong password', function (): void {
  $user = User::factory()->withPersonalTeam()->create();
  $this->actingAs($user);

  visit('/settings/two-factor')
    ->assertSee('Confirm your password')
    ->fill('password', 'wrong-password')
    ->click('@confirm-password-button')
    ->assertSee('provided password was incorrect')
    ->fill('password', 'password')
    ->click('@confirm-password-button')
    ->assertSee('Enable 2FA');
});

it('enables two-factor authentication after confirming a valid code', function (): void {
  $user = User::factory()->withPersonalTeam()->create();
  $this->actingAs($user);

  $page = visit('/settings/two-factor')
    ->fill('password', 'password')
    ->click('@confirm-password-button')
    ->assertSee('Enable 2FA')
    ->click('Enable 2FA')
    ->assertSee('scan the QR code')
    ->click('[data-slot="dialog-content"] button.w-full')
    ->assertSee('Verify Authentication Code');

  // The enable request has now created the encrypted secret.
  expect($user->fresh()->two_factor_secret)->not->toBeNull();
  expect($user->fresh()->two_factor_confirmed_at)->toBeNull();

  $otp = currentTwoFactorOtp($user);

  foreach (str_split($otp) as $index => $digit) {
    $page->type('[aria-label="pin input '.($index + 1).' of 6"]', $digit);
  }

  $page->click('[data-slot="dialog-content"] button[type="submit"]')
    ->assertSee('Disable 2FA');

  expect($user->fresh()->two_factor_confirmed_at)->not->toBeNull();
});

it('rejects an invalid confirmation code and leaves two-factor disabled', function (): void {
  $user = User::factory()->withPersonalTeam()->create();
  $this->actingAs($user);

  $page = visit('/settings/two-factor')
    ->fill('password', 'password')
    ->click('@confirm-password-button')
    ->assertSee('Enable 2FA')
    ->click('Enable 2FA')
    ->assertSee('scan the QR code')
    ->click('[data-slot="dialog-content"] button.w-full')
    ->assertSee('Verify Authentication Code');

  // Build a code guaranteed to differ from the real current OTP.
  $otp = currentTwoFactorOtp($user);
  $wrong = ((int) $otp[0] === 0 ? '1' : '0').substr($otp, 1);

  foreach (str_split($wrong) as $index => $digit) {
    $page->type('[aria-label="pin input '.($index + 1).' of 6"]', $digit);
  }

  $page->click('[data-slot="dialog-content"] button[type="submit"]')
    ->assertSee('The provided two factor authentication code was invalid.');

  expect($user->fresh()->two_factor_confirmed_at)->toBeNull();
});

it('views and regenerates recovery codes', function (): void {
  $user = User::factory()->withPersonalTeam()->create();
  enableTwoFactorFor($user, [
    'AAAA1-BBBB1', 'CCCC2-DDDD2', 'EEEE3-FFFF3', 'GGGG4-HHHH4',
    'IIII5-JJJJ5', 'KKKK6-LLLL6', 'MMMM7-NNNN7', 'OOOO8-PPPP8',
  ]);
  $this->actingAs($user);

  $page = visit('/settings/two-factor')
    ->fill('password', 'password')
    ->click('@confirm-password-button')
    ->assertSee('Disable 2FA')
    ->click('View Recovery Codes')
    ->assertSee('AAAA1-BBBB1');

  $before = $user->fresh()->two_factor_recovery_codes;

  $page->click('Regenerate Codes')
    ->assertDontSee('AAAA1-BBBB1');

  expect($user->fresh()->two_factor_recovery_codes)->not->toBe($before);
});

it('disables two-factor authentication', function (): void {
  $user = User::factory()->withPersonalTeam()->create();
  enableTwoFactorFor($user, [
    'AAAA1-BBBB1', 'CCCC2-DDDD2', 'EEEE3-FFFF3', 'GGGG4-HHHH4',
    'IIII5-JJJJ5', 'KKKK6-LLLL6', 'MMMM7-NNNN7', 'OOOO8-PPPP8',
  ]);
  $this->actingAs($user);

  visit('/settings/two-factor')
    ->fill('password', 'password')
    ->click('@confirm-password-button')
    ->assertSee('Disable 2FA')
    ->click('Disable 2FA')
    ->assertSee('Enable 2FA');

  expect($user->fresh()->two_factor_secret)->toBeNull();
  expect($user->fresh()->two_factor_confirmed_at)->toBeNull();
});
