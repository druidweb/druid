<?php

declare(strict_types=1);

it('renders the terms of service page', function (): void {
  visit('/terms-of-service')
    ->assertNoJavaScriptErrors()
    ->assertSee('Terms of Service')
    ->assertSee('Edit this file to define the terms of service');
});

it('renders the privacy policy page', function (): void {
  visit('/privacy-policy')
    ->assertNoJavaScriptErrors()
    ->assertSee('Privacy Policy')
    ->assertSee('Edit this file to define the privacy policy');
});
