# Plan wdrożenia – `profesjonalny-montaz-crm`

> Repozytorium: `kamilfityka/profesjonalny-montaz-crm`
> Branch roboczy: `claude/wordpress-crm-integration-8eOZN`
> Zależność: wymaga wersji `kamilfityka/profesjonalny-montaz-crm-base: ^0.X` z etapami B1/B2/B3 (patrz `praust_base_implementation.md`).

---

## Zakres

Funkcje domenowe branży montażowej, które trafiają tylko do CRM-a:

1. Integracja formularza WP → sprawa ze statusem „Oczekuje na weryfikację".
2. Wizualne wyróżnienie pilności na liście spraw (pole `priority` już istnieje w `Reclamation` + enum `Priority`).
3. Rozszerzenie karty sprawy: dowód zakupu, gwarancja, data zakupu, opis usterki, kategoria odpowiedzialności.
4. Logika 18 miesięcy – komunikat + zatwierdzenie e-maila odrzucenia.
5. (B1) Inline preview załączników – tylko konsumpcja z base.
6. (B2) Notatki + auto-log – konfiguracja trackowanych pól i triggerów domenowych.
7. (B3) Szablony e-maili – seed 5 szablonów + UI w karcie sprawy.
8. PDF – protokół serwisanta.

---

## Etap 1 – Fundament danych

### Migracja `xxxx_extend_reclamations_table.php`
- `purchase_date` DATE NULL
- `warranty` BOOLEAN DEFAULT 0 (objęty/poza)
- `fault_description` TEXT NULL (opis usterki – osobno od istniejącego `text`, lub zastąpienie `text` – do decyzji)
- `responsibility_category` VARCHAR(64) NULL (enum domenowy, string w DB)
- `source` VARCHAR(32) DEFAULT 'manual' (`manual`|`wp_form`)

### Enum `app/Models/Enums/ResponsibilityCategory.php`
Wzór: istniejący `app/Models/Enums/Priority.php`.
Wartości (string backed):
- `product_defect` – „Wada produktu"
- `incorrect_installation` – „Nieprawidłowy montaż"
- `misuse` – „Niewłaściwe użytkowanie"
- `mutual_misunderstanding` – „Brak zrozumienia jednej ze stron"
- `undetermined` – „Nie można ocenić odpowiedzialności"

### Aktualizacja `app/Models/Reclamation.php`
Dodać pola w `fields()`:
- `YesNo::make('warranty')->label('Gwarancja')` (wzór: `Sale.php`)
- `DateTime::make('purchase_date')->label('Data zakupu')`
- `Tinymce::make('fault_description')->label('Opis usterki')`
- `Radio::make('responsibility_category')->label('Kategoria odpowiedzialności')->options(ResponsibilityCategory::array())`

Dowód zakupu = załącznik – obsługa przez istniejący `PraustAttachments` (nic nowego na DB).

### Factory `database/factories/ReclamationFactory.php`
Rozszerzyć o nowe pola (losowe wartości) – wymagane dla testów.

### Testy
`tests/Unit/Models/ReclamationTest.php`:
- zapis nowych pól i odczyt.
- rzutowanie `warranty` na `bool`, `purchase_date` na `Carbon`.

---

## Etap 2 – Wizualne wyróżnienie pilności

Pole `priority` **istnieje** w modelu (enum `Priority`). Brakuje tylko prezentacji.

### Zmiany
- Override widoku listy Praust dla modułu reklamacji (`resources/views/vendor/praust/...` – zależnie od tego jak pakiet eksponuje override).
- Dodać klasę CSS wiersza w zależności od wartości `priority` (np. `row-urgent`, `row-normal`).
- `resources/css/app.css` (lub `resources/sass/app.scss` – sprawdzić po `webpack.mix.js`): style dla `.row-urgent` (np. pomarańczowe tło, wytłuszczenie).
- Badge „PILNE" obok tytułu na karcie i w tabeli.
- Filtr listy po pilności (checkbox w search).

### Testy
- Feature test: lista reklamacji zwraca klasę CSS dla rekordu z `priority=pilne`.

---

## Etap 3 – Integracja formularza WordPress

### Route `routes/api.php`
```php
Route::post('/reclamations/wp-webhook', [WpReclamationWebhookController::class, '__invoke'])
    ->middleware('wp.webhook.token');
```

### Middleware `app/Http/Middleware/VerifyWpWebhookToken.php`
- Porównanie nagłówka `X-WP-Webhook-Token` z `config('services.wp_webhook.token')` (bind `.env` → `WP_WEBHOOK_TOKEN`).
- Odrzucenie 401 przy niezgodności.

### FormRequest `app/Http/Requests/StoreReclamationFromWpRequest.php`
Walidacja:
- `name`, `phone`, `address`, `email` – wymagane.
- `purchase_date` – date, nullable.
- `fault_description` – string, wymagane.
- `urgency` – `in:pilne,niepilne`.
- `attachments.*` – `file|mimes:jpg,jpeg,png,mp4,mov,pdf|max:20480`.

