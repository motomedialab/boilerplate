# MotoMediaLab Boilerplate

This is a boilerplate to be installed on a Laravel project.
It enforces coding standards and sets up code analysis and formatting tools automatically.

## Installation

Install the package as a development dependency in your Laravel project:

```bash
composer require --dev motomedialab/boilerplate
```

> [!IMPORTANT]
> This package **must** be installed in the `require-dev` block. If you attempt to install it in `require`, the installation will fail to prevent deployment of dev tools to production.

### What happens automatically on install:
When you install the package, it hooks into Laravel's package discovery to automatically:
1. Copy formatting and analysis configuration stubs to your project root.
2. Inject formatting scripts (`format`, `check`, and `preflight`) into your `package.json`.
3. Install frontend styling and formatting node packages via npm.

## PHP Packages

It'll install the following packages for development:

- `driftingly/rector-laravel` - Rector code formatting
- `larastan/larastan` - PHP static analysis
- `laravel/pao` - Simplified output for AI testing
- `laravel/pint` - Code formatting for PSR12 standards

## JavaScript packages

It'll install the following node packages:

- `prettier`
- `prettier-plugin-blade`
- `prettier-plugin-tailwindcss`

## Stubs

It'll copy the following stubs into your project:

- `stubs/phpstan.neon` to the root of your project
- `stubs/rector.php` to the root of your project
- `stubs/pint.json` to the root of your project
- `stubs/.prettierignore` to the root of your project
- `stubs/.prettierrc.json` to the root of your project

## Installed npm functions

It'll install the following commands in your `package.json`:

```
"format": "npx prettier --write resources",
"check": "npx prettier --check resources",
"preflight": "npm run format && ./vendor/bin/rector && ./vendor/bin/phpstan --memory-limit=2G && ./vendor/bin/pint --parallel && ./vendor/bin/pest --parallel"
```