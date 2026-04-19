@php
    /** @var \App\Models\Reclamation $reclamation */
    /** @var string|null $responsibilityLabel */
@endphp
<!doctype html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <style>
        @font-face {
            font-family: Maisonneue;
            src: url("{{ url()->to('theme/fonts/Maisonneue/MaisonNeue-Book.ttf') }}") format("truetype");
            font-weight: 400;
            font-style: normal;
        }
        @font-face {
            font-family: Maisonneue;
            src: url("{{ url()->to('theme/fonts/Maisonneue/MaisonNeue-Bold.ttf') }}") format("truetype");
            font-weight: 700;
            font-style: normal;
        }
        @page { margin: 30px; }
        body { font-family: 'Maisonneue', sans-serif; font-size: 12px; color: #222; }
        h1 { font-size: 20px; margin: 0 0 10px; }
        h2 { font-size: 14px; margin: 18px 0 8px; border-bottom: 1px solid #999; padding-bottom: 3px; }
        table.kv { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        table.kv td { padding: 4px 6px; vertical-align: top; }
        table.kv td.label { width: 35%; color: #666; }
        .signatures { margin-top: 60px; width: 100%; }
        .signatures td { width: 50%; text-align: center; padding-top: 40px; border-top: 1px solid #444; }
        .desc { border: 1px solid #ccc; padding: 8px; min-height: 60px; }
    </style>
</head>
<body>
    <h1>Protokół serwisanta</h1>
    <div>Numer sprawy: <strong>#{{ $reclamation->getKey() }}</strong></div>
    <div>Data wystawienia: {{ now()->format('d-m-Y') }}</div>

    <h2>Dane zgłoszenia</h2>
    <table class="kv">
        <tr><td class="label">Data wprowadzenia:</td><td>{{ $reclamation->created_at?->format('d-m-Y H:i') }}</td></tr>
        <tr><td class="label">Klient:</td><td>{{ $reclamation->name }}</td></tr>
        <tr><td class="label">Telefon:</td><td>{{ $reclamation->phone }}</td></tr>
        <tr><td class="label">Adres budowy:</td><td>{{ $reclamation->address }}</td></tr>
        <tr><td class="label">Data zakupu:</td><td>{{ $reclamation->purchase_date?->format('d-m-Y') ?? '—' }}</td></tr>
        <tr><td class="label">Gwarancja:</td><td>{{ $reclamation->warranty ? 'Objęty' : 'Poza' }}</td></tr>
        <tr><td class="label">Priorytet:</td><td>{{ $reclamation->priority ?? '—' }}</td></tr>
        <tr><td class="label">Kategoria odpowiedzialności:</td><td>{{ $responsibilityLabel ?? '—' }}</td></tr>
    </table>

    <h2>Opis usterki</h2>
    <div class="desc">{!! $reclamation->fault_description ?: $reclamation->text !!}</div>

    @if($reclamation->admin_attachments && $reclamation->admin_attachments->count())
        <h2>Załączniki</h2>
        <ul>
            @foreach($reclamation->admin_attachments as $attachment)
                <li>{{ $attachment->file }}</li>
            @endforeach
        </ul>
    @endif

    <table class="signatures">
        <tr>
            <td>Podpis klienta</td>
            <td>Podpis serwisanta</td>
        </tr>
    </table>
</body>
</html>
