<?php

declare(strict_types=1);

use Symfony\Component\Process\Process;

beforeAll(function (): void {
  chiselBuildTemplate();
});

afterAll(function (): void {
  $template = chiselTemplatePath();

  if (is_dir($template)) {
    new Process(['rm', '-rf', $template])->setTimeout(60)->mustRun();
  }
});

beforeEach(function (): void {
  $this->sandbox = sys_get_temp_dir().'/chisel-strip-'.bin2hex(random_bytes(6));
  chiselCloneTemplate(chiselTemplatePath(), $this->sandbox);
});

afterEach(function (): void {
  if (property_exists($this, 'sandbox') && $this->sandbox !== null && is_dir($this->sandbox)) {
    new Process(['rm', '-rf', $this->sandbox])->setTimeout(60)->mustRun();
  }
});

test('stripping teams removes the team scaffolding and dangling references', function (): void {
  chiselRun($this->sandbox, ['api-tokens', 'profile-photos', 'terms', 'account-deletion', 'email-verification']);

  $deletedFiles = [
    'app/Actions/Teams/AddTeamMember.php',
    'app/Actions/Teams/CreateTeam.php',
    'app/Actions/Teams/DeleteTeam.php',
    'app/Actions/Teams/DeleteUserWithTeams.php',
    'app/Actions/Teams/InviteTeamMember.php',
    'app/Actions/Teams/RemoveTeamMember.php',
    'app/Actions/Teams/UpdateTeamMemberRole.php',
    'app/Actions/Teams/UpdateTeamName.php',
    'app/Actions/Teams/ValidateTeamDeletion.php',
    'app/Concerns/HasTeams.php',
    'app/Contracts/AddsTeamMembers.php',
    'app/Contracts/CreatesTeams.php',
    'app/Contracts/DeletesTeams.php',
    'app/Contracts/InvitesTeamMembers.php',
    'app/Contracts/RemovesTeamMembers.php',
    'app/Contracts/UpdatesTeamNames.php',
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
    'app/Http/Controllers/Teams/CurrentTeamController.php',
    'app/Http/Controllers/Teams/TeamController.php',
    'app/Http/Controllers/Teams/TeamInvitationController.php',
    'app/Http/Controllers/Teams/TeamMemberController.php',
    'app/Mail/TeamInvitation.php',
    'app/Models/Membership.php',
    'app/Models/Team.php',
    'app/Models/TeamInvitation.php',
    'app/Policies/TeamPolicy.php',
    'app/Providers/TeamServiceProvider.php',
    'app/Rules/OwnerRole.php',
    'app/Rules/Role.php',
    'app/Teams/Agent.php',
    'app/Teams/Role.php',
    'database/migrations/0001_01_01_000003_create_teams_table.php',
    'database/migrations/0001_01_01_000004_create_team_user_table.php',
    'database/migrations/0001_01_01_000005_create_team_invitations_table.php',
    'resources/js/components/TeamIndicatorBanner.vue',
    'resources/js/components/TeamSwitcher.vue',
    'resources/js/pages/teams/Create.vue',
    'resources/js/pages/teams/Show.vue',
    'resources/js/pages/teams/Partials/CreateTeamForm.vue',
    'resources/js/pages/teams/Partials/DeleteTeamForm.vue',
    'resources/js/pages/teams/Partials/TeamMemberManager.vue',
    'resources/js/pages/teams/Partials/UpdateTeamNameForm.vue',
  ];

  expectDeleted($this->sandbox, $deletedFiles);

  // Teams.php and Features.php survive — they are the runtime feature-flag
  // dispatcher and config builder that the rest of the kit still calls.
  expectKept($this->sandbox, [
    'app/Teams/Teams.php',
    'app/Teams/Features.php',
  ]);

  expectContentRemoved($this->sandbox, 'config/teams.php', ['Features::teams(']);
  expectContentRemoved($this->sandbox, 'bootstrap/providers.php', ['TeamServiceProvider::class']);
  expectContentRemoved($this->sandbox, 'app/Models/User.php', [
    'use App\Concerns\HasTeams',
    'use HasTeams',
  ]);

  // Catch any future regression where a deleted component is still imported.
  expectNoLingeringVueReferences($this->sandbox, $deletedFiles);
})->group('chisel-integration');

