<?php

declare(strict_types=1);

use Illuminate\Http\RedirectResponse;

it('adds banner macro to redirect response', function (): void {
  $response = redirect('/');

  expect($response)->toBeInstanceOf(RedirectResponse::class);
  expect(RedirectResponse::hasMacro('banner'))->toBeTrue();

  $result = $response->banner('Test message');

  expect($result)->toBeInstanceOf(RedirectResponse::class);
  expect($result->getSession()->get('flash'))->toBe([
    'bannerStyle' => 'success',
    'banner' => 'Test message',
  ]);
});

it('adds warningBanner macro to redirect response', function (): void {
  $response = redirect('/');

  expect(RedirectResponse::hasMacro('warningBanner'))->toBeTrue();

  $result = $response->warningBanner('Warning message');

  expect($result)->toBeInstanceOf(RedirectResponse::class);
  expect($result->getSession()->get('flash'))->toBe([
    'bannerStyle' => 'warning',
    'banner' => 'Warning message',
  ]);
});

it('adds dangerBanner macro to redirect response', function (): void {
  $response = redirect('/');

  expect(RedirectResponse::hasMacro('dangerBanner'))->toBeTrue();

  $result = $response->dangerBanner('Danger message');

  expect($result)->toBeInstanceOf(RedirectResponse::class);
  expect($result->getSession()->get('flash'))->toBe([
    'bannerStyle' => 'danger',
    'banner' => 'Danger message',
  ]);
});
