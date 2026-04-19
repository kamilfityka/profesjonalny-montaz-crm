# Plan wdrożenia – `profesjonalny-montaz-crm-base` (Praust)

> Repozytorium: `kamilfityka/profesjonalny-montaz-crm-base`
> Pakiet composer: `kamilfityka/profesjonalny-montaz-crm-base: 0.*`
> Charakter zmian: rozszerzenia generycznego silnika (CRUD + załączniki + formularze + layout admina) – **brak logiki domenowej branży montażowej**.

---

## Zakres

Funkcje z listy klienta, które powinny trafić do base, ponieważ mają charakter przekrojowy (używalne w wielu modułach CRM: `Reclamation`, `Sale`, `Process`, `Calendar`, `Client`):

1. **Podgląd załączników inline** – rozszerzenie traita `PraustAttachments`.
2. **System notatek + log zdarzeń** – nowy trait `PraustNotes` + generyczny observer bazowy.
3. **Szablony e-maili + historia wysyłek** – nowy moduł admina (CRUD) + serwis wysyłkowy.

Logika branżowa (18 miesięcy, 5 kategorii odpowiedzialności, webhook z konkretnego formularza WP, protokół serwisanta) – nie należy do base. Trafia do `profesjonalny-montaz-crm`.

---

## Etap B1 – Inline preview załączników (globalnie)

### Cel
`PraustAttachments` dziś renderuje listę pobieralnych linków. Potrzebujemy automatycznego podglądu inline dla obrazów (`<img>`) i wideo (`<video controls>`), z fallbackiem dla pozostałych MIME.

### Zmiany

- `src/Models/Concerns/PraustAttachments.php`
  - dodać akcesor `isImage(): bool`, `isVideo(): bool`, `mimeType(): string` (oparte o `Storage::mimeType()` lub kolumnę w migracji – patrz niżej).
  - dodać konfigurowalną listę rozszerzeń: `image/*`, `video/*` – z możliwością nadpisania przez `config('praust.attachments.inline_mime')`.

- Migracja `xxxx_add_mime_type_to_praust_attachments.php` (opcjonalnie – jeżeli tabela załączników nie trzyma MIME, dodać `mime_type` VARCHAR(100) NULL). Backfill w komendzie artisan `praust:attachments:backfill-mime`.

- Widok `resources/views/admin/_inc/attachments.blade.php` (lub odpowiednik w pakiecie):
  - dla `isImage()` → `<img>` z lazy-loadem + lightbox (klik → powiększenie).
  - dla `isVideo()` → `<video controls preload="metadata">`.
  - fallback → obecny link z ikoną i nazwą pliku.

