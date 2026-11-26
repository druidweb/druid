<?php

declare(strict_types=1);

namespace App\Providers;

use App\Actions\Teams\AddTeamMember;
use App\Actions\Teams\CreateTeam;
use App\Actions\Teams\DeleteTeam;
use App\Actions\Teams\DeleteUser;
use App\Actions\Teams\InviteTeamMember;
use App\Actions\Teams\RemoveTeamMember;
use App\Actions\Teams\UpdateTeamName;
use App\Models\Membership;
use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\User;
use App\Teams\Teams;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Events\PasswordUpdatedViaController;

final class TeamServiceProvider extends ServiceProvider
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
    // Explicit so a downstream app swaps a model in one obvious place.
    Teams::useUserModel(User::class);
    Teams::useTeamModel(Team::class);
    Teams::useMembershipModel(Membership::class);
    Teams::useTeamInvitationModel(TeamInvitation::class);

    if (! config('teams.routes')) {
      Teams::ignoreRoutes();
    }

    $this->configurePermissions();

    Teams::createTeamsUsing(CreateTeam::class);
    Teams::updateTeamNamesUsing(UpdateTeamName::class);
    Teams::addTeamMembersUsing(AddTeamMember::class);
    Teams::inviteTeamMembersUsing(InviteTeamMember::class);
    Teams::removeTeamMembersUsing(RemoveTeamMember::class);
    Teams::deleteTeamsUsing(DeleteTeam::class);
    Teams::deleteUsersUsing(DeleteUser::class);

    // Flashes a banner the frontend shows as a toast (see resources/js/composables/useFlashBanner.ts).
    RedirectResponse::macro('banner', function (string $message, string $style = 'success'): RedirectResponse {
      /** @var RedirectResponse $this */
      return $this->with('flash', [
        'bannerStyle' => $style,
        'banner' => $message,
      ]);
    });

    // Listen for password updates to track session hash for "logout other devices" feature
    Event::listen(function (PasswordUpdatedViaController $event): void {
      if (request()->hasSession()) {
        /** @var User|null $user */
        $user = Auth::user();
        if ($user) {
          request()->session()->put(['password_hash_sanctum' => $user->getAuthPassword()]);
        }
      }
    });
  }

  /**
   * Configure the roles and permissions that are available within the application.
   */
  private function configurePermissions(): void
  {
    // The full API token vocabulary; each role below grants a subset of it.
    Teams::permissions(['create', 'read', 'update', 'delete']);

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
