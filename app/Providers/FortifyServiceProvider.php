<?php

declare(strict_types=1);

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Laravel\Fortify\Contracts\PasswordConfirmedResponse;
use Laravel\Fortify\Features;
use Laravel\Fortify\Fortify;

final class FortifyServiceProvider extends ServiceProvider
{
  /**
   * Register any application services.
   */
  public function register(): void
  {
    $this->app->singleton(fn (): PasswordConfirmedResponse => new class implements PasswordConfirmedResponse
    {
      public function toResponse(mixed $request): RedirectResponse
      {
        return redirect()->intended('/dashboard');
      }
    });
  }

  /**
   * Bootstrap any application services.
   */
  public function boot(): void
  {
    $this->configureActions();
    $this->configureViews();
    $this->configureRateLimiting();
  }

  /**
   * Configure Fortify actions.
   */
  private function configureActions(): void
  {
    /* @chisel-registration */
    Fortify::createUsersUsing(CreateNewUser::class);
    /* @end-chisel-registration */
    /* @chisel-update-profile-information */
    Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
    /* @end-chisel-update-profile-information */
    /* @chisel-update-passwords */
    Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
    /* @end-chisel-update-passwords */
    /* @chisel-reset-passwords */
    Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
    /* @end-chisel-reset-passwords */
  }

  /**
   * Configure Fortify views.
   */
  private function configureViews(): void
  {
    Fortify::loginView(fn (Request $request) => Inertia::render('auth/Login', [
      'canResetPassword' => Features::enabled(Features::resetPasswords()),
      'canRegister' => Features::enabled(Features::registration()),
      'status' => $request->session()->get('status'),
    ]));

    /* @chisel-reset-passwords */
    Fortify::resetPasswordView(fn (Request $request) => Inertia::render('auth/ResetPassword', [
      'email' => $request->email,
      'token' => $request->route('token'),
    ]));

    Fortify::requestPasswordResetLinkView(fn (Request $request) => Inertia::render('auth/ForgotPassword', [
      'status' => $request->session()->get('status'),
    ]));
    /* @end-chisel-reset-passwords */

    /* @chisel-email-verification */
    Fortify::verifyEmailView(fn (Request $request) => Inertia::render('auth/VerifyEmail', [
      'status' => $request->session()->get('status'),
    ]));
    /* @end-chisel-email-verification */

    /* @chisel-registration */
    Fortify::registerView(fn () => Inertia::render('auth/Register'));
    /* @end-chisel-registration */

    /* @chisel-two-factor */
    Fortify::twoFactorChallengeView(fn () => Inertia::render('auth/TwoFactorChallenge'));

    Fortify::confirmPasswordView(fn (Request $request) => Inertia::render('auth/ConfirmPassword', [
      'intended' => $request->session()->get('url.intended', '/dashboard'),
    ]));
    /* @end-chisel-two-factor */
  }

  /**
   * Configure rate limiting.
   */
  private function configureRateLimiting(): void
  {
    /* @chisel-two-factor */
    RateLimiter::for('two-factor', fn (Request $request) => Limit::perMinute(5)->by($request->session()->get('login.id')));
    /* @end-chisel-two-factor */

    RateLimiter::for('login', function (Request $request) {
      /** @var string $email */
      $email = $request->input(Fortify::username(), '');
      $throttleKey = Str::transliterate(Str::lower($email).'|'.$request->ip());

      return Limit::perMinute(5)->by($throttleKey);
    });
  }
}
