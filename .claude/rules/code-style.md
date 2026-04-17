# Code Style & Architecture Guide

> Rules, not suggestions. Mandatory unless stated otherwise.

---

## Engineering Principles

- **KISS** — simplest solution that works; if it feels overengineered, it is
- **DRY** — extract reusable logic into services/helpers; avoid over-abstracting
- **YAGNI** — implement only what is needed now
- **SRP** — one class = one responsibility; one method = one clear purpose
- **Readability over cleverness** — avoid tricks and hidden behavior
- **Explicit over implicit** — no hidden side effects
- **Fail fast** — validate early, do not let invalid state propagate
- **Consistency over preference** — follow existing patterns
- **Boy Scout Rule** — leave code better than you found it

---

## Architecture

### Layers

| Layer | Responsibility |
|-------|---------------|
| Controller | Request/response only — call Actions or Praust base methods |
| Action | One class = one use case; contains business logic; no HTTP objects |
| Service | Integrations and reusable domain operations (e.g. file cloning, external APIs) |
| Model | Relationships, scopes, casts, small helpers only |
| Form Request | All validation logic |

### When to extract an Action

Extract to `app/Actions/` when a controller method:
- exceeds ~30 lines
- mixes file I/O, DB transactions, and model operations (e.g. `SaleController::getWin`, `CalendarController::postStore`)
- is called from more than one place

### Praust Framework Note

This project extends `PraustActionModel` and Praust base controllers for standard CRUD. Inheritance from Praust classes is **allowed and expected**. Do not fight the framework — put custom business logic in Actions/Services, not in Praust overrides.

### Folder structure

```
app/
  Actions/        ← business use cases (create if needed)
  Http/
    Controllers/
      Admin/
      Front/
    Requests/     ← Form Requests (create if needed)
  Models/
    Concerns/
    Enums/
    Exports/
  Services/       ← integrations, file ops, external APIs (create if needed)
```

---

## Inheritance & Composition

- Prefer composition over inheritance for application code
- No BaseService, BaseRepository, or similar shared base classes
- Shared logic goes into dedicated services or traits
- **Allowed:** extending Praust/Laravel framework classes — that is the project's design

---

## PHP / Laravel

- PHP 8.1+ features: enums, readonly properties, named arguments, match expressions
- `declare(strict_types=1);` in all new files
- Type hints everywhere — parameters, return types, properties
- Use `$request->validated()` — never `$request->all()`
- Prefer named scopes over raw query building in controllers
- Use Eloquent relationships — avoid manual joins unless performance-critical

**Forbidden in committed code:**
- `dd()`, `dump()`, `var_dump()`

**Use instead:**
- `\Log::info()` or Laravel Debugbar

### Naming Conventions

- Models: singular PascalCase — `ProcessCategory`
- Controllers: plural PascalCase + suffix — `ProcessCategoriesController`
- Migrations: snake_case with timestamp prefix
- Routes: kebab-case — `/admin/process-categories`
- Actions: verb + noun — `StoreCalendarEvent`, `CloneSaleToProcess`

---

## Database / Eloquent

- Always eager load relationships — avoid N+1 queries
- Use transactions for multi-step operations (`DB::transaction()`)
- Prefer `firstOrFail()` over `first()`
- Use Eloquent `$casts` instead of manual conversions
- No business logic in migrations

---

## Security

- Validate via Form Requests — never trust raw input
- Use Policies/Gates for authorization
- Never expose sensitive data in responses or logs
- `Hash::make()` for passwords
- Secrets in `.env` only

---

## Performance

- Avoid N+1 — use `with()` / `load()`
- Cache heavy operations
- Paginate large datasets
- Select only required columns when querying large tables

---

## Blade Templates

- No business logic in views
- Extract repeated HTML into partials in `resources/views/admin/_inc/`
- Use `@include`, `@component`, `@extends`/`@section`
- Escape with `{{ }}` — use `{!! !!}` only when intentionally rendering HTML

---

## JavaScript / Assets

- Plain JS (no TypeScript) — consistent with existing codebase
- `axios` for HTTP requests (already included)
- No `console.log` in committed code
- Asset entry points: `resources/js/app.js`, `resources/css/app.css`
- Prefer reusable functions and event delegation over globals

---

## Logging

- Use appropriate log levels (`info`, `warning`, `error`)
- Never log sensitive data (passwords, tokens, personal data)
- Prefer structured logs: `\Log::info('message', ['key' => $value])`

---

## Clean Code

- Max method length: ~30 lines — extract if longer
- Prefer early return over deep nesting
- No magic numbers — use named constants or config values
- Meaningful names — avoid abbreviations and single-letter variables

---

## Git

- Use conventional commits:
  - `feat:` new feature
  - `fix:` bug fix
  - `refactor:` restructure without behavior change
  - `test:` add/update tests
  - `chore:` tooling, deps, config
- Keep commits small and focused
- No direct commits to `main`

---

## Anti-Patterns (forbidden)

- Fat controllers — business logic belongs in Actions/Services
- Business logic in Blade views
- Base classes created just for code reuse
- God classes
- Deep inheritance trees in application code
- `static` helper chaos
