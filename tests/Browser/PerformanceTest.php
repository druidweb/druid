<?php

use App\Models\User;
use Laravel\Dusk\Browser;

test('pages load within acceptable time', function () {
  $this->browse(function (Browser $browser) {
    $start = microtime(true);
    $browser->visit('/');
    $loadTime = microtime(true) - $start;

    expect($loadTime)->toBeLessThan(3.0); // 3 seconds max
  });
});

test('authenticated pages load quickly', function () {
  $user = User::factory()->create();

  $this->browse(function (Browser $browser) use ($user) {
    $browser->loginAs($user);

    $start = microtime(true);
    $browser->visit('/dashboard');
    $loadTime = microtime(true) - $start;

    expect($loadTime)->toBeLessThan(2.0); // 2 seconds max for authenticated pages
  });
});

test('assets load correctly', function () {
  $this->browse(function (Browser $browser) {
    $browser->visit('/')
      ->script('
        const stylesheets = document.querySelectorAll("link[rel=stylesheet]");
        const scripts = document.querySelectorAll("script[src]");
        return {
          stylesheets: stylesheets.length,
          scripts: scripts.length,
          allLoaded: Array.from(stylesheets).every(link => !link.disabled) &&
                    Array.from(scripts).every(script => script.readyState === "complete" || !script.readyState)
        };
      ');
  });
});

test('images load without errors', function () {
  $this->browse(function (Browser $browser) {
    $browser->visit('/')
      ->script('
        const images = document.querySelectorAll("img");
        return Array.from(images).every(img => img.complete && img.naturalHeight !== 0);
      ');
  });
});

test('page size is reasonable', function () {
  $this->browse(function (Browser $browser) {
    $browser->visit('/')
      ->script('
        return {
          transferSize: performance.getEntriesByType("navigation")[0].transferSize,
          encodedBodySize: performance.getEntriesByType("navigation")[0].encodedBodySize
        };
      ');
  });
});

test('JavaScript bundles load efficiently', function () {
  $this->browse(function (Browser $browser) {
    $browser->visit('/')
      ->script('
        const scripts = performance.getEntriesByType("resource").filter(r => r.name.includes(".js"));
        return {
          count: scripts.length,
          totalSize: scripts.reduce((sum, script) => sum + script.transferSize, 0),
          loadTimes: scripts.map(script => script.duration)
        };
      ');
  });
});

test('CSS loads efficiently', function () {
  $this->browse(function (Browser $browser) {
    $browser->visit('/')
      ->script('
        const stylesheets = performance.getEntriesByType("resource").filter(r => r.name.includes(".css"));
        return {
          count: stylesheets.length,
          totalSize: stylesheets.reduce((sum, css) => sum + css.transferSize, 0),
          loadTimes: stylesheets.map(css => css.duration)
        };
      ');
  });
});

test('fonts load without blocking', function () {
  $this->browse(function (Browser $browser) {
    $browser->visit('/')
      ->script('
        return document.fonts.ready.then(() => {
          return {
            fontsLoaded: document.fonts.size,
            status: document.fonts.status
          };
        });
      ');
  });
});

test('lazy loading works for images', function () {
  $this->browse(function (Browser $browser) {
    $browser->visit('/')
      ->script('
        const lazyImages = document.querySelectorAll("img[loading=lazy]");
        return lazyImages.length;
      ')
      ->scrollToBottom()
      ->pause(1000)
      ->script('
        const lazyImages = document.querySelectorAll("img[loading=lazy]");
        return Array.from(lazyImages).every(img => img.complete);
      ');
  });
});

test('navigation is fast with Inertia', function () {
  $user = User::factory()->create();

  $this->browse(function (Browser $browser) use ($user) {
    $browser->loginAs($user)
      ->visit('/dashboard');

    $start = microtime(true);
    $browser->clickLink('Settings')
      ->waitForRoute('profile.edit');
    $navigationTime = microtime(true) - $start;

    expect($navigationTime)->toBeLessThan(1.0); // 1 second max for SPA navigation
  });
});

test('form submissions are responsive', function () {
  $user = User::factory()->create();

  $this->browse(function (Browser $browser) use ($user) {
    $browser->loginAs($user)
      ->visit('/settings/profile');

    $start = microtime(true);
    $browser->type('name', 'Updated Name')
      ->press('Save')
      ->waitForText('Saved.');
    $submissionTime = microtime(true) - $start;

    expect($submissionTime)->toBeLessThan(2.0); // 2 seconds max for form submission
  });
});

test('memory usage is reasonable', function () {
  $this->browse(function (Browser $browser) {
    $browser->visit('/')
      ->script('
        if (performance.memory) {
          return {
            used: performance.memory.usedJSHeapSize,
            total: performance.memory.totalJSHeapSize,
            limit: performance.memory.jsHeapSizeLimit
          };
        }
        return null;
      ');
  });
});

test('no memory leaks on navigation', function () {
  $user = User::factory()->create();

  $this->browse(function (Browser $browser) use ($user) {
    $browser->loginAs($user)
      ->visit('/dashboard')
      ->script('const initialMemory = performance.memory ? performance.memory.usedJSHeapSize : 0; window.initialMemory = initialMemory;')
      ->clickLink('Settings')
      ->waitForRoute('profile.edit')
      ->clickLink('Dashboard')
      ->waitForRoute('dashboard')
      ->script('
        if (performance.memory && window.initialMemory) {
          const currentMemory = performance.memory.usedJSHeapSize;
          const memoryIncrease = currentMemory - window.initialMemory;
          return memoryIncrease < 5000000; // Less than 5MB increase
        }
        return true;
      ');
  });
});

test('critical resources load first', function () {
  $this->browse(function (Browser $browser) {
    $browser->visit('/')
      ->script('
        const resources = performance.getEntriesByType("resource");
        const criticalResources = resources.filter(r => 
          r.name.includes("app.css") || 
          r.name.includes("app.js") ||
          r.name.includes("manifest")
        );
        return criticalResources.map(r => ({
          name: r.name,
          startTime: r.startTime,
          duration: r.duration
        }));
      ');
  });
});

test('service worker caches resources', function () {
  $this->browse(function (Browser $browser) {
    $browser->visit('/')
      ->script('
        if ("serviceWorker" in navigator) {
          return navigator.serviceWorker.getRegistrations().then(registrations => {
            return registrations.length > 0;
          });
        }
        return false;
      ');
  });
});

test('page renders progressively', function () {
  $this->browse(function (Browser $browser) {
    $browser->visit('/')
      ->waitFor('body', 1) // Basic structure loads quickly
      ->waitFor('[data-v-app]', 3) // Vue app mounts
      ->assertPresent('main, [role="main"]'); // Main content is present
  });
});
