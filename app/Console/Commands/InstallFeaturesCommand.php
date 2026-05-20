<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Laravel\Chisel\Chisel;
use Laravel\Chisel\PendingAnswers;
use Laravel\Chisel\Question;
use Laravel\Chisel\Script;

use function Laravel\Prompts\multiselect;

class InstallFeaturesCommand extends Command
{
  protected $signature = 'install:features
        {--answers= : JSON string of answers to skip interactive prompts}';

  protected $description = 'Select and install optional Druid features, stripping what you do not need.';

  public function handle(): void
  {
    $script = $this->loadScript();
    $script->chisel($this->resolveAnswers($script));
    $this->buildFrontend();
  }

  protected function loadScript(): Script
  {
    /** @var Script $script */
    $script = require base_path('chisel.php');

    return $script;
  }

  protected function resolveAnswers(Script $script): PendingAnswers
  {
    /** @var array<string, mixed> $providedAnswers */
    $providedAnswers = $this->option('answers') === null
        ? []
        : json_decode((string) $this->option('answers'), true, 512, JSON_THROW_ON_ERROR);

    return $script
      ->collectAnswers()
      ->onQuestion(fn (Question $question): array => multiselect(
        label: $question->label,
        options: $question->options,
        default: $question->default ?? [],
        required: $question->required,
        hint: $question->hint,
      ))
      ->interactive($this->input->isInteractive())
      ->withAnswers($providedAnswers);
  }

  protected function buildFrontend(): void
  {
    // @codeCoverageIgnoreStart
    $chisel = Chisel::in(base_path());

    $chisel->npm()->install();
    $chisel->npm()->run('build');
    // @codeCoverageIgnoreEnd
  }
}
