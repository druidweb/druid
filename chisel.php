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
        'registration' => 'Registration — User signup and the entire authentication stack (deselect for a presentation-only site)',
        'reset-passwords' => 'Reset Passwords — Forgot / reset password flow',
        'update-passwords' => 'Update Passwords — Change password from settings',
        'two-factor' => 'Two-Factor Authentication — TOTP enrollment, challenge, recovery codes',
        'email-verification' => 'Email Verification — Enforce verified email on protected routes',
        'profile-photos' => 'Profile Photos — User avatar uploads',
        'account-deletion' => 'Account Deletion — Self-service account removal',
        'teams' => 'Teams — Multi-user workspace support',
        'api-tokens' => 'API Tokens — Personal access token management',
        'terms' => 'Terms & Privacy — Legal document pages',
      ],
      default: [
        'registration', 'reset-passwords', 'update-passwords', 'two-factor',
        'email-verification', 'profile-photos', 'account-deletion',
        'teams', 'api-tokens', 'terms',
      ],
      hint: 'Use space to select, enter to confirm.',
    ),
  ])

  // ===========================================================================
  // REGISTRATION — root toggle. Deselecting strips the ENTIRE auth/user stack.
  // Subsequent feature blocks become no-ops thanks to Chisel's defensive
  // file_exists() checks. This block must run FIRST.
  // ===========================================================================
  ->selected('features', 'registration',
    then: fn (Chisel $c) => null,
    else: function (Chisel $c): void {
      // Fortify actions
      $c->files(
        'app/Actions/Fortify/ConfirmPassword.php',
        'app/Actions/Fortify/CreateNewUser.php',
        'app/Actions/Fortify/PasswordValidationRules.php',
        'app/Actions/Fortify/ResetUserPassword.php',
        'app/Actions/Fortify/UpdateUserPassword.php',
        'app/Actions/Fortify/UpdateUserProfileInformation.php',
      )->delete();

      // Team actions
      $c->files(
        'app/Actions/Teams/AddTeamMember.php',
        'app/Actions/Teams/CreateTeam.php',
        'app/Actions/Teams/DeleteTeam.php',
        'app/Actions/Teams/DeleteUser.php',
        'app/Actions/Teams/DeleteUserWithTeams.php',
        'app/Actions/Teams/InviteTeamMember.php',
        'app/Actions/Teams/RemoveTeamMember.php',
        'app/Actions/Teams/UpdateTeamMemberRole.php',
        'app/Actions/Teams/UpdateTeamName.php',
        'app/Actions/Teams/ValidateTeamDeletion.php',
      )->delete();

      // Concerns
      $c->files(
        'app/Concerns/HasProfilePhoto.php',
        'app/Concerns/HasTeams.php',
      )->delete();

      // Contracts
      $c->files(
        'app/Contracts/AddsTeamMembers.php',
        'app/Contracts/CreatesTeams.php',
        'app/Contracts/DeletesTeams.php',
        'app/Contracts/DeletesUsers.php',
        'app/Contracts/InvitesTeamMembers.php',
        'app/Contracts/RemovesTeamMembers.php',
        'app/Contracts/UpdatesTeamNames.php',
      )->delete();

      // Controllers — auth/settings/teams/api/legal/dashboard
      $c->files(
        'app/Http/Controllers/Api/ApiTokenController.php',
        'app/Http/Controllers/DashboardController.php',
        'app/Http/Controllers/PrivacyPolicyController.php',
        'app/Http/Controllers/Settings/OtherBrowserSessionsController.php',
        'app/Http/Controllers/Settings/ProfileController.php',
        'app/Http/Controllers/Settings/ProfilePhotoController.php',
        'app/Http/Controllers/Settings/TwoFactorAuthenticationController.php',
        'app/Http/Controllers/Teams/CurrentTeamController.php',
        'app/Http/Controllers/Teams/TeamController.php',
        'app/Http/Controllers/Teams/TeamInvitationController.php',
        'app/Http/Controllers/Teams/TeamMemberController.php',
        'app/Http/Controllers/TermsOfServiceController.php',
      )->delete();

      // Form requests
      $c->files(
        'app/Http/Requests/Settings/ProfileUpdateRequest.php',
        'app/Http/Requests/Settings/TwoFactorAuthenticationRequest.php',
      )->delete();

      // Events
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

      // Mail
      $c->file('app/Mail/TeamInvitation.php')->delete();

      // Models
      $c->files(
        'app/Models/Membership.php',
        'app/Models/PersonalAccessToken.php',
        'app/Models/Team.php',
        'app/Models/TeamInvitation.php',
        'app/Models/User.php',
      )->delete();

      // Policies
      $c->file('app/Policies/TeamPolicy.php')->delete();

      // Providers
      $c->files(
        'app/Providers/FortifyServiceProvider.php',
        'app/Providers/TeamServiceProvider.php',
      )->delete();

      // Rules
      $c->files(
        'app/Rules/OwnerRole.php',
        'app/Rules/Role.php',
      )->delete();

      // Teams runtime
      $c->files(
        'app/Teams/Agent.php',
        'app/Teams/Features.php',
        'app/Teams/Role.php',
        'app/Teams/Teams.php',
      )->delete();

      // Auth pages
      $c->files(
        'resources/js/pages/auth/ConfirmPassword.vue',
        'resources/js/pages/auth/ForgotPassword.vue',
        'resources/js/pages/auth/Login.vue',
        'resources/js/pages/auth/Register.vue',
        'resources/js/pages/auth/ResetPassword.vue',
        'resources/js/pages/auth/TwoFactorChallenge.vue',
        'resources/js/pages/auth/VerifyEmail.vue',
      )->delete();

      // Settings pages
      $c->files(
        'resources/js/pages/settings/Appearance.vue',
        'resources/js/pages/settings/Password.vue',
        'resources/js/pages/settings/Profile.vue',
        'resources/js/pages/settings/Sessions.vue',
        'resources/js/pages/settings/TwoFactor.vue',
      )->delete();

      // Teams pages
      $c->files(
        'resources/js/pages/teams/Create.vue',
        'resources/js/pages/teams/Show.vue',
        'resources/js/pages/teams/Partials/CreateTeamForm.vue',
        'resources/js/pages/teams/Partials/DeleteTeamForm.vue',
        'resources/js/pages/teams/Partials/TeamMemberManager.vue',
        'resources/js/pages/teams/Partials/UpdateTeamNameForm.vue',
      )->delete();

      // API page
      $c->files(
        'resources/js/pages/api/Index.vue',
        'resources/js/pages/api/Partials/ApiTokenManager.vue',
      )->delete();

      // Legal pages
      $c->files(
        'resources/js/pages/TermsOfService.vue',
        'resources/js/pages/PrivacyPolicy.vue',
      )->delete();

      // Markdown source
      $c->files(
        'resources/markdown/terms.md',
        'resources/markdown/policy.md',
      )->delete();

      // Dashboard page
      $c->file('resources/js/pages/Dashboard.vue')->delete();

      // Layouts (auth-only)
      $c->files(
        'resources/js/layouts/AuthLayout.vue',
        'resources/js/layouts/settings/Layout.vue',
      )->delete();

      // Auth/user components
      $c->files(
        'resources/js/components/DeleteUser.vue',
        'resources/js/components/NavUser.vue',
        'resources/js/components/TeamIndicatorBanner.vue',
        'resources/js/components/TeamSwitcher.vue',
        'resources/js/components/TwoFactorRecoveryCodes.vue',
        'resources/js/components/TwoFactorSetupModal.vue',
        'resources/js/components/UserAvatar.vue',
        'resources/js/components/UserInfo.vue',
        'resources/js/components/UserMenuContent.vue',
      )->delete();

      // 2FA UI primitives
      $c->files(
        'resources/js/components/ui/pin-input/PinInput.vue',
        'resources/js/components/ui/pin-input/PinInputGroup.vue',
        'resources/js/components/ui/pin-input/PinInputSeparator.vue',
        'resources/js/components/ui/pin-input/PinInputSlot.vue',
        'resources/js/components/ui/pin-input/index.ts',
      )->delete();

      // Composables
      $c->file('resources/js/composables/useTwoFactorAuth.ts')->delete();

      // Routes
      $c->files(
        'routes/settings.php',
        'routes/teams.php',
      )->delete();

      // Migrations
      $c->files(
        'database/migrations/0001_01_01_000000_create_users_table.php',
        'database/migrations/0001_01_01_000003_create_teams_table.php',
        'database/migrations/0001_01_01_000004_create_team_user_table.php',
        'database/migrations/0001_01_01_000005_create_team_invitations_table.php',
        'database/migrations/0001_01_01_000006_create_personal_access_tokens_table.php',
      )->delete();

      // Factories / seeders
      $c->file('database/factories/UserFactory.php')->delete();

      // Configs
      $c->files(
        'config/fortify.php',
        'config/teams.php',
      )->delete();

      // Tests
      $c->files(
        'tests/EmailVerificationTest.php',
        'tests/Feature/Auth/AuthenticationTest.php',
        'tests/Feature/Auth/EmailVerificationTest.php',
        'tests/Feature/Auth/PasswordConfirmationTest.php',
        'tests/Feature/Auth/PasswordResetTest.php',
        'tests/Feature/Auth/RegistrationTest.php',
        'tests/Feature/Auth/TwoFactorChallengeTest.php',
        'tests/Feature/Auth/VerificationNotificationTest.php',
        'tests/Feature/Settings/BrowserSessionsTest.php',
        'tests/Feature/Settings/DeleteAccountTest.php',
        'tests/Feature/Settings/PasswordUpdateTest.php',
        'tests/Feature/Settings/ProfileInformationTest.php',
        'tests/Feature/Settings/ProfileUpdateTest.php',
        'tests/Feature/Settings/TwoFactorAuthenticationTest.php',
        'tests/Feature/Actions/Fortify/ConfirmPasswordTest.php',
        'tests/Feature/Actions/Teams/AddTeamMemberTest.php',
        'tests/Feature/Actions/Teams/DeleteUserWithTeamsTest.php',
        'tests/Feature/Api/ApiTokenPermissionsTest.php',
        'tests/Feature/Api/CreateApiTokenTest.php',
        'tests/Feature/Api/DeleteApiTokenTest.php',
        'tests/Feature/Concerns/HasProfilePhotoTest.php',
        'tests/Feature/Concerns/HasTeamsTest.php',
        'tests/Feature/Controllers/ApiTokenControllerTest.php',
        'tests/Feature/Controllers/CurrentTeamControllerTest.php',
        'tests/Feature/Controllers/PrivacyPolicyControllerTest.php',
        'tests/Feature/Controllers/ProfileControllerTest.php',
        'tests/Feature/Controllers/ProfilePhotoControllerTest.php',
        'tests/Feature/Controllers/OtherBrowserSessionsControllerTest.php',
        'tests/Feature/Controllers/TeamControllerTest.php',
        'tests/Feature/Controllers/TeamInvitationControllerTest.php',
        'tests/Feature/Controllers/TeamMemberControllerTest.php',
        'tests/Feature/Controllers/TermsOfServiceControllerTest.php',
        'tests/Feature/Events/TeamEventsTest.php',
        'tests/Feature/Models/TeamTest.php',
        'tests/Feature/Policies/TeamPolicyTest.php',
        'tests/Feature/Providers/FortifyServiceProviderTest.php',
        'tests/Feature/Providers/TeamServiceProviderTest.php',
        'tests/Feature/Rules/RoleRulesTest.php',
        'tests/Feature/Teams/AgentTest.php',
        'tests/Feature/Teams/CreateTeamTest.php',
        'tests/Feature/Teams/DeleteTeamTest.php',
        'tests/Feature/Teams/InviteTeamMemberTest.php',
        'tests/Feature/Teams/LeaveTeamTest.php',
        'tests/Feature/Teams/RemoveTeamMemberTest.php',
        'tests/Feature/Teams/TeamsTest.php',
        'tests/Feature/Teams/UpdateTeamNameTest.php',
        'tests/Feature/UpdateTeamMemberRoleTest.php',
        'tests/Vue/components/DeleteUser.test.ts',
      )->delete();

      // composer.json — drop Fortify + Sanctum requires
      $c->file('composer.json')
        ->removeLinesContaining('"laravel/fortify":')
        ->removeLinesContaining('"laravel/sanctum":');

      // bootstrap/providers.php — strip Fortify + Team provider
      $c->file('bootstrap/providers.php')
        ->removeSection('registration')
        ->removeLinesContaining('TeamServiceProvider::class');

      // routes/web.php — strip dashboard + sub-route requires
      $c->file('routes/web.php')->removeSection('registration');

      // HandleInertiaRequests — strip teams + auth share blocks and orphaned imports
      $c->file('app/Http/Middleware/HandleInertiaRequests.php')->removeSection('registration');

      // Welcome landing page — strip auth nav + register/login CTAs
      $c->file('resources/js/pages/Welcome.vue')->removeSection('registration');
    },
  )

  // ===========================================================================
  // RESET PASSWORDS
  // ===========================================================================
  ->selected('features', 'reset-passwords',
    then: fn (Chisel $c) => null,
    else: function (Chisel $c): void {
      $c->files(
        'app/Actions/Fortify/ResetUserPassword.php',
        'resources/js/pages/auth/ForgotPassword.vue',
        'resources/js/pages/auth/ResetPassword.vue',
        'tests/Feature/Auth/PasswordResetTest.php',
      )->delete();

      $c->file('config/fortify.php')->removeLinesContaining('Features::resetPasswords(');

      $c->file('app/Providers/FortifyServiceProvider.php')
        ->removeSection('reset-passwords')
        ->removeLinesContaining('use App\\Actions\\Fortify\\ResetUserPassword;');

      $c->file('resources/js/pages/auth/Login.vue')->removeSection('reset-passwords');
    },
  )

  // ===========================================================================
  // UPDATE PASSWORDS
  // ===========================================================================
  ->selected('features', 'update-passwords',
    then: fn (Chisel $c) => null,
    else: function (Chisel $c): void {
      $c->files(
        'app/Actions/Fortify/UpdateUserPassword.php',
        'resources/js/pages/settings/Password.vue',
        'tests/Feature/Settings/PasswordUpdateTest.php',
      )->delete();

      $c->file('config/fortify.php')->removeLinesContaining('Features::updatePasswords(');

      $c->file('app/Providers/FortifyServiceProvider.php')
        ->removeSection('update-passwords')
        ->removeLinesContaining('use App\\Actions\\Fortify\\UpdateUserPassword;');

      $c->file('routes/settings.php')->removeSection('update-passwords');
      $c->file('app/Http/Middleware/HandleInertiaRequests.php')->removeSection('update-passwords');
      $c->file('resources/js/layouts/settings/Layout.vue')->removeSection('update-passwords');
    },
  )

  // ===========================================================================
  // TWO-FACTOR AUTHENTICATION
  // ===========================================================================
  ->selected('features', 'two-factor',
    then: fn (Chisel $c) => null,
    else: function (Chisel $c): void {
      $c->files(
        'app/Actions/Fortify/ConfirmPassword.php',
        'app/Http/Controllers/Settings/TwoFactorAuthenticationController.php',
        'app/Http/Requests/Settings/TwoFactorAuthenticationRequest.php',
        'resources/js/pages/auth/ConfirmPassword.vue',
        'resources/js/pages/auth/TwoFactorChallenge.vue',
        'resources/js/pages/settings/TwoFactor.vue',
        'resources/js/components/TwoFactorRecoveryCodes.vue',
        'resources/js/components/TwoFactorSetupModal.vue',
        'resources/js/components/ui/pin-input/PinInput.vue',
        'resources/js/components/ui/pin-input/PinInputGroup.vue',
        'resources/js/components/ui/pin-input/PinInputSeparator.vue',
        'resources/js/components/ui/pin-input/PinInputSlot.vue',
        'resources/js/components/ui/pin-input/index.ts',
        'resources/js/composables/useTwoFactorAuth.ts',
        'tests/Feature/Auth/PasswordConfirmationTest.php',
        'tests/Feature/Auth/TwoFactorChallengeTest.php',
        'tests/Feature/Settings/TwoFactorAuthenticationTest.php',
        'tests/Feature/Actions/Fortify/ConfirmPasswordTest.php',
      )->delete();

      $c->file('config/fortify.php')->removeLinesContaining('Features::twoFactorAuthentication(');

      $c->file('app/Providers/FortifyServiceProvider.php')->removeSection('two-factor');
      $c->file('routes/settings.php')->removeSection('two-factor');
      $c->file('app/Http/Middleware/HandleInertiaRequests.php')->removeSection('two-factor');
      $c->file('resources/js/layouts/settings/Layout.vue')->removeSection('two-factor');
      $c->file('database/migrations/0001_01_01_000000_create_users_table.php')->removeSection('two-factor');

      $c->file('app/Models/User.php')
        ->removeSection('two-factor')
        ->removeLinesContaining('use Laravel\\Fortify\\TwoFactorAuthenticatable;');
    },
  )

  // ===========================================================================
  // TEAMS
  // ===========================================================================
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
      $c->file('app/Models/User.php')
        ->removeLinesContaining('use App\Concerns\HasTeams;')
        ->removeLinesContaining('use HasTeams;');
      $c->files(
        'resources/js/layouts/app/AppHeaderLayout.vue',
        'resources/js/layouts/app/AppSidebarLayout.vue',
        'resources/js/components/AppHeader.vue',
        'resources/js/components/AppSidebar.vue',
      )->removeSection('teams');
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
        ->removeLinesContaining('Sanctum::usePersonalAccessTokenModel')
        ->removeLinesContaining('use App\Models\PersonalAccessToken;')
        ->removeLinesContaining('use Laravel\Sanctum\Sanctum;');
      $c->file('app/Models/User.php')
        ->removeLinesContaining('use Laravel\Sanctum\HasApiTokens;')
        ->removeLinesContaining('use HasApiTokens;');
      $c->file('resources/js/components/UserMenuContent.vue')->removeSection('api-tokens');
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
      $c->file('app/Models/User.php')
        ->removeLinesContaining('use App\Concerns\HasProfilePhoto;')
        ->removeLinesContaining('use HasProfilePhoto;')
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
        ->removeLinesContaining('Teams::deleteUsersUsing')
        ->removeLinesContaining('use App\Actions\Teams\DeleteUser;');
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
      $c->file('app/Models/User.php')
        ->removeLinesContaining('use Illuminate\Contracts\Auth\MustVerifyEmail;')
        ->replace(' implements MustVerifyEmail', '');
    },
  )
  ->apply(function (Chisel $c): void {
    $c->file('composer.json')
      ->removeLinesContaining('"@php artisan install:features --ansi"')
      ->removeLinesContaining('"test:chisel"')
      ->replace(' --exclude-group=chisel-integration', '')
      ->replace(",\n      \"@test:chisel\"", '');
    $c->files(
      'app/Console/Commands/InstallFeaturesCommand.php',
      'tests/Feature/Console/InstallFeaturesCommandTest.php',
      'tests/Feature/Chisel/StripFeatureTest.php',
      'chisel.php',
    )->delete();
  });
