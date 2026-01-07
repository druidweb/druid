<?php

namespace App\Teams;

use Closure;
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

  /**
   * Key value store for resolved strings.
   *
   * @var array<string, mixed>
   */
  protected array $store = [];

  /**
   * Get the platform name from the User Agent.
   */
  public function platform(): ?string
  {
    /** @var string|null $result */
    $result = $this->retrieveUsingCacheOrResolve('teams.platform', function (): ?string {
      /** @var array<string, string> $os */
      $os = MobileDetect::getOperatingSystems();

      return $this->findDetectionRulesAgainstUserAgent(
        array_merge($os, static::$additionalOperatingSystems)
      );
    });

    return $result;
  }

  /**
   * Get the browser name from the User Agent.
   */
  public function browser(): ?string
  {
    /** @var string|null $result */
    $result = $this->retrieveUsingCacheOrResolve('teams.browser', function (): ?string {
      /** @var array<string, string> $browsers */
      $browsers = MobileDetect::getBrowsers();

      return $this->findDetectionRulesAgainstUserAgent(
        array_merge(static::$additionalBrowsers, $browsers)
      );
    });

    return $result;
  }

  /**
   * Determine if the device is a desktop computer.
   */
  public function isDesktop(): bool
  {
    /** @var bool $result */
    $result = $this->retrieveUsingCacheOrResolve('teams.desktop', function (): bool {
      // Check specifically for cloudfront headers if the useragent === 'Amazon CloudFront'
      if (
        $this->getUserAgent() === static::$cloudFrontUA
        && $this->getHttpHeader('HTTP_CLOUDFRONT_IS_DESKTOP_VIEWER') === 'true'
      ) {
        return true;
      }

      return ! $this->isMobile() && ! $this->isTablet();
    });

    return $result;
  }

  /**
   * Match a detection rule and return the matched key.
   *
   * @param  array<string, string>  $rules
   */
  protected function findDetectionRulesAgainstUserAgent(array $rules): ?string
  {
    $userAgent = $this->getUserAgent();

    foreach ($rules as $key => $regex) {
      if (empty($regex)) {
        continue;
      }

      if ($this->match($regex, $userAgent ?? '')) {
        if ($key !== '') {
          return $key;
        }
        /** @var string|false $match */
        $match = reset($this->matchesArray);

        return $match !== false ? $match : null;
      }
    }

    return null;
  }

  /**
   * Retrieve from the given key from the cache or resolve the value.
   */
  protected function retrieveUsingCacheOrResolve(string $key, Closure $callback): mixed
  {
    $cacheKey = $this->createCacheKey($key);

    if (! is_null($cacheItem = $this->store[$cacheKey] ?? null)) {
      return $cacheItem;
    }

    return tap(call_user_func($callback), function (mixed $result) use ($cacheKey): void {
      $this->store[$cacheKey] = $result;
    });
  }

  /**
   * Merge multiple rules into one array.
   *
   * @param  array<string, string>  ...$all
   * @return array<string, string>
   */
  protected function mergeRules(...$all): array
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