- Asset JS `resources/js/praust-attachments.js` – prosty lightbox (czysty JS, zgodnie z regułą „Plain JS, no TS").

- Konfiguracja `config/praust.php`:
  ```php
  'attachments' => [
      'inline_mime' => ['image/*', 'video/*'],
      'lightbox' => true,
      'video_max_width' => 640,
  ],
  ```

### Testy
- Unit test na `isImage()`/`isVideo()` dla różnych MIME.
- Feature test renderowania widoku z mockowanym załącznikiem.

### Kompatybilność wstecz
- Trait nadal renderuje listę linków jeśli `config('praust.attachments.inline_mime')` = `[]`.
- Żadne sygnatury publiczne nie zmieniają się.

---

## Etap B2 – Generyczny system notatek + log zdarzeń

### Cel
Dowolny model korzystający z traita `PraustNotes` dostaje:
- relację `notes(): morphMany` – wpisy ręczne i automatyczne,
- event log (zmiany statusu, przypisania, wysyłki e-maili) zapisywany w tej samej tabeli z `type='auto'`,
- UI: chronologiczna lista w karcie rekordu + formularz dodania/edycji ręcznej notatki.

### Zmiany

- Migracja `xxxx_create_praust_notes_table.php`:
  ```
  id, notable_type, notable_id, user_id (nullable),
  type ENUM('auto','manual') default 'manual',
  event VARCHAR(100) NULL,    -- np. 'status.changed','email.sent'
  body TEXT,
  meta JSON NULL,             -- dowolne dane eventu (from_status, to_status, template_id...)
  created_at, updated_at
  ```

- Model `Praust\App\Models\PraustNote` (MorphTo `notable`, BelongsTo `user`).

- Trait `Praust\App\Models\Concerns\PraustNotes`:
  - relacja `notes()`.
  - metody `addManualNote(string $body, ?User $user = null)`, `logEvent(string $event, string $body, array $meta = [])`.

- Obserwator abstrakcyjny `Praust\App\Observers\PraustNotableObserver`:
  - na `updated` – jeżeli zmieniło się pole z listy trackowanych (`$trackable` w modelu), wywołaj `logEvent('field.changed', ...)`.
  - domyślnie nieaktywny – konsument rejestruje go w `AppServiceProvider`.

- Widok partial `resources/views/admin/_inc/notes.blade.php`:
  - timeline (najnowsze na górze), badge dla `auto` vs `manual`, inline edit dla własnych notatek.
  - formularz dodania (AJAX POST do `praust.notes.store`).

- Route pakietu `praust.notes.{store,update,destroy}` (middleware admin).

- Policy `PraustNotePolicy` – edycja tylko przez autora lub uprawnienie `notes.manage-any`.

### Testy
- Feature: dodanie notatki ręcznej, edycja, usunięcie.
- Unit: `logEvent()` zapisuje rekord z `type=auto`.
- Feature: obserwator loguje zmianę pola.

---

## Etap B3 – Szablony e-maili + historia komunikacji

### Cel
Moduł admina „Szablony e-maili" – CRUD szablonów (name, subject, body HTML, category). Serwis `PraustMailer` wysyła e-mail na podstawie szablonu, zapisując historię w tabeli `praust_sent_emails` powiązanej morficznie z modelem źródłowym.

### Zmiany

- Migracja `xxxx_create_praust_email_templates_table.php`:
  ```
  id, name, slug (unique), category (nullable, string),
  subject, body LONGTEXT (HTML/Blade-like z placeholderami {{name}}, {{address}}...),
  is_active BOOL default 1, created_at, updated_at
  ```

- Migracja `xxxx_create_praust_sent_emails_table.php`:
  ```
  id, sendable_type, sendable_id,
  template_id (nullable FK),
  to_email, to_name (nullable),
  subject, body LONGTEXT,
  sent_by (user_id nullable), sent_at,
  status ENUM('sent','failed') default 'sent',
  error TEXT NULL
  ```

- Model `Praust\App\Models\PraustEmailTemplate` – używa field builderów (`TextName`, `Select`, `Tinymce`).

- Model `Praust\App\Models\PraustSentEmail` (MorphTo `sendable`).

- Kontroler `Praust\App\Http\Controllers\Admin\EmailTemplatesController` – rozszerza `PraustActionController` (standard CRUD).

- Trait `Praust\App\Models\Concerns\PraustSendable`:
  - relacja `sentEmails(): morphMany`.
  - metoda `sendTemplate(string $slug, string $to, array $variables = [], ?User $sentBy = null)`.

- Serwis `Praust\App\Services\TemplateRenderer`:
  - prosty engine zastępujący `{{key}}` wartościami ze `$variables`.
  - zabezpieczenie przed injection (escape domyślnie, `{{{raw}}}` dla surowego HTML).

- Serwis `Praust\App\Services\PraustMailer`:
  - `send(PraustEmailTemplate $tpl, string $to, array $vars, Model $sendable, ?User $user): PraustSentEmail`.
  - korzysta z `Mail::html(...)->send(...)` na istniejącej konfiguracji `MAIL_*` z `.env` projektu.
  - zapis do `praust_sent_emails` po wysyłce, łapanie wyjątku → `status=failed`.
  - jeżeli model ma trait `PraustNotes` – automatyczny `logEvent('email.sent', ...)`.

- UI – partial `resources/views/admin/_inc/send-email.blade.php`:
  - dropdown szablonów (filtrowalny po `category` – konsument przekazuje kategorię jako parametr partiala),
  - preview i edytor treści (Tinymce) przed wysłaniem,
  - lista historii wysyłek (subject + data + status).

- Route pakietu:
  - zasób `admin/email-templates` (CRUD).
  - `POST admin/send-email/{model_type}/{id}` – wysyłka.

### Konfiguracja
`config/praust.php`:
```php
'emails' => [
    'template_categories' => [],  // nadpisywane przez konsumenta
    'default_from' => env('MAIL_FROM_ADDRESS'),
],
```

### Testy
- Feature: CRUD szablonów.
- Feature: wysyłka e-maila (mock `Mail::fake()`), weryfikacja wpisu w `praust_sent_emails`.
- Unit: `TemplateRenderer::render()` dla różnych placeholderów.

---

## Wersjonowanie i release

- Wersja paczki: `0.X.0` (SemVer, minor – nowe features, brak breaking changes).
- Changelog: `CHANGELOG.md` z sekcjami „Added / Changed / Fixed".
- Tag git: `v0.X.0`.
- Po publikacji: bump w `profesjonalny-montaz-crm/composer.json`:
  ```
  "kamilfityka/profesjonalny-montaz-crm-base": "^0.X"
  ```
  i `composer update kamilfityka/profesjonalny-montaz-crm-base`.

---

## Kolejność pracy

1. **B1 – inline preview** (najmniejszy, czysto UI, niskie ryzyko).
2. **B2 – notatki + log zdarzeń** (fundament pod B3 i pod logikę CRM-ową).
3. **B3 – szablony e-maili + historia** (zależy od B2 dla auto-logu „email.sent").

Każdy etap = osobny PR do `profesjonalny-montaz-crm-base`, osobny tag, osobny bump w CRM.

---

## Kryterium „done"

- Wszystkie testy pakietu zielone (`vendor/bin/phpunit` w repo base).
- Paczka opublikowana pod tagiem, zainstalowana w CRM lokalnie, nie łamie istniejących widoków (spot-check: lista klientów, lista reklamacji, kalendarz, sprzedaż).
- Migracje paczki przechodzą na świeżej bazie CRM (`php artisan migrate:fresh --seed`).
