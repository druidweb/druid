<?php

declare(strict_types=1);

use App\Concerns\RedirectsActions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;

class TestRedirectsActions
{
  use RedirectsActions;
}

class ActionWithRedirectToMethod
{
  public function redirectTo(): string
  {
    return '/custom-path';
  }
}

class ActionWithRedirectToProperty
{
  public ?string $redirectTo = '/property-path';
}

class ActionWithoutRedirect
{
  // No redirectTo method or property
}

it('uses redirectTo method when available', function (): void {
  $trait = new TestRedirectsActions;
  $action = new ActionWithRedirectToMethod;

  $result = $trait->redirectPath($action);

  expect($result)->toBeInstanceOf(RedirectResponse::class);
  expect($result->getTargetUrl())->toContain('/custom-path');
});

it('uses redirectTo property when method not available', function (): void {
  $trait = new TestRedirectsActions;
  $action = new ActionWithRedirectToProperty;

  $result = $trait->redirectPath($action);

  expect($result)->toBeInstanceOf(RedirectResponse::class);
  expect($result->getTargetUrl())->toContain('/property-path');
});

it('uses fortify home config when no redirectTo', function (): void {
  $trait = new TestRedirectsActions;
  $action = new ActionWithoutRedirect;

  $result = $trait->redirectPath($action);

  expect($result)->toBeInstanceOf(RedirectResponse::class);
});

it('returns response directly when redirectTo returns Response', function (): void {
  $trait = new TestRedirectsActions;
  $action = new class
  {
    public function redirectTo(): Response
    {
      return new Response('Custom response');
    }
  };

  $result = $trait->redirectPath($action);

  expect($result)->toBeInstanceOf(Response::class);
});
