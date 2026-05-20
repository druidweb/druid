<?php

declare(strict_types=1);

use Symfony\Component\Process\Process;

beforeEach(function (): void {
  $this->sandbox = sys_get_temp_dir().'/chisel-strip-'.bin2hex(random_bytes(6));
  chiselSandboxCreate(base_path(), $this->sandbox);
});

afterEach(function (): void {
  if (isset($this->sandbox) && is_dir($this->sandbox)) {
    (new Process(['rm', '-rf', $this->sandbox]))->setTimeout(60)->mustRun();
  }
});

dataset('chisel_feature_combinations', [
  'teams stripped' => [['api-tokens', 'profile-photos', 'terms', 'account-deletion', 'email-verification']],
  'api-tokens stripped' => [['teams', 'profile-photos', 'terms', 'account-deletion', 'email-verification']],
  'profile-photos stripped' => [['teams', 'api-tokens', 'terms', 'account-deletion', 'email-verification']],
  'terms stripped' => [['teams', 'api-tokens', 'profile-photos', 'account-deletion', 'email-verification']],
  'account-deletion stripped' => [['teams', 'api-tokens', 'profile-photos', 'terms', 'email-verification']],
  'email-verification stripped' => [['teams', 'api-tokens', 'profile-photos', 'terms', 'account-deletion']],
  'everything stripped' => [[]],
]);

test('chiseled project boots cleanly', function (array $kept): void {
  chiselSandboxRunChisel($this->sandbox, $kept);

  $artisan = chiselSandboxArtisan($this->sandbox, ['about', '--only=environment']);

  expect($artisan->getExitCode())->toBe(
    0,
    'artisan boot failed after stripping all but ['.implode(',', $kept)."]:\n"
    .'STDOUT: '.$artisan->getOutput()."\n"
    .'STDERR: '.$artisan->getErrorOutput(),
  );
})->with('chisel_feature_combinations')->group('chisel-integration');

function chiselSandboxCreate(string $source, string $destination): void
{
  mkdir($destination, 0o755, true);

  $rsync = new Process([
    'rsync',
    '-a',
    '--exclude=node_modules',
    '--exclude=.git',
    '--exclude=storage/logs/*',
    '--exclude=storage/framework/cache/*',
    '--exclude=storage/framework/sessions/*',
    '--exclude=storage/framework/testing/*',
    '--exclude=storage/framework/views/*',
    '--exclude=bootstrap/cache/*.php',
    '--exclude=coverage.xml',
    '--exclude=.phpunit.cache',
    '--exclude=public/hot',
    '--exclude=.env',
    rtrim($source, '/').'/',
    rtrim($destination, '/').'/',
  ]);

  $rsync->setTimeout(120);
  $rsync->mustRun();

  copy($destination.'/.env.example', $destination.'/.env');

  $key = 'base64:'.base64_encode(random_bytes(32));
  $env = (string) file_get_contents($destination.'/.env');
  $env = preg_replace('/^APP_KEY=.*$/m', "APP_KEY={$key}", $env);
  file_put_contents($destination.'/.env', $env);
}

function chiselSandboxRunChisel(string $sandbox, array $kept): void
{
  $answers = json_encode(['features' => $kept], JSON_THROW_ON_ERROR);

  $script = <<<'PHP'
$sandbox = $argv[1];
$answers = json_decode($argv[2], true, 512, JSON_THROW_ON_ERROR);
$s = require $sandbox.'/chisel.php';
$s->chisel($answers);
PHP;

  $process = new Process([PHP_BINARY, '-r', $script, $sandbox, $answers]);
  $process->setTimeout(60);
  $process->mustRun();
}

function chiselSandboxArtisan(string $sandbox, array $args): Process
{
  $process = new Process(
    command: [PHP_BINARY, 'artisan', ...$args],
    cwd: $sandbox,
  );

  $process->setTimeout(60);
  $process->run();

  return $process;
}
