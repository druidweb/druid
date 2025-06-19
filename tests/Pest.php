<?php

use Tests\DuskTestCase;
use Tests\TestCase;

/**
 * TEST CASE
 *
 * The closure you provide to your test functions is always bound to a specific PHPUnit test
 * case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
 * need to change it using the "pest()" function to bind a different classes or traits.
 */
pest()->extend(TestCase::class)->in('Feature');

/**
 * BROWSER TESTS
 *
 * Laravel Dusk provides comprehensive end-to-end testing and browser automation for our
 * Inertia.js + Vue.js application. These tests simulate real user interactions in a
 * headless Chrome browser, testing the complete user experience.
 *
 * Tests use the DuskTestCase which includes database migrations and Chrome driver
 * configuration optimized for CI/CD environments.
 */
pest()->extend(DuskTestCase::class)->in('Browser');

/**
 * EXPECTATIONS
 *
 * When you're writing tests, you often need to check that values meet certain conditions. The
 * "expect()" function gives you access to a set of "expectations" methods that you can use
 * to assert different things. Of course, you may extend the Expectation API at any time.
 */
expect()->extend('toBeOne', function () {
  return $this->toBe(1);
});

/**
 * FUNCTIONS
 *
 * While Pest is very powerful out-of-the-box, you may have some testing code specific to your
 * project that you don't want to repeat in every file. Here you can also expose helpers as
 * global functions to help you to reduce the number of lines of code in your test files.
 */
function something()
{
  // ..
}