### Action `app/Actions/CreateReclamationFromWpForm.php`
Jedna odpowiedzialność: utworzyć sprawę + załączniki + log.
- Znalezienie/utworzenie `Client` po e-mailu/telefonie.
- Zapis `Reclamation` ze statusem „Oczekuje na weryfikację" (category_id = id tej kategorii; jeżeli nie ma – seed w etapie 0).
- `source = 'wp_form'`.
- Upload załączników przez `PraustAttachments`.
- `logEvent('reclamation.wp_registered', 'Zgłoszenie z formularza WWW', ['ip' => $request->ip()])` (wymaga B2).

### Kontroler `app/Http/Controllers/Api/WpReclamationWebhookController.php`
Cienki – wywołuje Action, zwraca `201` z `id` sprawy.

### Konfiguracja
`config/services.php`:
```php
'wp_webhook' => ['token' => env('WP_WEBHOOK_TOKEN')],
```

### Seed
`ReclamationCategorySeeder` – upewnić się że kategoria „Oczekuje na weryfikację" istnieje.

### Testy
`tests/Feature/WpWebhookTest.php`:
- 401 bez tokenu.
- 422 przy niepoprawnym payloadzie.
- 201 + rekord w DB + załączniki zapisane + wpis w `praust_notes` (auto).

### Dokumentacja dla strony WP
`implementation_docs/wp_webhook_spec.md` – kontrakt JSON, nagłówki, przykład `curl`.

---

## Etap 4 – Logika 18 miesięcy

### Action `app/Actions/CheckWarrantyDeadline.php`
```php
public function __invoke(Reclamation $r): WarrantyStatusDto
```
- Jeżeli `purchase_date` = null → status `unknown`.
- Dni od zakupu = `now()->diffInDays($r->purchase_date)`.
- Jeżeli `dni > 18*30` (lub dokładniej `purchase_date->addMonths(18) < now()`) → status `expired`, `overdueDays = diff`.
- W przeciwnym razie → `active`, `remainingDays`.

### DTO `app/Actions/Dto/WarrantyStatusDto.php`
Readonly: `status`, `overdueDays`, `remainingDays`.

### Widok karty sprawy
- Alert w karcie: „Termin bezpłatnej regulacji minął X dni temu" dla `expired`.
- Przycisk „Przygotuj e-mail odrzucenia" – otwiera modal z prefillowanym szablonem `rejection_warranty_expired` (patrz etap 7), edycja i wysyłka.

### Testy
- Unit: `CheckWarrantyDeadline` – przypadki brzegowe (dokładnie 18 miesięcy, 18 miesięcy + 1 dzień, brak daty).
- Feature: po wysyłce e-maila odrzucenia – wpis w historii i w notatkach (auto-log).

---

## Etap 5 – Inline preview załączników

Konsumpcja B1 z base. Zmiany w CRM:
- Upewnić się że widok karty sprawy używa partiala z base (`@include('praust::admin._inc.attachments', ['model' => $reclamation])`).
- Override CSS jeśli potrzeba (np. max-width wideo na karcie sprawy).

Brak własnej logiki.

---

## Etap 6 – Notatki + log zdarzeń (triggery domenowe)

Konsumpcja B2 z base. Zmiany w CRM:

### Model `Reclamation`
- Dodać trait `use \Praust\App\Models\Concerns\PraustNotes;`.
- Zdefiniować `protected array $trackable = ['reclamation_category_id', 'priority', 'user_id', 'responsibility_category'];` (używane przez `PraustNotableObserver`).

### Observer rejestracja
`app/Providers/AppServiceProvider.php`:
```php
Reclamation::observe(\Praust\App\Observers\PraustNotableObserver::class);
```

### Mapowanie eventów → czytelne komunikaty
`config/reclamation-events.php`:
```php
return [
    'reclamation_category_id' => 'Zmiana statusu',
    'priority'                => 'Zmiana pilności',
    'user_id'                 => 'Zmiana przypisania',
    'responsibility_category' => 'Zmiana kategorii odpowiedzialności',
];
```
Observer w base czyta config `praust.notes.label_map` – konsument wstrzykuje to w ServiceProvider.

### UI
- Partial `@include('praust::admin._inc.notes', ['model' => $reclamation])` w karcie sprawy.

### Testy
- Feature: zmiana statusu → wpis w `praust_notes` z `type=auto`, `event='status.changed'`, `meta` zawiera `from`/`to`.
- Feature: ręczna notatka – dodanie, edycja własnej, brak edycji cudzej.

---

## Etap 7 – Szablony e-maili + historia

Konsumpcja B3 z base. Zmiany w CRM:

