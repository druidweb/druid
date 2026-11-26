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

it('accepts a banner style', function (string $style): void {
  $result = redirect('/')->banner('Styled message', $style);

  expect($result->getSession()->get('flash'))->toBe([
    'bannerStyle' => $style,
    'banner' => 'Styled message',
  ]);
})->with(['warning', 'danger']);
