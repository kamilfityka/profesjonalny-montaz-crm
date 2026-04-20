Oba agenty zakończyły pracę. Oto szczegółowy plan implementacji:

---

## Plan implementacji: Rozszerzenie CRM o 9 funkcjonalności

### Obecny stan projektu

- **Stack**: Laravel 9, PHP 8.1+, Praust CMS (własny framework admina), Blade, DomPDF, Maatwebsite Excel
- **Moduł reklamacji**: Model `Reclamation` z polami: name, phone, address, text, closed_at, priority, type_id, client_id, user_id
- **Kanban**: Sprawy wyświetlane na tablicy Kanban wg kategorii (`ReclamationCategory`)
- **Brak**: API (`routes/api.php` puste), integracji z WP, szablonów e-mail, systemu notatek

---

### Kolejność implementacji (wg zależności)

```
3. Rozszerzona karta sprawy  ← FUNDAMENT (nowe kolumny DB)
2. Pole pilności
6. System notatek
5. Podgląd załączników inline
4. Logika 18 miesięcy         ← zależy od pola purchase_date z Feature 3
7. Szablony e-maili           ← zależy od Feature 6 (historia) i Feature 4 (odrzucenie)
1. Integracja WordPress       ← zależy od Feature 3 (nowe pola w API)
8. Generowanie PDF            ← zależy od Feature 3 + Feature 6
9. Dokumentacja użytkownika   ← zależy od wszystkich
```

---

### FEATURE 3: Rozszerzona karta sprawy (fundament)

**Migracja**: `database/migrations/xxxx_add_extended_fields_to_reclamations_table.php`
- `warranty` (boolean, default false) — checkbox gwarancja objęty/poza
- `purchase_date` (date, nullable) — data zakupu
- `fault_description` (text, nullable) — szczegółowy opis usterki
- `fault_category` (string, nullable) — kategoryzacja

**Nowy enum**: `app/Models/Enums/FaultCategory.php`
- Wzór: istniejący `app/Models/Enums/Priority.php`
- Wartości: `Wada produktu`, `Nieprawidłowy montaż`, `Niewłaściwe użytkowanie`, `Brak zrozumienia jednej ze stron`, `Nie można ocenić odpowiedzialności`

**Modyfikacja**: `app/Models/Reclamation.php`
- Dodanie pól do metody `fields()` korzystając z istniejących typów:
    - `YesNo::make("warranty")` (wzór: `Sale.php` linia 72)
    - `DateTime::make("purchase_date")`
    - `Tinymce::make("fault_description")`
    - `Radio::make("fault_category")->options(FaultCategory::array())`
- Dowód zakupu: obsługiwany przez istniejący system `PraustAttachments` (użytkownik uploaduje pliki jak dotychczas)

**Seeder**: `database/seeders/ReclamationSeeder.php` — dodanie nowych pól do faker data

---

### FEATURE 2: Pole pilności (Pilne/Niepilne)

**Nowy enum**: `app/Models/Enums/Urgency.php`
- `URGENT = 'Pilne'`, `NOT_URGENT = 'Niepilne'`

**Migracja**: dodanie kolumny `urgency` (string, default `'Niepilne'`) do tabeli `reclamations`

**Modyfikacja**: `app/Models/Reclamation.php`
- `Radio::make("urgency")->options(Urgency::array())` w `fields()`

**Wizualne wyróżnienie**: `resources/views/admin/reclamation/category/partials/kanban.blade.php`
- Dodanie klasy CSS `task-urgent` gdy `$data->urgency === 'Pilne'`
- Styl: czerwona ramka + jasno-czerwone tło (Bootstrap jest już w projekcie)

**Export**: `app/Models/Exports/Reclamations.php` — dodanie kolumny pilności

---

### FEATURE 6: System notatek z logiem zdarzeń

**Migracja**: `create_reclamation_notes_table`
- `reclamation_id` (FK), `user_id` (FK nullable), `type` (string: `manual`/`auto_status_change`/`auto_email_sent`/`auto_created`), `content` (text), `timestamps`

