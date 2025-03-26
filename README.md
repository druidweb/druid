# Dais App Starter Kit

A modern, opinionated starter kit for Laravel 12+ applications with Vue 3, TypeScript, and Tailwind CSS.

## Features

- 🚀 [Laravel 12+](https://laravel.com) - Latest Laravel with PHP 8.2+ support
- ⚡️ [Vue 3](https://vuejs.org) with [Vite](https://vitejs.dev)
- 🔧 [TypeScript](https://www.typescriptlang.org) - Full type safety
- 🎨 [Tailwind CSS](https://tailwindcss.com) with dark mode support
- 📱 [Inertia.js](https://inertiajs.com) - Modern single-page apps without API complexity
- 🔒 [Laravel Sanctum](https://laravel.com/docs/sanctum) - API authentication
- 🚦 [Laravel Reverb](https://laravel.com/docs/reverb) - WebSocket server
- 🧪 [Pest PHP](https://pestphp.com) - Testing with pleasure
- 📝 [ESLint](https://eslint.org) + [Prettier](https://prettier.io) - Consistent code style
- 🔄 Automated releases with semantic-release
- 👷 GitHub Actions workflows for testing and deployment
- 🏗️ Pre-configured development environment

## Requirements

- PHP 8.2 or higher
- Composer 2+
- Node.js 18+ (or Bun)
- SQLite / MySQL / PostgreSQL

## Installation

First, ensure you have the Laravel installer v5.14+ installed globally:

```bash
composer global require laravel/installer
```

Then create a new Laravel application using this starter kit:

```bash
laravel new my_app --using=daisorg/dais-app-starter-kit
```

For more information about Laravel starter kits, please refer to the [Laravel documentation](https://laravel.com/docs/12.x/starter-kits).

## Development

```bash
# Update all dependencies
bun run cn

# Start development server
composer dev

# Build for production
bun run build

# Run tests
./vendor/bin/pest

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

## Security

If you discover any security-related issues, please email hello@druidweb.com instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
