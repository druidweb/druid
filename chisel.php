<?php

declare(strict_types=1);

require getenv('LARAVEL_INSTALLER_AUTOLOADER') ?: __DIR__.'/vendor/autoload.php';

use Laravel\Chisel\Chisel;
use Laravel\Chisel\Question;

return Chisel::script(__DIR__)
  ->questions([
    Question::multiselect(
      name: 'features',
      label: 'Which optional features would you like to include?',
      options: [
        'teams' => 'Teams — Multi-user workspace support',
        'api-tokens' => 'API Tokens — Personal access token management',
        'profile-photos' => 'Profile Photos — User avatar uploads',
        'terms' => 'Terms & Privacy — Legal document pages',
        'account-deletion' => 'Account Deletion — Self-service account removal',
        'email-verification' => 'Email Verification — Enforce verified email on protected routes',
      ],
      hint: 'Use space to select, enter to confirm.',
    ),
  ])
  ->selected('features', 'teams',
    then: fn (Chisel $c) => null,
    else: function (Chisel $c): void {
      $c->files(
        'app/Actions/Teams/AddTeamMember.php',
        'app/Actions/Teams/CreateTeam.php',
        'app/Actions/Teams/DeleteTeam.php',
        'app/Actions/Teams/DeleteUserWithTeams.php',
        'app/Actions/Teams/InviteTeamMember.php',
        'app/Actions/Teams/RemoveTeamMember.php',
        'app/Actions/Teams/UpdateTeamMemberRole.php',
        'app/Actions/Teams/UpdateTeamName.php',
        'app/Actions/Teams/ValidateTeamDeletion.php',
      )->delete();
      $c->file('app/Concerns/HasTeams.php')->delete();
      $c->files(
        'app/Contracts/AddsTeamMembers.php',
        'app/Contracts/CreatesTeams.php',
        'app/Contracts/DeletesTeams.php',
        'app/Contracts/InvitesTeamMembers.php',
        'app/Contracts/RemovesTeamMembers.php',
        'app/Contracts/UpdatesTeamNames.php',
      )->delete();
      $c->files(
        'app/Http/Controllers/Teams/CurrentTeamController.php',
        'app/Http/Controllers/Teams/TeamController.php',
        'app/Http/Controllers/Teams/TeamInvitationController.php',
        'app/Http/Controllers/Teams/TeamMemberController.php',
      )->delete();
      $c->files(
        'app/Events/AddingTeam.php',
        'app/Events/AddingTeamMember.php',
        'app/Events/InvitingTeamMember.php',
        'app/Events/RemovingTeamMember.php',
        'app/Events/TeamCreated.php',
        'app/Events/TeamDeleted.php',
        'app/Events/TeamEvent.php',
        'app/Events/TeamMemberAdded.php',
        'app/Events/TeamMemberRemoved.php',
        'app/Events/TeamMemberUpdated.php',
        'app/Events/TeamUpdated.php',
      )->delete();
      $c->file('app/Mail/TeamInvitation.php')->delete();
      $c->files(
        'app/Models/Membership.php',
        'app/Models/Team.php',
        'app/Models/TeamInvitation.php',
      )->delete();
      $c->file('app/Policies/TeamPolicy.php')->delete();
      $c->file('app/Providers/TeamServiceProvider.php')->delete();
      $c->files(
        'app/Rules/OwnerRole.php',
        'app/Rules/Role.php',
      )->delete();
      // App\Teams\Teams stays alive — it is the runtime feature-flag
      // dispatcher (Teams::hasTeamFeatures(), hasApiFeatures(), etc.) that
      // surviving middleware and controllers still call. Its config-driven
      // methods return false after stripping, so the gates evaluate inert.
      $c->files(
        'app/Teams/Agent.php',
        'app/Teams/Role.php',
      )->delete();
      $c->files(
        'resources/js/pages/teams/Create.vue',
        'resources/js/pages/teams/Show.vue',
        'resources/js/pages/teams/Partials/CreateTeamForm.vue',
        'resources/js/pages/teams/Partials/DeleteTeamForm.vue',
        'resources/js/pages/teams/Partials/TeamMemberManager.vue',
        'resources/js/pages/teams/Partials/UpdateTeamNameForm.vue',
      )->delete();
      $c->files(
        'resources/js/components/TeamIndicatorBanner.vue',
        'resources/js/components/TeamSwitcher.vue',
      )->delete();
      $c->files(
        'database/migrations/0001_01_01_000003_create_teams_table.php',
        'database/migrations/0001_01_01_000004_create_team_user_table.php',
        'database/migrations/0001_01_01_000005_create_team_invitations_table.php',
      )->delete();
      $c->files(
        'tests/Feature/Teams/AgentTest.php',
        'tests/Feature/Teams/CreateTeamTest.php',
        'tests/Feature/Teams/DeleteTeamTest.php',
        'tests/Feature/Teams/InviteTeamMemberTest.php',
        'tests/Feature/Teams/LeaveTeamTest.php',
        'tests/Feature/Teams/RemoveTeamMemberTest.php',
        'tests/Feature/Teams/TeamsTest.php',
        'tests/Feature/Teams/UpdateTeamNameTest.php',
        'tests/Feature/UpdateTeamMemberRoleTest.php',
        'tests/Feature/Controllers/CurrentTeamControllerTest.php',
        'tests/Feature/Controllers/TeamControllerTest.php',
        'tests/Feature/Controllers/TeamInvitationControllerTest.php',
        'tests/Feature/Controllers/TeamMemberControllerTest.php',
        'tests/Feature/Actions/Teams/AddTeamMemberTest.php',
        'tests/Feature/Actions/Teams/DeleteUserWithTeamsTest.php',
        'tests/Feature/Policies/TeamPolicyTest.php',
        'tests/Feature/Events/TeamEventsTest.php',
        'tests/Feature/Models/TeamTest.php',
        'tests/Feature/Providers/TeamServiceProviderTest.php',
        'tests/Feature/Concerns/HasTeamsTest.php',
        'tests/Feature/Rules/RoleRulesTest.php',
      )->delete();
      $c->file('config/teams.php')->removeLinesContaining('Features::teams(');
      $c->file('bootstrap/providers.php')->removeLinesContaining('TeamServiceProvider::class');
      $c->php('app/Models/User.php')
        ->removeImport('App\\Concerns\\HasTeams')
        ->removeTrait('HasTeams');
    },
  )
  ->selected('features', 'api-tokens',
    then: fn (Chisel $c) => null,
    else: function (Chisel $c): void {
      $c->file('app/Http/Controllers/Api/ApiTokenController.php')->delete();
      $c->file('app/Models/PersonalAccessToken.php')->delete();
      $c->files(
        'resources/js/pages/api/Index.vue',
        'resources/js/pages/api/Partials/ApiTokenManager.vue',
      )->delete();
      $c->file('database/migrations/0001_01_01_000006_create_personal_access_tokens_table.php')->delete();
      $c->files(
        'tests/Feature/Api/ApiTokenPermissionsTest.php',
        'tests/Feature/Api/CreateApiTokenTest.php',
        'tests/Feature/Api/DeleteApiTokenTest.php',
        'tests/Feature/Controllers/ApiTokenControllerTest.php',
      )->delete();
      $c->file('config/teams.php')->removeLinesContaining('Features::api(');
      $c->file('app/Providers/AppServiceProvider.php')
        ->removeLinesContaining('Sanctum::usePersonalAccessTokenModel');
      $c->php('app/Providers/AppServiceProvider.php')
        ->removeImport('App\\Models\\PersonalAccessToken')
        ->removeImport('Laravel\\Sanctum\\Sanctum');
      $c->php('app/Models/User.php')
        ->removeImport('Laravel\\Sanctum\\HasApiTokens')
        ->removeTrait('HasApiTokens');
    },
  )
  ->selected('features', 'profile-photos',
    then: fn (Chisel $c) => null,
    else: function (Chisel $c): void {
      $c->file('app/Http/Controllers/Settings/ProfilePhotoController.php')->delete();
      $c->file('app/Concerns/HasProfilePhoto.php')->delete();
      $c->files(
        'tests/Feature/Concerns/HasProfilePhotoTest.php',
        'tests/Feature/Controllers/ProfilePhotoControllerTest.php',
      )->delete();
      $c->file('config/teams.php')->removeLinesContaining('Features::profilePhotos(');
      $c->file('resources/js/pages/settings/Profile.vue')
        ->removeSection('profile-photos')
        ->replace('@success="clearPhotoFileInput"', '');
      $c->php('app/Models/User.php')
        ->removeImport('App\\Concerns\\HasProfilePhoto')
        ->removeTrait('HasProfilePhoto');
      $c->file('app/Models/User.php')
        ->replace(
          "#[Appends([\n  'profile_photo_url',\n])]",
          '',
        );
    },
  )
  ->selected('features', 'terms',
    then: fn (Chisel $c) => null,
    else: function (Chisel $c): void {
      $c->files(
        'app/Http/Controllers/TermsOfServiceController.php',
        'app/Http/Controllers/PrivacyPolicyController.php',
      )->delete();
      $c->files(
        'resources/js/pages/TermsOfService.vue',
        'resources/js/pages/PrivacyPolicy.vue',
      )->delete();
      $c->files(
        'resources/markdown/terms.md',
        'resources/markdown/policy.md',
      )->delete();
      $c->files(
        'tests/Feature/Controllers/TermsOfServiceControllerTest.php',
        'tests/Feature/Controllers/PrivacyPolicyControllerTest.php',
      )->delete();
      $c->file('config/teams.php')->removeLinesContaining('Features::termsAndPrivacyPolicy(');
      $c->file('resources/js/pages/auth/Register.vue')->removeSection('terms');
    },
  )
  ->selected('features', 'account-deletion',
    then: fn (Chisel $c) => null,
    else: function (Chisel $c): void {
      $c->file('app/Actions/Teams/DeleteUser.php')->delete();
      $c->file('app/Contracts/DeletesUsers.php')->delete();
      $c->file('resources/js/components/DeleteUser.vue')->delete();
      $c->file('tests/Feature/Settings/DeleteAccountTest.php')->delete();
      $c->file('tests/Vue/components/DeleteUser.test.ts')->delete();
      $c->file('config/teams.php')->removeLinesContaining('Features::accountDeletion(');
      $c->file('app/Providers/TeamServiceProvider.php')
        ->removeLinesContaining('Teams::deleteUsersUsing');
      $c->php('app/Providers/TeamServiceProvider.php')
        ->removeImport('App\\Actions\\Teams\\DeleteUser');
      $c->file('resources/js/pages/settings/Profile.vue')->removeSection('account-deletion');
      $c->file('routes/settings.php')->removeSection('account-deletion');
      $c->file('tests/Feature/Settings/ProfileUpdateTest.php')->removeSection('account-deletion');
    },
  )
  ->selected('features', 'email-verification',
    then: fn (Chisel $c) => null,
    else: function (Chisel $c): void {
      $c->file('resources/js/pages/auth/VerifyEmail.vue')->delete();
      $c->files(
        'tests/EmailVerificationTest.php',
        'tests/Feature/Auth/EmailVerificationTest.php',
        'tests/Feature/Auth/VerificationNotificationTest.php',
      )->delete();
      $c->file('app/Providers/FortifyServiceProvider.php')->removeSection('email-verification');
      $c->file('tests/Feature/Providers/FortifyServiceProviderTest.php')->removeSection('email-verification');
      $c->file('resources/js/pages/settings/Profile.vue')->removeSection('email-verification');
      $c->php('app/Models/User.php')
        ->removeImport('Illuminate\\Contracts\\Auth\\MustVerifyEmail')
        ->removeInterface('MustVerifyEmail');
    },
  )
  ->apply(function (Chisel $c): void {
    $c->file('composer.json')
      ->removeLinesContaining('"@php artisan install:features --ansi"');
    $c->files(
      'app/Console/Commands/InstallFeaturesCommand.php',
      'tests/Feature/Console/InstallFeaturesCommandTest.php',
      'chisel.php',
    )->delete();
  });
