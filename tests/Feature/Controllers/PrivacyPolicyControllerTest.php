<?php

declare(strict_types=1);

use App\Teams\Features;

it('displays privacy policy page', function (): void {
  $response = $this->get('/privacy-policy');

  if (Features::hasTermsAndPrivacyPolicyFeature()) {
    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->component('PrivacyPolicy'));
  } else {
    $response->assertNotFound();
  }
});
