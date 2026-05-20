<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Laravel\Chisel\Chisel;
use Laravel\Chisel\Question;

use function Laravel\Prompts\multiselect;

final class InstallFeaturesCommand extends Command
{
  protected $signature = 'install:features
        {--answers= : JSON string of answers to skip interactive prompts}';

  protected $description = 'Select and install optional Druid features, stripping what you do not need.';

  public function handle(): void
  {
    /** @var \Laravel\Chisel\Script $script */
    $script = require base_path('chisel.php');

    /** @var array<string, mixed> $providedAnswers */
    $providedAnswers = $this->option('answers') === null
        ? []
        : json_decode((string) $this->option('answers'), true, 512, JSON_THROW_ON_ERROR);

    $answers = $script
      ->collectAnswers()
      ->onQuestion(fn (Question $question) => multiselect(
        label: $question->label,
        options: $question->options,
        default: $question->default ?? [],
        required: $question->required,
        hint: $question->hint,
      ))
      ->interactive($this->input->isInteractive())
      ->withAnswers($providedAnswers);

    $script->chisel($answers);

    $chisel = Chisel::in(base_path());

    $chisel->npm()->install();
    $chisel->npm()->run('build');
  }
}