test('stripping api-tokens removes sanctum hookup and personal access token plumbing', function (): void {
  chiselRun($this->sandbox, ['teams', 'profile-photos', 'terms', 'account-deletion', 'email-verification']);

  $deletedFiles = [
    'app/Http/Controllers/Api/ApiTokenController.php',
    'app/Models/PersonalAccessToken.php',
    'database/migrations/0001_01_01_000006_create_personal_access_tokens_table.php',
    'resources/js/pages/api/Index.vue',
    'resources/js/pages/api/Partials/ApiTokenManager.vue',
  ];

  expectDeleted($this->sandbox, $deletedFiles);
  expectNoLingeringVueReferences($this->sandbox, $deletedFiles);

  expectContentRemoved($this->sandbox, 'config/teams.php', ['Features::api(']);
  expectContentRemoved($this->sandbox, 'app/Models/User.php', [
    'use Laravel\Sanctum\HasApiTokens',
    'use HasApiTokens',
  ]);
  expectContentRemoved($this->sandbox, 'app/Providers/AppServiceProvider.php', [
    'use App\Models\PersonalAccessToken',
    'use Laravel\Sanctum\Sanctum',
    'Sanctum::usePersonalAccessTokenModel',
  ]);
})->group('chisel-integration');

test('stripping profile-photos removes the photo controller, concern, and UI hooks', function (): void {
  chiselRun($this->sandbox, ['teams', 'api-tokens', 'terms', 'account-deletion', 'email-verification']);

  $deletedFiles = [
    'app/Http/Controllers/Settings/ProfilePhotoController.php',
    'app/Concerns/HasProfilePhoto.php',
  ];

  expectDeleted($this->sandbox, $deletedFiles);
  expectNoLingeringVueReferences($this->sandbox, $deletedFiles);

  expectContentRemoved($this->sandbox, 'config/teams.php', ['Features::profilePhotos(']);
  expectContentRemoved($this->sandbox, 'app/Models/User.php', [
    'use App\Concerns\HasProfilePhoto',
    'use HasProfilePhoto',
    "'profile_photo_url'",
  ]);
  expectContentRemoved($this->sandbox, 'resources/js/pages/settings/Profile.vue', [
    '@/routes/current-user-photo',
    '@success="clearPhotoFileInput"',
    'page.props.teams?.managesProfilePhotos',
  ]);
})->group('chisel-integration');

test('stripping terms removes both legal pages and the registration consent UI', function (): void {
  chiselRun($this->sandbox, ['teams', 'api-tokens', 'profile-photos', 'account-deletion', 'email-verification']);

  $deletedFiles = [
    'app/Http/Controllers/TermsOfServiceController.php',
    'app/Http/Controllers/PrivacyPolicyController.php',
    'resources/js/pages/TermsOfService.vue',
    'resources/js/pages/PrivacyPolicy.vue',
    'resources/markdown/terms.md',
    'resources/markdown/policy.md',
  ];

  expectDeleted($this->sandbox, $deletedFiles);
  expectNoLingeringVueReferences($this->sandbox, $deletedFiles);

  expectContentRemoved($this->sandbox, 'config/teams.php', ['Features::termsAndPrivacyPolicy(']);
  expectContentRemoved($this->sandbox, 'resources/js/pages/auth/Register.vue', [
    'hasTermsAndPrivacyPolicyFeature',
    '@/routes/terms',
    '@/routes/policy',
  ]);
})->group('chisel-integration');

test('stripping account-deletion removes the deletion path everywhere it surfaces', function (): void {
  chiselRun($this->sandbox, ['teams', 'api-tokens', 'profile-photos', 'terms', 'email-verification']);

  $deletedFiles = [
    'app/Actions/Teams/DeleteUser.php',
    'app/Contracts/DeletesUsers.php',
    'resources/js/components/DeleteUser.vue',
  ];

  expectDeleted($this->sandbox, $deletedFiles);
  expectNoLingeringVueReferences($this->sandbox, $deletedFiles);

  expectContentRemoved($this->sandbox, 'config/teams.php', ['Features::accountDeletion(']);
  expectContentRemoved($this->sandbox, 'app/Providers/TeamServiceProvider.php', [
    'use App\Actions\Teams\DeleteUser',
    'Teams::deleteUsersUsing',
  ]);
  expectContentRemoved($this->sandbox, 'resources/js/pages/settings/Profile.vue', ['<DeleteUser']);
  expectContentRemoved($this->sandbox, 'routes/settings.php', ['profile.destroy']);
  expectContentRemoved($this->sandbox, 'tests/Feature/Settings/ProfileUpdateTest.php', [
    'user can delete their account',
    'correct password must be provided to delete account',
  ]);
})->group('chisel-integration');

test('stripping email-verification removes the verify view, Fortify hookup, and User interface', function (): void {
  chiselRun($this->sandbox, ['teams', 'api-tokens', 'profile-photos', 'terms', 'account-deletion']);

  $deletedFiles = [
    'resources/js/pages/auth/VerifyEmail.vue',
  ];

  expectDeleted($this->sandbox, $deletedFiles);
  expectNoLingeringVueReferences($this->sandbox, $deletedFiles);

  expectContentRemoved($this->sandbox, 'app/Providers/FortifyServiceProvider.php', [
    'Fortify::verifyEmailView',
  ]);
  expectContentRemoved($this->sandbox, 'app/Models/User.php', [
    'use Illuminate\Contracts\Auth\MustVerifyEmail',
    'implements MustVerifyEmail',
  ]);
  expectContentRemoved($this->sandbox, 'resources/js/pages/settings/Profile.vue', [
    'mustVerifyEmail',
    '@/routes/verification',
  ]);
})->group('chisel-integration');

