# Code Style

## PHP / Laravel

- PHP 8.1+ features preferred: enums, readonly properties, named arguments, match expressions
- Strict types declaration in all new files: `declare(strict_types=1);`
- Type hints everywhere — parameters, return types, properties
- No `var_dump`, `dd`, `dump` left in committed code — use `\Log::info()` or Laravel Debugbar
- Use `$request->validated()` when working with Form Requests — never raw `$request->all()`
- Prefer named scopes on models over raw query building in controllers
- Use Eloquent relationships — avoid manual joins unless performance-critical
- Follow Laravel naming conventions:
  - Models: singular PascalCase (`ProcessCategory`)
  - Controllers: plural PascalCase + Controller suffix (`ProcessCategoriesController`)
  - Migrations: snake_case with timestamp prefix
  - Routes: kebab-case slugs (`/admin/process-categories`)

## Blade Templates

- Extract repeated HTML into partials in `resources/views/admin/_inc/`
- Use `@include`, `@component`, or `@extends`/`@section` — no inline PHP logic in views
- Escape output with `{{ }}` — use `{!! !!}` only when intentionally rendering HTML

## JavaScript / Assets

- Plain JS (no TypeScript) — keep consistent with existing codebase
- Use `axios` for HTTP requests (already included)
- No `console.log` in committed code
- Asset entry points: `resources/js/app.js`, `resources/css/app.css`

## General

- Max method length: ~30 lines — extract if longer
- Prefer `early return` over deep nesting
- No magic numbers — use named constants or config values