**Nowy model**: `app/Models/ReclamationNote.php`
- Zwykły Eloquent model (nie Praust), `fillable = ['reclamation_id', 'user_id', 'type', 'content']`

**Relacja w** `app/Models/Reclamation.php`:
- `notes(): HasMany` z `orderByDesc('created_at')`

**Serwis**: `app/Services/ReclamationLogger.php`
- Statyczna metoda `log($reclamationId, $type, $content)` — wywoływana z hooków kontrolera (`afterStore`, `afterUpdate`) i akcji e-mail

**Kontroler**: `app/Http/Controllers/Admin/ReclamationController.php`
- Nowa metoda `postNote($request, $id)` — zapis ręcznej notatki

**Route**: `routes/web.php` — zmiana `generateDefaultRoute('reclamation')` na wariant z callback:
```php
generateDefaultRoute('reclamation', function() {
    app('router')->post('/{id}/note', [ReclamationController::class, 'postNote'])->name('reclamation-note');
});
```

**Widok**: `resources/views/admin/reclamation/partials/notes.blade.php`
- Timeline notatek (auto + manual) z datownikiem
- Formularz dodawania nowej notatki (textarea + submit)

**Integracja**: Override `resources/views/admin/reclamation/edit.blade.php` — Praust szuka `admin.reclamation.edit` przed fallbackiem na `praust::admin.default.edit`

---

### FEATURE 5: Podgląd załączników inline

**Widok**: `resources/views/admin/reclamation/partials/attachments-preview.blade.php`
- Iteracja po `$data->admin_attachments`
- Obrazy (jpg/png/gif/webp): `<img>` z lightboxem (Bootstrap modal)
- Wideo (mp4/webm): `<video controls>`
- Inne pliki: link do pobrania (jak dotychczas)
- Detekcja typu: `pathinfo($attachment->file, PATHINFO_EXTENSION)`

**Integracja**: Osadzenie w overrideowanym `edit.blade.php`

---

### FEATURE 4: Logika 18 miesięcy

**Accessory w** `app/Models/Reclamation.php`:
```php
getWarrantyExpiredAttribute(): bool     // Carbon::parse(purchase_date)->addMonths(18)->isPast()
getWarrantyDaysOverdueAttribute(): ?int // ile dni po terminie
```

**Widok**: `resources/views/admin/reclamation/partials/warranty-alert.blade.php`
- Alert: „Termin bezpłatnej regulacji minął **X dni** temu"
- Przycisk „Wyślij e-mail o odrzuceniu" → triggeruje szablon odrzucenia (Feature 7)
- Umieszczony na górze formularza edycji

Obliczenie czysto w runtime (accessor) — bez crona.

---

### FEATURE 7: Szablony e-maili z historią komunikacji

**Migracja**: `create_email_templates_table`
- `name`, `subject`, `body` (z placeholderami: `{client_name}`, `{case_number}` itp.)

**Seeder**: `database/seeders/EmailTemplateSeeder.php`
- 5 szablonów: Przyjęcie / Odrzucenie / Prośba o dokumenty / Wizyta serwisanta / Inne

**Model**: `app/Models/EmailTemplate.php` — z Praust `fields()` do zarządzania w adminie

**Mailable**: `app/Mail/ReclamationEmail.php` — wzór: `app/Mail/Contact.php`

**Widok e-mail**: `resources/views/emails/reclamation.blade.php`

**Kontroler** (w ReclamationController):
- `getEmailTemplates($request, $id)` — JSON z listą szablonów
- `postSendEmail($request, $id)` — wysyłka + log w ReclamationNote

**Panel w widoku edycji**: `resources/views/admin/reclamation/partials/email-panel.blade.php`
- Historia komunikacji (filtrowane notatki `type = 'auto_email_sent'`)
- Dropdown wyboru szablonu + podgląd + przycisk wyślij

**Admin szablonów**: dodanie do `Configuration.php`, nowy kontroler `EmailTemplateController`, route `generateDefaultRoute('email-template')`

---

