<?php

declare(strict_types=1);

use App\Teams\Features;

it('displays terms of service page', function (): void {
  $response = $this->get('/terms-of-service');

  if (Features::hasTermsAndPrivacyPolicyFeature()) {
    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->component('TermsOfService'));
  } else {
    $response->assertNotFound();
  }
});
