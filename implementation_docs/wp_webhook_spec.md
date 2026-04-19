# Webhook formularza serwisowego WordPress

## Endpoint

`POST /api/reclamations/wp-webhook`

## Autoryzacja

Nagłówek `X-WP-Webhook-Token` z wartością równą `WP_WEBHOOK_TOKEN` z `.env` CRM-a.

Brak nagłówka lub niezgodność → `401 Unauthorized`.
Brak konfiguracji tokenu po stronie CRM-a → `503 Service Unavailable`.

## Payload (multipart/form-data)

| Pole | Typ | Wymagane | Uwagi |
|------|-----|----------|-------|
| `name` | string (max 255) | tak | Imię i nazwisko zgłaszającego |
| `phone` | string (max 64) | tak | Telefon kontaktowy |
| `email` | string (email, max 255) | nie | Używany do powiązania z istniejącym `Client` |
| `address` | string (max 500) | tak | Adres budowy |
| `purchase_date` | date (`YYYY-MM-DD`) | nie | Data zakupu produktu |
| `fault_description` | string | tak | Opis usterki |
| `urgency` | enum `pilne` \| `niepilne` | nie | Domyślnie `niepilne` |
| `attachments[]` | plik (jpg/jpeg/png/mp4/mov/pdf, max 20 MB, max 20 plików) | nie | Załączniki do sprawy |

## Odpowiedź

- `201 Created` z body `{"id": <reclamation_id>, "status": "created"}`.
- `422 Unprocessable Entity` przy błędach walidacji (standard Laravel).

## Konfiguracja po stronie CRM (`.env`)

```
WP_WEBHOOK_TOKEN=<wygeneruj losowy 32+ znakowy ciąg>
WP_WEBHOOK_PENDING_CATEGORY_ID=<id kategorii „Oczekuje na weryfikację" z reclamation_categories>
```

## Przykład curl

```bash
curl -X POST https://crm.example.com/api/reclamations/wp-webhook \
  -H "X-WP-Webhook-Token: <token>" \
  -F "name=Jan Kowalski" \
  -F "phone=+48123456789" \
  -F "email=jan@example.com" \
  -F "address=ul. Przykładowa 1, Warszawa" \
  -F "purchase_date=2024-08-15" \
  -F "fault_description=Brama nie domyka się od strony zawiasów." \
  -F "urgency=pilne" \
  -F "attachments[]=@./zdjecie1.jpg" \
  -F "attachments[]=@./video1.mp4"
```

## Co tworzy CRM po przyjęciu zgłoszenia

1. Rekord w `reclamations` ze statusem (kategorią) z `WP_WEBHOOK_PENDING_CATEGORY_ID`, `source = 'wp_form'`.
2. Powiązanie z istniejącym klientem po `email` lub `phone`, jeżeli znajdzie dopasowanie.
3. Zapis załączników w `reclamation_attachments` + storage.
4. Wpis do logu (`storage/logs/laravel.log`, event `reclamation.wp_registered`).
   Po wdrożeniu B2 (system notatek w base) zostanie zastąpiony zapisem do `praust_notes`.
