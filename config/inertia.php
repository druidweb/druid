<?php

declare(strict_types=1);

return [

  /**
   * SERVER SIDE RENDERING
   *
   * These options configure if and how Inertia uses Server Side Rendering
   * to pre-render every initial visit made to your application's pages
   * automatically. A separate rendering service should be available.
   *
   * See: https://inertiajs.com/server-side-rendering
   */
  'ssr' => [
    'enabled' => (bool) env('INERTIA_SSR_ENABLED', true),
    'runtime' => env('INERTIA_SSR_RUNTIME', 'node'),
    'ensure_runtime_exists' => (bool) env('INERTIA_SSR_ENSURE_RUNTIME_EXISTS', false),
    'url' => env('INERTIA_SSR_URL', 'http://127.0.0.1:13714'),
    'ensure_bundle_exists' => (bool) env('INERTIA_SSR_ENSURE_BUNDLE_EXISTS', true),
    // 'bundle' => base_path('bootstrap/ssr/ssr.mjs'),
    'throw_on_error' => (bool) env('INERTIA_SSR_THROW_ON_ERROR', false),
  ],

  /**
   * PAGES
   *
   * Defines where to look for page components and which file extensions to
   * consider. When `ensure_pages_exist` is enabled, Inertia verifies that
   * the requested component exists on disk before rendering.
   */
  'pages' => [
    'ensure_pages_exist' => false,

    'paths' => [
      resource_path('js/pages'),
    ],

    'extensions' => [
      'js',
      'jsx',
      'svelte',
      'ts',
      'tsx',
      'vue',
    ],
  ],

  /**
   * TESTING
   *
   * When using `assertInertia`, the assertion attempts to locate the
   * component as a file relative to `pages.paths` using any of the
   * `pages.extensions` defined above.
   */
  'testing' => [
    'ensure_pages_exist' => true,
  ],

  /**
   * EXPOSE SHARED PROP KEYS
   *
   * When enabled, each page response includes a `sharedProps` metadata key
   * listing the top-level prop keys registered via `Inertia::share`. The
   * frontend uses this to carry shared props over during instant visits.
   */
  'expose_shared_prop_keys' => true,

  /**
   * HISTORY
   *
   * Enable `encrypt` to encrypt page data before it is stored in the
   * browser's history state, preventing sensitive information from
   * remaining accessible after logout. May also be enabled per-request
   * or via the `inertia.encrypt` middleware.
   */
  'history' => [
    'encrypt' => (bool) env('INERTIA_ENCRYPT_HISTORY', false),
  ],
];
