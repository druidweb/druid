<?php

declare(strict_types=1);

namespace App\Teams;

use Detection\MobileDetect;

/**
 * @copyright Originally created by Jens Segers: https://github.com/jenssegers/agent
 */
class Agent extends MobileDetect
{
  /**
   * List of additional operating systems.
   *
   * @var array<string, string>
   */
  protected static array $additionalOperatingSystems = [
    'Windows' => 'Windows',
    'Windows NT' => 'Windows NT',
    'OS X' => 'Mac OS X',
    'Debian' => 'Debian',
    'Ubuntu' => 'Ubuntu',
    'Macintosh' => 'PPC',
    'OpenBSD' => 'OpenBSD',
    'Linux' => 'Linux',
    'ChromeOS' => 'CrOS',
  ];

  /**
   * List of additional browsers.
   *
   * @var array<string, string>
   */
  protected static array $additionalBrowsers = [
    'Opera Mini' => 'Opera Mini',
    'Opera' => 'Opera|OPR',
    'Edge' => 'Edge|Edg',
    'Coc Coc' => 'coc_coc_browser',
    'UCBrowser' => 'UCBrowser',
    'Vivaldi' => 'Vivaldi',
    'Chrome' => 'Chrome',
    'Firefox' => 'Firefox',
    'Safari' => 'Safari',
    'IE' => 'MSIE|IEMobile|MSIEMobile|Trident/[.0-9]+',
    'Netscape' => 'Netscape',
    'Mozilla' => 'Mozilla',
    'WeChat' => 'MicroMessenger',
  ];

  public function platform(): ?string
  {
    /** @var array<string, string> $os */
    $os = MobileDetect::getOperatingSystems();

    return $this->firstMatchingRule($this->mergeRules($os, static::$additionalOperatingSystems));
  }

  public function browser(): ?string
  {
    /** @var array<string, string> $browsers */
    $browsers = MobileDetect::getBrowsers();

    return $this->firstMatchingRule($this->mergeRules(static::$additionalBrowsers, $browsers));
  }

  // MobileDetect's isMobile()/isTablet() already honour CloudFront's viewer headers.
  public function isDesktop(): bool
  {
    return ! $this->isMobile() && ! $this->isTablet();
  }

  // Name of the first rule whose pattern matches the user agent; rule order is priority order.
  /** @param  array<string, string>  $rules */
  protected function firstMatchingRule(array $rules): ?string
  {
    foreach ($rules as $name => $pattern) {
      if ($this->match($pattern, (string) $this->getUserAgent())) {
        return $name;
      }
    }

    return null;
  }

  /**
   * Merge multiple rules into one array.
   *
   * @param  array<string, string>  ...$all
   * @return array<string, string>
   */
  protected function mergeRules(array ...$all): array
  {
    /** @var array<string, string> $merged */
    $merged = [];

    foreach ($all as $rules) {
      foreach ($rules as $key => $value) {
        if (empty($merged[$key])) {
          $merged[$key] = $value;
        } else {
          $merged[$key] .= '|'.$value;
        }
      }
    }

    return $merged;
  }
}
