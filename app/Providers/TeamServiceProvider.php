<?php

namespace App\Providers;

use App\Actions\Teams\AddTeamMember;
use App\Actions\Teams\CreateTeam;
use App\Actions\Teams\DeleteTeam;
use App\Actions\Teams\DeleteUser;
use App\Actions\Teams\InviteTeamMember;
use App\Actions\Teams\RemoveTeamMember;
use App\Actions\Teams\UpdateTeamName;
use App\Teams\Teams;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Events\PasswordUpdatedViaController;

class TeamServiceProvider extends ServiceProvider
{
  /**
   * Register any application services.
   */
  public function register(): void {}

  /**
   * Bootstrap any application services.
   */
  public function boot(): void
  {
    $this->configurePermissions();

    Teams::createTeamsUsing(CreateTeam::class);
    Teams::updateTeamNamesUsing(UpdateTeamName::class);
    Teams::addTeamMembersUsing(AddTeamMember::class);
    Teams::inviteTeamMembersUsing(InviteTeamMember::class);
    Teams::removeTeamMembersUsing(RemoveTeamMember::class);
    Teams::deleteTeamsUsing(DeleteTeam::class);
    Teams::deleteUsersUsing(DeleteUser::class);

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

    // Listen for password updates to track session hash for "logout other devices" feature
    Event::listen(function (PasswordUpdatedViaController $event): void {
      if (request()->hasSession()) {
        request()->session()->put(['password_hash_sanctum' => Auth::user()->getAuthPassword()]);
      }
    });
  }

  /**
   * Configure the roles and permissions that are available within the application.
   */
  protected function configurePermissions(): void
  {
    Teams::defaultApiTokenPermissions(['read']);

    Teams::role('admin', 'Administrator', [
      'create',
      'read',
      'update',
      'delete',
    ])->description('Administrator users can perform any action.');

    Teams::role('editor', 'Editor', [
      'read',
      'create',
      'update',
    ])->description('Editor users have the ability to read, create, and update.');
  }
}
