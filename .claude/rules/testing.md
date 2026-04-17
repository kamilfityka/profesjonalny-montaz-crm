# Testing

## Stack

- PHPUnit 9.x via `php artisan test` or `./vendor/bin/phpunit`
- Mockery for mocking dependencies
- Config: `phpunit.xml` in project root

## Rules

- Test every public method on service/model classes with non-trivial logic
- Use `RefreshDatabase` trait for tests touching the database
- Use factories (`database/factories/`) to create test data — no raw DB inserts
- Mock external HTTP calls (Guzzle) — tests must not hit real APIs
- Test file location mirrors `app/` structure: `app/Models/Process.php` → `tests/Unit/Models/ProcessTest.php`
- Feature tests go in `tests/Feature/`, unit tests in `tests/Unit/`

## Running Tests

```bash
php artisan test                        # all tests
php artisan test --filter ProcessTest   # single class
php artisan test tests/Unit/            # directory
```

## Before Committing

- All tests must pass
- No skipped tests without a comment explaining why
