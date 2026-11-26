<?php

declare(strict_types=1);

use App\Teams\Agent;

it('detects browser from user agent', function (): void {
  $agent = new Agent;
  $agent->setUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');

  expect($agent->browser())->toBe('Chrome');
});

it('detects desktop safari and firefox rather than the generic mozilla rule', function (): void {
  $agent = new Agent;
  $agent->setUserAgent('Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.1.1 Safari/605.1.15');
  expect($agent->browser())->toBe('Safari');

  $agent = new Agent;
  $agent->setUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0');
  expect($agent->browser())->toBe('Firefox');
});

it('detects windows platform', function (): void {
  $agent = new Agent;
  $agent->setUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');

  expect($agent->platform())->toBe('Windows');
});

it('detects macos platform', function (): void {
  $agent = new Agent;
  $agent->setUserAgent('Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.1.1 Safari/605.1.15');

  expect($agent->platform())->toBe('OS X');
});

it('detects linux platform', function (): void {
  $agent = new Agent;
  $agent->setUserAgent('Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');

  expect($agent->platform())->toBe('Linux');
});

it('detects desktop device', function (): void {
  $agent = new Agent;
  $agent->setUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');

  expect($agent->isDesktop())->toBeTrue();
  expect($agent->isMobile())->toBeFalse();
});

it('detects mobile device', function (): void {
  $agent = new Agent;
  $agent->setUserAgent('Mozilla/5.0 (iPhone; CPU iPhone OS 14_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.1.1 Mobile/15E148 Safari/604.1');

  expect($agent->isMobile())->toBeTrue();
  expect($agent->isDesktop())->toBeFalse();
});

it('treats a cloudfront mobile viewer as not desktop', function (): void {
  $agent = new Agent;
  $agent->setHttpHeaders(['HTTP_USER_AGENT' => 'Amazon CloudFront', 'HTTP_CLOUDFRONT_IS_MOBILE_VIEWER' => 'true']);

  expect($agent->isDesktop())->toBeFalse();
});

it('returns null for an unknown browser', function (): void {
  $agent = new Agent;
  $agent->setUserAgent('curl/8.0');

  expect($agent->browser())->toBeNull();
});

it('returns null for unknown platform', function (): void {
  $agent = new Agent;
  $agent->setUserAgent('Unknown/1.0');

  expect($agent->platform())->toBeNull();
});

it('detects various browsers', function (): void {
  $agent = new Agent;

  // Test that browser detection returns a string for various user agents
  $agent->setUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:89.0) Gecko/20100101 Firefox/89.0');
  expect($agent->browser())->toBeString();

  $agent2 = new Agent;
  $agent2->setUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36 Edg/91.0.864.59');
  expect($agent2->browser())->toBeString();

  $agent3 = new Agent;
  $agent3->setUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36 OPR/77.0.4054.277');
  expect($agent3->browser())->toBeString();

  $agent4 = new Agent;
  $agent4->setUserAgent('Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.1.1 Safari/605.1.15');
  expect($agent4->browser())->toBeString();
});

it('detects ubuntu platform', function (): void {
  $agent = new Agent;
  $agent->setUserAgent('Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:89.0) Gecko/20100101 Firefox/89.0');

  expect($agent->platform())->toBe('Ubuntu');
});

it('detects chromeos platform', function (): void {
  $agent = new Agent;
  $agent->setUserAgent('Mozilla/5.0 (X11; CrOS x86_64 14092.77.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/93.0.4577.107 Safari/537.36');

  expect($agent->platform())->toBe('ChromeOS');
});

it('merges rules correctly', function (): void {
  $agent = new class extends Agent
  {
    public function testMergeRules(...$all): array
    {
      return $this->mergeRules(...$all);
    }
  };

  $rules1 = ['Browser' => 'Chrome'];
  $rules2 = ['Browser' => 'Firefox', 'Platform' => 'Windows'];

  $merged = $agent->testMergeRules($rules1, $rules2);

  expect($merged)->toBe([
    'Browser' => 'Chrome|Firefox',
    'Platform' => 'Windows',
  ]);
});

it('merges empty rules', function (): void {
  $agent = new class extends Agent
  {
    public function testMergeRules(...$all): array
    {
      return $this->mergeRules(...$all);
    }
  };

  $merged = $agent->testMergeRules([], []);

  expect($merged)->toBe([]);
});