### Seeder `database/seeders/ReclamationEmailTemplatesSeeder.php`
Pięć szablonów (kategoria `reclamation`):
1. `reclamation_accept` – „Przyjęcie zgłoszenia"
2. `reclamation_reject` – „Odrzucenie"
3. `reclamation_request_docs` – „Prośba o dokumenty"
4. `reclamation_technician_visit` – „Wizyta serwisanta"
5. `reclamation_other` – „Inne"

Dodatkowo (pod etap 4):
6. `rejection_warranty_expired` – „Odrzucenie – termin 18 miesięcy minął"

Placeholdery: `{{client_name}}`, `{{reclamation_id}}`, `{{purchase_date}}`, `{{overdue_days}}`, `{{address}}`.

### Model `Reclamation`
- `use \Praust\App\Models\Concerns\PraustSendable;`.

### UI
- Partial `@include('praust::admin._inc.send-email', ['model' => $reclamation, 'category' => 'reclamation'])` w karcie sprawy.
- Konfiguracja `praust.emails.template_categories` w AppServiceProvider:
  ```php
  config(['praust.emails.template_categories' => ['reclamation' => 'Reklamacje']]);
  ```

### Zmienne do szablonu
Metoda `Reclamation::mailVariables(): array` – zwraca mapę placeholderów → wartości (bazując na powiązanym `Client`, `purchase_date`, itd.).

### Testy
- Feature: wysyłka szablonu z karty sprawy → wpis w `praust_sent_emails` + auto-notatka „email.sent" w `praust_notes`.
- Feature: edycja treści przed wysyłką nie nadpisuje szablonu bazowego.

---

## Etap 8 – PDF protokół serwisanta

### Widok `resources/views/admin/reclamation/pdf/protocol.blade.php`
- Nagłówek: nazwa firmy + logo (z `Configuration`).
- Sekcja „Dane zgłoszenia": nr sprawy, data utworzenia, klient (imię, telefon, adres), data zakupu, gwarancja (tak/nie), pilność.
- Sekcja „Opis usterki": `{!! $reclamation->fault_description !!}` (HTML z Tinymce – świadomie `!!`).
- Sekcja „Kategoria odpowiedzialności": etykieta z enuma.
- Sekcja „Załączniki (lista)": nazwy plików (bez inline – PDF offline).
- Stopka: miejsce na podpis klienta i serwisanta.

### Action `app/Actions/GenerateReclamationProtocolPdf.php`
```php
public function __invoke(Reclamation $r): \Symfony\Component\HttpFoundation\Response
```
- `PDF::loadView('admin.reclamation.pdf.protocol', ['reclamation' => $r])`.
- `->download("protokol-{$r->id}.pdf")`.

### Kontroler
W `ReclamationController` dodać metodę `getProtocolPdf(int $id)` wywołującą Action.

### Route
W `routes/web.php` (grupa admin): `GET /admin/reclamations/{id}/protocol.pdf`.

### UI
Przycisk „Drukuj protokół" w karcie sprawy – link do route powyżej.

### Testy
- Feature: 200 + `Content-Type: application/pdf`.
- Smoke: wygenerowany PDF ma rozmiar > 1KB (walidacja że DomPDF nie zwrócił pustego outputu).

---

## Kolejność wdrożenia (uwzględnia zależności)

1. **Etap 1** – migracja + enum + pola w modelu (fundament pod wszystko).
2. **Etap 2** – pilność na liście (niezależne, można równolegle).
3. **Etap 6** – notatki (wymaga B2 wydanego w base).
4. **Etap 5** – inline preview (wymaga B1 wydanego w base).
5. **Etap 7** – szablony e-maili (wymaga B3 i Etapu 6).
6. **Etap 4** – logika 18 miesięcy (wymaga Etapu 1 i Etapu 7).
7. **Etap 3** – integracja WP (wymaga Etapu 1 i Etapu 6 dla logu).
8. **Etap 8** – PDF protokół (wymaga Etapu 1).
9. **Finalizacja** – testy end-to-end, `npm run prod`, deploy na staging, akceptacja klienta.

---

## Gdzie **nic** nie idzie do CRM-a

Funkcjonalności przekrojowe (inline preview, system notatek, silnik szablonów e-maili) **implementujemy w base**. W CRM tylko:
- migracje domenowe,
- seed szablonów z treścią branżową,
- konfiguracja (trackable pola, kategorie szablonów, label map),
- widoki Blade używające partiali z base,
- Actions z logiką biznesową branży.

Zasada: jeżeli kiedyś będziemy chcieli użyć notatek/szablonów/preview w `Sale` lub `Calendar` – nie duplikujemy kodu, tylko dodajemy trait do modelu.

---

## Kryterium „done" (całego wdrożenia)

- `php artisan test` – zielone.
- `npm run prod` – bez błędów.
- Staging: manualny przejazd ścieżki klienta (formularz WP → karta sprawy → zmiana statusu → wysyłka e-maila → PDF).
- Akceptacja klienta na stagingu.
- Merge do `main` + tag release.
