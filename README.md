<picture>
  <source media="(prefers-color-scheme: dark)" srcset=".github/img/dark.png">
  <source media="(prefers-color-scheme: light)" srcset=".github/img/light.png">
  <img alt="Druid Starter Kit" src=".github/img/light.png" width="100%">
</picture>

<p align="center">
<a href="https://github.com/druidweb/druid/blob/main/clover.xml"><img src="https://img.shields.io/badge/dynamic/xml?color=success&label=coverage&query=round%28%2F%2Fcoverage%2Fproject%2Fmetrics%2F%40coveredelements%20div%20%2F%2Fcoverage%2Fproject%2Fmetrics%2F%40elements%20%2A%20100%29&suffix=%25&url=https%3A%2F%2Fraw.githubusercontent.com%2Fdruidweb%2Fdruid%2Fmain%2Fclover.xml" alt="Coverage"></a>
<a href="https://github.com/druidweb/druid/actions"><img src="https://github.com/druidweb/druid/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/druidweb/druid"><img src="https://img.shields.io/packagist/dt/druidweb/druid" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/druidweb/druid"><img src="https://img.shields.io/packagist/v/druidweb/druid" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/druidweb/druid"><img src="https://img.shields.io/packagist/l/druidweb/druid" alt="License"></a>
</p>

# Druid Starter Kit

A modern, opinionated starter kit for Laravel 12+ applications with Vue 3, TypeScript, and Tailwind CSS.

## Features

- 🚀 [Laravel 12](https://laravel.com) - Latest Laravel with PHP 8.3+ support
- ⚡️ [Vue 3](https://vuejs.org) with [Vite](https://vitejs.dev)
- 🧩 [Shadcn-Vue](https://www.shadcn-vue.com) - Beautiful, accessible, and responsive UI components
- 🔧 [TypeScript](https://www.typescriptlang.org) - Full type safety
- 🎨 [Tailwind 4](https://tailwindcss.com) with dark mode support
- 📱 [Inertia.js](https://inertiajs.com) - Modern single-page apps without API complexity
- 🔒 [Laravel Sanctum](https://laravel.com/docs/sanctum) - API authentication
- 🧪 [Pest PHP](https://pestphp.com) - Testing with pleasure
- 🌐 [Laravel Dusk](https://laravel.com/docs/dusk) - Browser testing for Laravel
- ⚡️ [Vitest](https://vitest.dev) - Fast JavaScript unit testing
- 🔍 [Larastan](https://github.com/larastan/larastan) - Static analysis for Laravel
- 📝 [ESLint](https://eslint.org) + [Prettier](https://prettier.io) - Consistent code style
- 🔄 Automated releases with semantic-release
- 👷 GitHub Actions workflows for testing and deployment
- 🏗️ Pre-configured development environment
- 💯 100% code coverage with automated reporting

## Requirements

- PHP 8.3 or higher
- Composer 2+
- Node.js 18+ (preferably Bun)
- SQLite / MySQL / PostgreSQL

## Installation

First, ensure you have the Laravel installer v5.14+ installed globally:

```bash
composer global require laravel/installer
```

Then create a new Laravel application using this starter kit:

```bash
laravel new --using=druidweb/druid
```

For more information about Laravel starter kits, please refer to the [Laravel documentation](https://laravel.com/docs/12.x/starter-kits).

## Development

```bash
# Update all dependencies
bun run cb

# Start development server
bun run dev

# Build for production
bun run build

# Run tests
composer test

# Run tests with coverage
composer test:coverage

# Run browser tests (Laravel Dusk)
composer test:browser

# Run JavaScript tests (Vitest)
bun run test:js

# Run static analysis
composer analyse

# Format code
bun run format

# Lint code
bun run lint
```

## Maintenance Branches

This starter kit follows semantic versioning using maintenance branches:

- `main` - Latest development version
- `N.x` - Maintenance branches for major versions (e.g., `1.x`, `2.x`)

## Contributing

Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](https://github.com/druidweb/druid/security/policy) on how to report security vulnerabilities.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

# Test coverage generation