### FEATURE 1: Integracja WordPress → CRM

**API route**: `routes/api.php`
```php
Route::post('/reclamation', [ReclamationApiController::class, 'store'])->middleware('throttle:10,1');
```

**Middleware**: `app/Http/Middleware/VerifyApiToken.php` — sprawdzanie `Authorization: Bearer {token}` vs `config('services.wordpress.api_token')`

**Config**: `config/services.php` — sekcja `wordpress.api_token` z env `WORDPRESS_API_TOKEN`

**Kontroler**: `app/Http/Controllers/Api/ReclamationApiController.php`
- Walidacja danych z formularza WP
- Znajdź lub utwórz Client po phone/email
- Utwórz Reclamation z kategorią „Oczekuje na weryfikację"
- Log via `ReclamationLogger::log()`
- Return JSON z ID sprawy

**Seeder/migracja**: Zapewnienie istnienia `ReclamationCategory` o nazwie „Oczekuje na weryfikację"

---

### FEATURE 8: Generowanie PDF — protokół serwisanta

**Widok**: `resources/views/admin/reclamation/pdf.blade.php`
- Wzór: `resources/views/admin/document/pdf.blade.php` (ten sam font Maisonneue, style)
- Sekcje: nagłówek firmy, „PROTOKÓŁ SERWISOWY", dane sprawy, klient, data zakupu, gwarancja, kategoria usterki, opis, historia notatek, linie na podpisy

**Kontroler** (w ReclamationController):
```php
public function getPdf($request, $id) // wzór: DocumentController::getPdf()
```

**Route**: `app('router')->get('/pdf/{id}', [ReclamationController::class, 'getPdf'])->name('reclamation-pdf');`

**Przycisk**: w overrideowanym `edit.blade.php` → link do `custom_route('reclamation-pdf')`

---

### FEATURE 9: Dokumentacja użytkownika

**Podejście**: strona pomocy w panelu admina

**Pliki**:
- `app/Http/Controllers/Admin/HelpController.php`
- `resources/views/admin/help/index.blade.php`
- Route: `generateDefaultRoute('help')`
- `Configuration.php`: dodanie zakładki „Pomoc" w menu

**Treść**: instrukcja obsługi każdej nowej funkcji z opisami krok po kroku

---

### Podsumowanie plików

| Nowe pliki (23) | Feature |
|---|---|
| 4 migracje | 2, 3, 6, 7 |
| 2 enumy (`FaultCategory`, `Urgency`) | 2, 3 |
| 3 modele (`ReclamationNote`, `EmailTemplate`, + serwis `ReclamationLogger`) | 6, 7 |
| 3 kontrolery (Api, EmailTemplate, Help) | 1, 7, 9 |
| 1 middleware (`VerifyApiToken`) | 1 |
| 1 mailable (`ReclamationEmail`) | 7 |
| 1 seeder (`EmailTemplateSeeder`) | 7 |
| 7 widoków Blade (edit override, 4 partiale, PDF, email) | 3-8 |
| 1 widok pomocy | 9 |

| Modyfikowane pliki (11) | Zmiany |
|---|---|
| `Reclamation.php` | Nowe pola, relacje, accessory |
| `ReclamationController.php` | Nowe metody: note, pdf, email, auto-logging |
| `routes/web.php` | Callback do reclamation route |
| `routes/api.php` | Endpoint WordPress |
| `Configuration.php` | Nowe zakładki menu |
| `kanban.blade.php` | Klasa CSS dla pilności |
| `Reclamations.php` (export) | Nowe kolumny |
| `ReclamationSeeder.php` | Nowe pola |
| `DatabaseSeeder.php` | EmailTemplateSeeder |
| `Kernel.php` | Middleware API |
| `config/services.php` | Token WP |

---

## STATUS IMPLEMENTACJI (2026-04-04)

### WSZYSTKIE 9 FUNKCJONALNOŚCI ZOSTAŁO ZAIMPLEMENTOWANYCH

---

