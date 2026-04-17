# profesjonalny-montaz-crm

CRM system for managing installation processes, clients, reclamations, sales, and documents.

## Architecture

- **Backend:** Laravel 9 / PHP 8.1
- **Database:** MySQL 8.0 (Eloquent ORM)
- **Frontend:** Blade templates + Laravel Mix (webpack)
- **PDF:** barryvdh/laravel-dompdf
- **Excel:** maatwebsite/excel
- **CMS:** kacper55331/praust-cms
- **Runtime:** Docker (PHP-FPM + Nginx + MySQL)

## Commands

```bash
# Development
php artisan serve                  # local dev (without Docker)
npm run dev                        # compile assets (watch mode: npm run watch)
docker compose up -d               # start full stack

# Testing
php artisan test                   # run PHPUnit tests
./vendor/bin/phpunit               # direct PHPUnit

# Database
php artisan migrate                # run migrations
php artisan db:seed                # seed database
php artisan migrate:fresh --seed   # reset + seed

# Assets
npm run prod                       # production build
```

## Project Structure

```
app/
  Http/Controllers/
    Admin/          ← all admin panel controllers
    Front/          ← public-facing controllers
  Models/
    Concerns/       ← shared model traits
    Enums/          ← PHP enums for status fields
    Exports/        ← Maatwebsite Excel export classes
resources/views/
  admin/
    builders/       ← builder management
    calendar/       ← calendar & scheduling
    client/         ← client management
    dashboard/      ← main dashboard
    document/       ← document generation
    monter/         ← installer/monter management
    process/        ← process tracking
    reclamation/    ← reclamation handling
    sale/           ← sales management
    statistic/      ← reports & statistics
    _inc/           ← shared layout partials
  emails/           ← mail templates
routes/
  web.php           ← web routes
  api.php           ← API routes
```

## Domain Modules

| Module | Description |
|--------|-------------|
| Builder | Installation companies/contractors |
| Client | End clients |
| Calendar | Scheduling and events |
| Process | Installation processes with categories and types |
| Reclamation | Complaints and reclamation tracking |
| Sale | Sales pipeline |
| Document | PDF document generation |
| Configuration | App-wide settings |

## Rules

@.claude/rules/code-style.md
@.claude/rules/testing.md

## Workflow

- Run `php artisan test` after any logic changes
- Use `php artisan make:model`, `make:controller`, `make:migration` — never create these files manually
- Always run `npm run dev` after changing Blade/CSS/JS assets to verify compilation
- Keep controllers thin — business logic in models or dedicated service classes
- Commits should be small and focused; describe what and why
