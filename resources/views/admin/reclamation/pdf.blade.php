@php
    /** @var \App\Models\Reclamation $data */
@endphp
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <style>
        @font-face {
            font-family: Maisonneue;
            src: url("{{url()->to('theme/fonts/Maisonneue/MaisonNeue-Book.ttf')}}") format("truetype");
            font-weight: 400;
            font-style: normal
        }
        @font-face {
            font-family: Maisonneue;
            src: url("{{url()->to('theme/fonts/Maisonneue/MaisonNeue-Bold.ttf')}}") format("truetype");
            font-weight: 700;
            font-style: normal
        }
        @font-face {
            font-family: Maisonneue;
            src: url("{{url()->to('theme/fonts/Maisonneue/MaisonNeue-Medium.ttf')}}") format("truetype");
            font-weight: 500;
            font-style: normal
        }
        @page {
            margin: 30px 30px 30px 30px;
        }
        * { margin: 0; padding: 0; }
        body {
            font-family: 'Maisonneue', sans-serif;
            font-size: 11px;
            padding: 20px;
            line-height: 1.5;
        }
        h1 { font-size: 18px; text-align: center; margin-bottom: 20px; text-transform: uppercase; }
        h2 { font-size: 13px; margin: 15px 0 8px; border-bottom: 1px solid #333; padding-bottom: 3px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        table.info td { padding: 4px 8px; vertical-align: top; }
        table.info td:first-child { font-weight: 700; width: 40%; }
        .section { margin-bottom: 15px; }
        .note-item { padding: 5px 0; border-bottom: 1px solid #eee; }
        .note-date { font-size: 9px; color: #666; }
        .note-type { font-weight: 700; font-size: 10px; }
        .signatures { margin-top: 60px; }
        .signatures table td { text-align: center; padding-top: 50px; border-top: 1px solid #333; width: 45%; }
        .signatures table td:nth-child(2) { width: 10%; border: none; }
    </style>
</head>
<body>
    <h1>Protokół serwisowy</h1>

    <h2>Dane zgłoszenia</h2>
    <table class="info">
        <tr><td>Nr sprawy:</td><td>{{ $data->getKey() }}</td></tr>
        <tr><td>Data zgłoszenia:</td><td>{{ $data->created_at?->format('d-m-Y H:i') }}</td></tr>
        <tr><td>Imię i nazwisko:</td><td>{{ $data->name }}</td></tr>
        <tr><td>Telefon:</td><td>{{ $data->phone }}</td></tr>
        <tr><td>Adres:</td><td>{{ $data->address }}</td></tr>
        <tr><td>Priorytet:</td><td>{{ $data->priority }}</td></tr>
        <tr><td>Pilność:</td><td>{{ $data->urgency ?? 'Niepilne' }}</td></tr>
    </table>

    <h2>Dane klienta</h2>
    <table class="info">
        @if($data->client)
            <tr><td>Klient:</td><td>{{ $data->client->name }}</td></tr>
            <tr><td>Firma:</td><td>{{ $data->client->company_name ?? '-' }}</td></tr>
            <tr><td>Telefon:</td><td>{{ $data->client->phone }}</td></tr>
            <tr><td>E-mail:</td><td>{{ $data->client->email ?? '-' }}</td></tr>
        @else
            <tr><td colspan="2">Brak przypisanego klienta</td></tr>
        @endif
    </table>

    <h2>Szczegóły reklamacji</h2>
    <table class="info">
        <tr><td>Gwarancja:</td><td>{{ $data->warranty ? 'Tak' : 'Nie' }}</td></tr>
        <tr><td>Data zakupu:</td><td>{{ $data->purchase_date ? \Carbon\Carbon::parse($data->purchase_date)->format('d-m-Y') : '-' }}</td></tr>
        <tr><td>Kategoria usterki:</td><td>{{ $data->fault_category ?? '-' }}</td></tr>
        <tr><td>Przyczyna:</td><td>{{ $data->type?->name ?? '-' }}</td></tr>
        @if($data->warranty_expired)
            <tr><td>Status gwarancji:</td><td style="color: red;">Termin minął {{ $data->warranty_days_overdue }} dni temu</td></tr>
        @endif
    </table>

    <h2>Opis wady</h2>
    <div class="section">{!! $data->text !!}</div>

    @if($data->fault_description)
        <h2>Szczegółowy opis usterki</h2>
        <div class="section">{!! $data->fault_description !!}</div>
    @endif

    @if($data->notes && $data->notes->count())
        <h2>Historia notatek</h2>
        @foreach($data->notes as $note)
            <div class="note-item">
                <span class="note-type">{{ $note->getTypeLabel() }}</span>
                <span class="note-date">{{ $note->created_at->format('d-m-Y H:i') }}
                    @if($note->user) | {{ $note->user->name ?? $note->user->email }} @endif
                </span>
                <br>{{ $note->content }}
            </div>
        @endforeach
    @endif

    <div class="signatures">
        <table>
            <tr>
                <td>Podpis serwisanta</td>
                <td></td>
                <td>Podpis klienta</td>
            </tr>
        </table>
    </div>
</body>
</html>
