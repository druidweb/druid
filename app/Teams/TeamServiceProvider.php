<?php

namespace App\Teams;

use App\Http\Middleware\ShareInertiaData;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Events\PasswordUpdatedViaController;

class TeamServiceProvider extends ServiceProvider
{
  /**
   * Register any application services.
   */
  public function register(): void
  {
    $this->mergeConfigFrom(dirname(__DIR__).'/config/teams.php', 'teams');
  }

  /**
   * Bootstrap any application services.
   */
  public function boot(): void
  {
    if (! $this->app->runningInConsole()) {
      return;
    }

    RedirectResponse::macro('banner', function ($message): RedirectResponse {
      /** @var RedirectResponse $this */
      return $this->with('flash', [
        'bannerStyle' => 'success',
        'banner' => $message,
      ]);
    });

    RedirectResponse::macro('warningBanner', function ($message): RedirectResponse {
      /** @var RedirectResponse $this */
      return $this->with('flash', [
        'bannerStyle' => 'warning',
        'banner' => $message,
      ]);
    });

    RedirectResponse::macro('dangerBanner', function ($message): RedirectResponse {
      /** @var RedirectResponse $this */
      return $this->with('flash', [
        'bannerStyle' => 'danger',
        'banner' => $message,
      ]);
    });

    // Register ShareInertiaData middleware to web group
    $this->app->booted(function (): void {
      $router = $this->app->make(Router::class);
      $router->pushMiddlewareToGroup('web', ShareInertiaData::class);
    });

    // Listen for password updates to track session hash for "logout other devices" feature
    Event::listen(function (PasswordUpdatedViaController $event): void {
      if (request()->hasSession()) {
        request()->session()->put(['password_hash_sanctum' => Auth::user()->getAuthPassword()]);
      }
    });
  }
}
