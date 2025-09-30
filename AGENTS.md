# Repository Guidelines

This guide captures the conventions we follow in the iron-crm codebase so updates stay consistent and easy to review.

## Project Structure & Module Organization
Core Laravel code lives under `app/`; keep domain logic in dedicated service classes within `app/Services` instead of controllers. API endpoints are declared in `routes/api.php` and should pair with request validators and resource transformers under `app/Http`. Vue 2 assets reside in `resources/js` with styles in `resources/sass`; Laravel Mix outputs compiled bundles to `public/`. Database migrations and idempotent seeders are in `database/migrations` and `database/seeds`. Tests mirror the app structure in `tests/Feature` and `tests/Unit`.

## Build, Test, and Development Commands
Run `composer install` and `npm install` after pulling new dependencies. `php artisan migrate --seed` synchronizes schema and baseline fixtures. For local API work use `php artisan serve --host=0.0.0.0 --port=8000`; docker users can run `docker-compose up web`. Frontend bundles: `npm run dev` (single build), `npm run watch` (hot reload), and `npm run prod` (optimized build). Execute the suite with `vendor/bin/phpunit` or filter with `vendor/bin/phpunit --filter TestName`.

## Coding Style & Naming Conventions
Follow PSR-12 with 4-space indentation in PHP, typed properties where Laravel 6 allows, and descriptive method names. Vue/JS files use 2-space indentation, single quotes, and Prettier via `npx prettier resources/js/**/*.js --write` before large diffs. Name controllers `*Controller`, jobs `*Job`, events `*Event`; use snake_case for columns, camelCase for JSON payloads, and kebab-case for Vue component files.

## Testing Guidelines
Feature and unit tests should use Laravel model factories from `database/factories`. Name classes `<Subject>Test.php`, add `@group` for external integrations, and keep JSON fixtures in `tests/Fixtures`. Make sure new behavior is covered before submitting PRs.

## Commit & Pull Request Guidelines
Commits follow short prefixes such as `feature:`, `fix:`, or `refactor:` and stay in present tense. Reference tracker IDs in the body (e.g., `Refs CRM-123`) and call out migrations or breaking API changes. Pull requests should include a purpose statement, testing notes with `vendor/bin/phpunit` output, and UI screenshots or GIFs when applicable.

## Environment & Configuration Tips
Copy `.env.example` to `.env`, update service credentials, then run `php artisan key:generate`. Cache config with `php artisan config:cache` before deploying, and keep secrets out of version control.
