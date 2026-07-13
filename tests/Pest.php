<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * TEST CASE
 *
 * The closure you provide to your test functions is always bound to a specific PHPUnit test
 * case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
 * need to change it using the "pest()" function to bind a different classes or traits.
 */
pest()->extend(TestCase::class)
  ->use(RefreshDatabase::class)
  ->in('Feature');

/**
 * BROWSER TESTS
 *
 * End-to-end browser tests live in tests/Browser and drive a real browser via Playwright.
 * They are intentionally NOT registered as a PHPUnit testsuite, so the default suites
 * (Unit/Feature) and CI never run them. Execute them on demand with "composer test:e2e".
 */
pest()->extend(TestCase::class)
  ->use(RefreshDatabase::class)
  ->in('Browser');

// A browser assertion failure drops a screenshot into tests/Browser/Screenshots. We never keep them.
// The plugin already clears that directory at the start of a run; this clears it at the end too, so
// nothing lingers in the working tree after a failed run. Runs at process shutdown, so it never
// affects a test's exit code.
register_shutdown_function(static function (): void {
  $dir = __DIR__.'/Browser/Screenshots';

  if (! is_dir($dir)) {
    return;
  }

  foreach (glob($dir.'/*') ?: [] as $file) {
    @unlink($file);
  }

  @rmdir($dir);
});

/**
 * EXPECTATIONS
 *
 * When you're writing tests, you often need to check that values meet certain conditions. The
 * "expect()" function gives you access to a set of "expectations" methods that you can use
 * to assert different things. Of course, you may extend the Expectation API at any time.
 */
expect()->extend('toBeOne', fn () => $this->toBe(1));

/**
 * Functions
 *
 * While Pest is very powerful out-of-the-box, you may have some testing code specific to your
 * project that you don't want to repeat in every file. Here you can also expose helpers as
 * global functions to help you to reduce the number of lines of code in your test files.
 */
function something(): void
{
  // ..
}