function chiselTemplatePath(): string
{
  return sys_get_temp_dir().'/chisel-template-'.getmypid();
}

function chiselBuildTemplate(): void
{
  $template = chiselTemplatePath();

  if (is_dir($template)) {
    new Process(['rm', '-rf', $template])->setTimeout(60)->mustRun();
  }

  mkdir($template, 0o755, true);

  // We do NOT copy vendor or node_modules. The chisel subprocess loads
  // the original project's autoloader via LARAVEL_INSTALLER_AUTOLOADER,
  // so the sandbox only needs the source files chisel touches.
  new Process([
    'rsync',
    '-a',
    '--exclude=node_modules',
    '--exclude=vendor',
    '--exclude=.git',
    '--exclude=storage/logs/*',
    '--exclude=storage/framework/*',
    '--exclude=bootstrap/cache/*.php',
    '--exclude=coverage.xml',
    '--exclude=.phpunit.cache',
    '--exclude=public/hot',
    '--exclude=.env',
    rtrim((string) getcwd(), '/').'/',
    rtrim($template, '/').'/',
  ])->setTimeout(120)->mustRun();
}

function chiselCloneTemplate(string $template, string $destination): void
{
  // macOS APFS supports `cp -Rc` for O(1) copy-on-write clones.
  $flags = PHP_OS_FAMILY === 'Darwin' ? '-Rc' : '-R';

  new Process(['cp', $flags, $template.'/.', $destination])
    ->setTimeout(60)
    ->mustRun();
}

function chiselRun(string $sandbox, array $kept): void
{
  $answers = json_encode(['features' => $kept], JSON_THROW_ON_ERROR);

  $script = <<<'PHP'
$sandbox = $argv[1];
$answers = json_decode($argv[2], true, 512, JSON_THROW_ON_ERROR);
$s = require $sandbox.'/chisel.php';
$s->chisel($answers);
PHP;

  new Process(
    command: [PHP_BINARY, '-r', $script, $sandbox, $answers],
    env: ['LARAVEL_INSTALLER_AUTOLOADER' => base_path('vendor/autoload.php')],
  )->setTimeout(60)->mustRun();
}

function expectDeleted(string $sandbox, array $paths): void
{
  foreach ($paths as $path) {
    expect(file_exists($sandbox.'/'.$path))
      ->toBeFalse("Expected {$path} to be deleted but it still exists");
  }
}

function expectKept(string $sandbox, array $paths): void
{
  foreach ($paths as $path) {
    expect(file_exists($sandbox.'/'.$path))
      ->toBeTrue("Expected {$path} to remain but it was deleted");
  }
}

function expectContentRemoved(string $sandbox, string $path, array $needles): void
{
  $contents = (string) file_get_contents($sandbox.'/'.$path);

  foreach ($needles as $needle) {
    // We can't use ->not->toContain($needle, $message) — Pest treats extra
    // args as additional needles, not a failure message, which silently
    // passes when the message string isn't in the haystack.
    expect(str_contains($contents, (string) $needle))
      ->toBeFalse("Expected {$path} not to contain '{$needle}' but it does");
  }
}

// Sweep check: for each deleted Vue component, ensure no surviving Vue/TS
// source still imports it. We match on the @-aliased import path
// (e.g. '@/components/TeamSwitcher') rather than the bare name, because
// bare names collide with common words (Create, Show, etc). Wayfinder
// regenerated dirs are excluded.
function expectNoLingeringVueReferences(string $sandbox, array $deletedComponents): void
{
  foreach ($deletedComponents as $deleted) {
    if (! str_ends_with($deleted, '.vue')) {
      continue;
    }

    // 'resources/js/components/TeamSwitcher.vue' → '@/components/TeamSwitcher'
    $importPath = '@/'.substr($deleted, strlen('resources/js/'), -strlen('.vue'));

    $grep = new Process(
      command: [
        'grep', '-rl', '--include=*.vue', '--include=*.ts',
        '--exclude-dir=actions', '--exclude-dir=routes',
        $importPath, 'resources/js',
      ],
      cwd: $sandbox,
    );
    $grep->run();

    $matches = array_values(array_filter(explode("\n", trim($grep->getOutput()))));

    expect($matches)->toBeEmpty(
      "Deleted component import '{$importPath}' is still referenced in: ".implode(', ', $matches),
    );
  }
}