### FEATURE 3: Rozszerzona karta sprawy - ZAIMPLEMENTOWANE
- [x] Migracja: `database/migrations/2026_04_04_000001_add_extended_fields_to_reclamations_table.php` (warranty, purchase_date, fault_description, fault_category, urgency)
- [x] Enum: `app/Models/Enums/FaultCategory.php`
- [x] Model `Reclamation.php` — dodane pola: YesNo(warranty), DateTime(purchase_date), Tinymce(fault_description), Radio(fault_category)
- [x] Seeder: `ReclamationSeeder.php` — zaktualizowany o nowe pola

### FEATURE 2: Pole pilności - ZAIMPLEMENTOWANE
- [x] Enum: `app/Models/Enums/Urgency.php` (Pilne / Niepilne)
- [x] Kolumna `urgency` w migracji Feature 3 (ta sama migracja)
- [x] Model: Radio(urgency) dodane do `fields()`
- [x] Kanban: `kanban.blade.php` — klasa `task-urgent` (czerwona ramka) + badge "PILNE"
- [x] Export: `Reclamations.php` — dodane kolumny: Pilność, Gwarancja, Data zakupu, Kategoria usterki

### FEATURE 6: System notatek z logiem zdarzeń - ZAIMPLEMENTOWANE
- [x] Migracja: `database/migrations/2026_04_04_000002_create_reclamation_notes_table.php`
- [x] Model: `app/Models/ReclamationNote.php`
- [x] Serwis: `app/Services/ReclamationLogger.php`
- [x] Relacja: `Reclamation::notes()` HasMany
- [x] Kontroler: `ReclamationController::postNote()` + auto-logging w `afterStore` / `afterUpdate`
- [x] Route: `POST /{id}/note` (reclamation-note)
- [x] Widok: `resources/views/admin/reclamation/partials/notes.blade.php` — timeline z formularzem

### FEATURE 5: Podgląd załączników inline - ZAIMPLEMENTOWANE
- [x] Widok: `resources/views/admin/reclamation/partials/attachments-preview.blade.php`
- [x] Obrazy: miniaturka + Bootstrap modal lightbox
- [x] Wideo: `<video controls>`
- [x] Inne pliki: link do pobrania
- [x] Integracja: osadzone w `edit.blade.php`

### FEATURE 4: Logika 18 miesięcy - ZAIMPLEMENTOWANE
- [x] Accessory: `getWarrantyExpiredAttribute()` i `getWarrantyDaysOverdueAttribute()` w `Reclamation.php`
- [x] Widok: `resources/views/admin/reclamation/partials/warranty-alert.blade.php`
- [x] Alert czerwony (wygasła) z przyciskiem "Wyślij e-mail o odrzuceniu"
- [x] Alert zielony (aktywna) z informacją ile dni pozostało

### FEATURE 7: Szablony e-maili - ZAIMPLEMENTOWANE
- [x] Migracja: `database/migrations/2026_04_04_000003_create_email_templates_table.php`
- [x] Model: `app/Models/EmailTemplate.php` (PraustActionModel z fields())
- [x] Seeder: `database/seeders/EmailTemplateSeeder.php` — 5 szablonów
- [x] Mailable: `app/Mail/ReclamationEmail.php`
- [x] Widok e-mail: `resources/views/emails/reclamation.blade.php`
- [x] Kontroler: `getEmailTemplates()` + `postSendEmail()` w ReclamationController
- [x] Panel: `resources/views/admin/reclamation/partials/email-panel.blade.php` — historia + modal wysyłki
- [x] Admin szablonów: `EmailTemplateController.php`, route `email-template`, zakładka w Configuration

### FEATURE 1: Integracja WordPress → CRM - ZAIMPLEMENTOWANE
- [x] API route: `routes/api.php` — `POST /api/reclamation` z throttle
- [x] Middleware: `app/Http/Middleware/VerifyApiToken.php` — Bearer token
- [x] Config: `config/services.php` — sekcja `wordpress.api_token` (env: WORDPRESS_API_TOKEN)
- [x] Kontroler: `app/Http/Controllers/Api/ReclamationApiController.php`
- [x] Migracja: `2026_04_04_000004_add_verification_reclamation_category.php` — kategoria "Oczekuje na weryfikację"

