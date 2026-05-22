# MotoMediaLab Boilerplate

This is a boilerplate to be installed on a Laravel project.
It enforces coding standards.

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