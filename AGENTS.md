# Repository Guidelines

## Project Structure & Module Organization
- `app/` holds Laravel services, jobs, and HTTP layer; keep domain logic in dedicated service classes under `app/Services` when adding features.
- `routes/api.php` drives most endpoints; pair API additions with request validation and resource transformers in `app/Http`.
- Frontend assets live in `resources/js` (Vue 2) and `resources/sass`; compiled bundles land in `public/` via Laravel Mix.
- Data scaffolding sits in `database/migrations` and `database/seeds`; seeders should be idempotent for repeatable docker-compose spins.
- PHPUnit suites reside in `tests/Feature` and `tests/Unit`; mirror the namespace of the code under test.

## Build, Test, and Development Commands
- `composer install` / `npm install` bootstraps PHP and JS dependencies.
- `php artisan migrate --seed` syncs the schema and loads baseline fixtures.
- `php artisan serve --host=0.0.0.0 --port=8000` runs the API locally; docker users can instead run `docker-compose up web`.
- `npm run dev` builds assets once; `npm run watch` hot-rebuilds during frontend work; `npm run prod` emits minified bundles.
- `vendor/bin/phpunit` (or `vendor/bin/phpunit --filter …`) executes the test suite; integrate it into CI jobs.

## Coding Style & Naming Conventions
- Follow PSR-12 for PHP with 4-space indentation; prefer typed properties and return types when Laravel 6 allows them.
- Vue/JS files use 2-space indentation and single quotes; run `npx prettier resources/js/**/*.js --write` before large diffs.
- Controllers end with `Controller`, jobs with `Job`, events with `Event`; align filenames and namespaces accordingly.
- Use snake_case for DB columns, camelCase for request payload keys, and kebab-case for Vue component files.

## Testing Guidelines
- Co-locate factories in `database/factories` and reference them via Laravel model factories for deterministic seeds.
- Name tests `<Subject>Test.php` and use `@group` annotations when exercising external services.
- Provide HTTP JSON fixtures in `tests/Fixtures` if mocking integrations; clean them up after changes.

## Commit & Pull Request Guidelines
- Follow the existing short prefix pattern (`feature:`, `fix:`, `refactor:`) and write present-tense summaries.
- Reference tracker items in the body (e.g., `Refs CRM-123`) and list any migrations or breaking API changes.
- Pull requests need: purpose statement, testing notes (`vendor/bin/phpunit` output), and screenshots/GIFs for UI-visible updates.