### FEATURE 8: Generowanie PDF - ZAIMPLEMENTOWANE
- [x] Widok: `resources/views/admin/reclamation/pdf.blade.php` (font Maisonneue, styl jak document/pdf)
- [x] Kontroler: `ReclamationController::getPdf()`
- [x] Route: `GET /pdf/{id}` (reclamation-pdf)
- [x] Przycisk: w `edit.blade.php` obok tytułu sprawy

### FEATURE 9: Dokumentacja użytkownika - ZAIMPLEMENTOWANE
- [x] Kontroler: `app/Http/Controllers/Admin/HelpController.php`
- [x] Widok: `resources/views/admin/help/index.blade.php` — pełna instrukcja wszystkich funkcji
- [x] Route: `/help` w `web.php`
- [x] Menu: zakładka "Pomoc" dodana do `Configuration.php`

---

### Podsumowanie utworzonych plików

| Nowy plik | Feature |
|---|---|
| `database/migrations/2026_04_04_000001_add_extended_fields_to_reclamations_table.php` | 2, 3 |
| `database/migrations/2026_04_04_000002_create_reclamation_notes_table.php` | 6 |
| `database/migrations/2026_04_04_000003_create_email_templates_table.php` | 7 |
| `database/migrations/2026_04_04_000004_add_verification_reclamation_category.php` | 1 |
| `app/Models/Enums/FaultCategory.php` | 3 |
| `app/Models/Enums/Urgency.php` | 2 |
| `app/Models/ReclamationNote.php` | 6 |
| `app/Models/EmailTemplate.php` | 7 |
| `app/Services/ReclamationLogger.php` | 6 |
| `app/Http/Controllers/Api/ReclamationApiController.php` | 1 |
| `app/Http/Controllers/Admin/EmailTemplateController.php` | 7 |
| `app/Http/Controllers/Admin/HelpController.php` | 9 |
| `app/Http/Middleware/VerifyApiToken.php` | 1 |
| `app/Mail/ReclamationEmail.php` | 7 |
| `database/seeders/EmailTemplateSeeder.php` | 7 |
| `resources/views/admin/reclamation/edit.blade.php` | 3-8 |
| `resources/views/admin/reclamation/partials/notes.blade.php` | 6 |
| `resources/views/admin/reclamation/partials/attachments-preview.blade.php` | 5 |
| `resources/views/admin/reclamation/partials/warranty-alert.blade.php` | 4 |
| `resources/views/admin/reclamation/partials/email-panel.blade.php` | 7 |
| `resources/views/admin/reclamation/pdf.blade.php` | 8 |
| `resources/views/admin/help/index.blade.php` | 9 |
| `resources/views/emails/reclamation.blade.php` | 7 |

### Zmodyfikowane pliki

| Plik | Zmiany |
|---|---|
| `app/Models/Reclamation.php` | Nowe pola, relacja notes(), accessory warranty |
| `app/Http/Controllers/Admin/ReclamationController.php` | postNote, getPdf, getEmailTemplates, postSendEmail, auto-logging |
| `routes/web.php` | Callback reclamation, email-template, help |
| `routes/api.php` | Endpoint WordPress |
| `app/Models/Configuration.php` | Zakładki: Pomoc, Szablony e-maili |
| `resources/views/admin/reclamation/category/partials/kanban.blade.php` | task-urgent, badge PILNE |
| `app/Models/Exports/Reclamations.php` | Nowe kolumny exportu |
| `database/seeders/ReclamationSeeder.php` | Nowe pola faker |
| `database/seeders/DatabaseSeeder.php` | EmailTemplateSeeder |
| `config/services.php` | wordpress.api_token |

### Po wdrożeniu należy wykonać
```bash
php artisan migrate
php artisan db:seed --class=EmailTemplateSeeder
# Ustawić w .env: WORDPRESS_API_TOKEN=twoj-tajny-token
```
