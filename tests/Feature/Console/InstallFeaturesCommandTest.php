<?php

declare(strict_types=1);

use App\Console\Commands\InstallFeaturesCommand;
use Illuminate\Contracts\Console\Kernel;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

beforeEach(function (): void {
  $this->originalBasePath = base_path();
  $this->tmp = sys_get_temp_dir().'/chisel-test-'.bin2hex(random_bytes(6));
  mkdir($this->tmp);
});

afterEach(function (): void {
  $this->app->setBasePath($this->originalBasePath);

  if (is_dir($this->tmp)) {
    $items = new RecursiveIteratorIterator(
      new RecursiveDirectoryIterator($this->tmp, FilesystemIterator::SKIP_DOTS),
      RecursiveIteratorIterator::CHILD_FIRST,
    );

    foreach ($items as $item) {
      $item->isDir() ? rmdir($item->getPathname()) : unlink($item->getPathname());
    }

    rmdir($this->tmp);
  }
});

function installFeaturesStubScript(string $tmp, string $hookPath): void
{
  $hook = var_export($hookPath, true);

  file_put_contents($tmp.'/chisel.php', <<<PHP
<?php

use Laravel\\Chisel\\Chisel;
use Laravel\\Chisel\\Question;

return Chisel::script(__DIR__)
  ->questions([
    Question::multiselect(
      name: 'features',
      label: 'Pick',
      options: ['a' => 'A', 'b' => 'B'],
      default: ['a'],
    ),
  ])
  ->apply(function (\$chisel, \$answers) {
    file_put_contents({$hook}, json_encode(\$answers));
  });
PHP);
}

function installFeaturesRun(InstallFeaturesCommand $command, array $options = [], bool $interactive = false): int
{
  $command->setLaravel(app());

  $input = new ArrayInput($options);
  $input->setInteractive($interactive);

  return $command->run($input, new BufferedOutput);
}

test('runs the chisel script with provided answers and builds the frontend', function (): void {
  $hook = $this->tmp.'/answers.json';
  installFeaturesStubScript($this->tmp, $hook);
  $this->app->setBasePath($this->tmp);

  $command = new class extends InstallFeaturesCommand
  {
    public bool $built = false;

    protected function buildFrontend(): void
    {
      $this->built = true;
    }
  };

  $exit = installFeaturesRun($command, ['--answers' => '{"features":["b"]}']);

  expect($exit)->toBe(0);
  expect($command->built)->toBeTrue();
  expect(json_decode((string) file_get_contents($hook), true))->toBe(['features' => ['b']]);
});

test('falls back to question defaults when answers are omitted non-interactively', function (): void {
  $hook = $this->tmp.'/answers.json';
  installFeaturesStubScript($this->tmp, $hook);
  $this->app->setBasePath($this->tmp);

  $command = new class extends InstallFeaturesCommand
  {
    protected function buildFrontend(): void
    {
      //
    }
  };

  $exit = installFeaturesRun($command);

  expect($exit)->toBe(0);
  expect(json_decode((string) file_get_contents($hook), true))->toBe(['features' => ['a']]);
});

test('invokes the multiselect prompt when running interactively', function (): void {
  $hook = $this->tmp.'/answers.json';
  installFeaturesStubScript($this->tmp, $hook);
  $this->app->setBasePath($this->tmp);

  $command = new class extends InstallFeaturesCommand
  {
    protected function buildFrontend(): void
    {
      //
    }
  };

  $this->app->make(Kernel::class)->registerCommand($command);

  $this->artisan('install:features')
    ->expectsChoice('Pick', ['a'], ['a' => 'A', 'b' => 'B'])
    ->assertExitCode(0);

  expect(json_decode((string) file_get_contents($hook), true))->toBe(['features' => ['a']]);
});
