# Repository Guidelines

## Project Structure & Module Organization
- `app/` application code: `Controllers`, `Models`, `Views`, `Config`, `Libraries`, `Helpers`.
- `public/` web root (entry point `index.php`). Point your web server here.
- `tests/` PHPUnit tests (`unit`, `database`, `session`, `_support`).
- `writable/` runtime cache/logs/uploads; ensure it is writable.
- `vendor/` Composer dependencies (do not edit).
- Key config: `.env`, `app/Config/*`, routes in `app/Config/Routes.php`.

## Build, Test, and Development Commands
- Install deps: `composer install`
- Run dev server: `php spark serve` (e.g., `--port 8080`)
- Run tests: `composer test` or `vendor/bin/phpunit -c phpunit.xml.dist`
- Optimize autoload: `composer dump-autoload -o`
- Toggle framework source: `php builds release|development` then `composer update`

## Coding Style & Naming Conventions
- PHP 8.1+, PSR-12 style, 4-space indentation, one class per file.
- PSR-4 namespaces under `App\` (e.g., `app/Controllers/News.php` → `App\\Controllers\\News`).
- Controllers: PascalCase extending `BaseController`; Models: singular PascalCase; Views: snake_case `.php` in `app/Views/` (e.g., `welcome_message.php`).
- Use strict types, scalar/return types, and nullability where appropriate.

## Testing Guidelines
- Framework: PHPUnit 10; extend `CIUnitTestCase` (see `tests/unit/HealthTest.php`).
- Naming: file ends with `*Test.php`; class named `*Test`.
- Run with coverage: `vendor/bin/phpunit --coverage-text` (reports also in `build/logs/`).
- Database tests: configure `database.tests.*` env vars in `phpunit.xml.dist` or `.env` as needed.

## Commit & Pull Request Guidelines
- Commits: short imperative subject (max ~72 chars), optional scope, concise body explaining why.
- PRs: focused scope, clear description, linked issues, steps to verify, and notes on migrations/BC-breaks.
- Pre-submit checklist: `composer test` passes, routes updated (`app/Config/Routes.php`), docs/examples adjusted.

## Security & Configuration Tips
- Never commit secrets; keep `.env` local. Set `CI_ENVIRONMENT`, `app.baseURL`, DB settings, and encryption key.
- Ensure web root points to `public/`; keep `writable/` permissions restricted to the web user.
- Do not modify `vendor/`; place app code under `app/` and add routes/tests accordingly.
