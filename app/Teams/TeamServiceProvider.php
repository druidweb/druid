<?php

namespace App\Teams;

use App\Http\Middleware\ShareInertiaData;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Events\PasswordUpdatedViaController;

class TeamServiceProvider extends ServiceProvider
{
  /**
   * Register any application services.
   *
   * @return void
   */
  public function register()
  {
    $this->mergeConfigFrom(dirname(__DIR__).'/config/teams.php', 'teams');
  }

  /**
   * Bootstrap any application services.
   *
   * @return void
   */
  public function boot()
  {
    if (! $this->app->runningInConsole()) {
      return;
    }

    RedirectResponse::macro('banner', function ($message): RedirectResponse {
      /** @var \Illuminate\Http\RedirectResponse $this */
      return $this->with('flash', [
        'bannerStyle' => 'success',
        'banner' => $message,
      ]);
    });

    RedirectResponse::macro('warningBanner', function ($message): RedirectResponse {
      /** @var \Illuminate\Http\RedirectResponse $this */
      return $this->with('flash', [
        'bannerStyle' => 'warning',
        'banner' => $message,
      ]);
    });

    RedirectResponse::macro('dangerBanner', function ($message): RedirectResponse {
      /** @var \Illuminate\Http\RedirectResponse $this */
      return $this->with('flash', [
        'bannerStyle' => 'danger',
        'banner' => $message,
      ]);
    });

    // Register ShareInertiaData middleware to web group
    $this->app->booted(function () {
      $router = $this->app['router'];
      $router->pushMiddlewareToGroup('web', ShareInertiaData::class);
    });

    // Listen for password updates to track session hash for "logout other devices" feature
    Event::listen(function (PasswordUpdatedViaController $event) {
      if (request()->hasSession()) {
        request()->session()->put(['password_hash_sanctum' => Auth::user()->getAuthPassword()]);
      }
    });
  }
}
