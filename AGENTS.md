# Repository Guidelines

## Project Structure & Modules

Linguide is a Laravel 12 Telegram bot built with PHP 8.4. Most application code is under `app/`: `Core/Modules` contains domain logic grouped by feature (such as `Term`, `User`, and `Ai`), `Infrastructure/Modules` contains persistence and external-service adapters, and `Interfaces/Telegram` and `Interfaces/Console` expose bot and command entry points. Keep feature-specific code in its module. Migrations and seeders live beside infrastructure modules; shared Laravel configuration is in `config/`. Prompts and dictionary data are in `resources/`. Module tests belong in `app/Core/Modules/<Module>/Tests/Unit` or `Feature`.

## Build, Test & Development

- `composer install` installs PHP dependencies.
- `composer run setup` prepares the environment, database, and frontend dependencies/build; review its `.env` and migration effects before using it on an existing checkout.
- `composer run dev` starts Laravel, queue, log tailing, and Vite development processes.
- `composer test` clears cached configuration and runs `php artisan test`.
- `composer phpstan` runs static analysis using `phpstan.neon`.
- `vendor/bin/pint` formats PHP code with Laravel Pint.

For local setup, copy `.env.example` to `.env`, set the required Telegram and AI credentials, then run migrations with `php artisan migrate`.

## Coding Style & Naming

Follow Laravel Pint defaults and PSR-4 autoloading (`App\\` maps to `app/`). Use `StudlyCase` for classes and module directories, descriptive `camelCase` methods and variables, and suffix names according to their role (for example, `...Action`, `...Task`, `...Dto`, and `...Handler`). Keep boundaries clear: core modules hold business behavior; infrastructure implements persistence and integrations.

## Testing

Use PHPUnit through Laravel's test runner. Name tests clearly and group them by module and behavior in the configured `Unit` and `Feature` directories. Run `composer test` before submitting changes; add focused tests for behavior changes and cover edge cases in parsers, validators, and external-service boundaries.

## Commits & Pull Requests

Recent history uses short, lowercase imperative summaries, such as `add reminders for word review` and `fix start-container`. Keep each commit focused and use the same style. Pull requests should explain the user-visible or operational change, note relevant configuration or migration steps, link related issues when available, and include screenshots for UI-facing changes. Mention the checks you ran and any that remain outstanding.

## Configuration & Sensitive Data

Keep credentials and environment-specific values in `.env`; never commit secrets. Update the relevant files under `config/` when adding configurable integrations, and document any new required environment variables in the pull request.
