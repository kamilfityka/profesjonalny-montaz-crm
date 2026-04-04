@extends('praust::admin.layout')

@section('content-header')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Pomoc - Instrukcja obsługi</h4>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Spis treści</h4>
                    <ul>
                        <li><a href="#rozszerzona-karta">Rozszerzona karta sprawy</a></li>
                        <li><a href="#pilnosc">Pole pilności</a></li>
                        <li><a href="#notatki">System notatek</a></li>
                        <li><a href="#zalaczniki">Podgląd załączników</a></li>
                        <li><a href="#gwarancja">Logika 18 miesięcy gwarancji</a></li>
                        <li><a href="#emailing">Szablony e-maili</a></li>
                        <li><a href="#wordpress">Integracja z WordPress</a></li>
                        <li><a href="#pdf">Generowanie protokołu PDF</a></li>
                    </ul>
                </div>
            </div>

            {{-- Feature 3 --}}
            <div class="card" id="rozszerzona-karta">
                <div class="card-body">
                    <h4 class="header-title"><i class="mdi mdi-card-text-outline me-1"></i> Rozszerzona karta sprawy</h4>
                    <p>Formularz edycji zgłoszenia zawiera teraz dodatkowe pola:</p>
                    <ul>
                        <li><strong>Objęty gwarancją</strong> - checkbox Tak/Nie</li>
                        <li><strong>Data zakupu</strong> - data zakupu produktu/usługi</li>
                        <li><strong>Szczegółowy opis usterki</strong> - edytor tekstu do opisu usterki</li>
                        <li><strong>Kategoria usterki</strong> - wybór z listy: Wada produktu, Nieprawidłowy montaż, Niewłaściwe użytkowanie, Brak zrozumienia jednej ze stron, Nie można ocenić odpowiedzialności</li>
                    </ul>
                    <p>Dowody zakupu można dodać jako załączniki do zgłoszenia (standardowy system załączników).</p>
                </div>
            </div>

            {{-- Feature 2 --}}
            <div class="card" id="pilnosc">
                <div class="card-body">
                    <h4 class="header-title"><i class="mdi mdi-alert-outline me-1"></i> Pole pilności</h4>
                    <p>Każde zgłoszenie może być oznaczone jako:</p>
                    <ul>
                        <li><strong>Pilne</strong> - wyróżnione na tablicy Kanban czerwoną ramką i znacznikiem "PILNE"</li>
                        <li><strong>Niepilne</strong> (domyślne) - standardowy wygląd</li>
                    </ul>
                    <p>Pilność widoczna jest również w eksporcie Excel.</p>
                </div>
            </div>

            {{-- Feature 6 --}}
            <div class="card" id="notatki">
                <div class="card-body">
                    <h4 class="header-title"><i class="mdi mdi-message-text-outline me-1"></i> System notatek</h4>
                    <p>Pod formularzem edycji zgłoszenia znajduje się sekcja notatek:</p>
                    <ol>
                        <li><strong>Dodawanie notatek</strong> - wpisz treść notatki i kliknij "Dodaj notatkę"</li>
                        <li><strong>Automatyczne wpisy</strong> - system automatycznie rejestruje zdarzenia:
                            <ul>
                                <li>Utworzenie zgłoszenia</li>
                                <li>Aktualizacja zgłoszenia</li>
                                <li>Wysłanie e-maila</li>
                            </ul>
                        </li>
                    </ol>
                    <p>Notatki wyświetlane są chronologicznie (najnowsze na górze) z datą, typem i autorem.</p>
                </div>
            </div>

            {{-- Feature 5 --}}
            <div class="card" id="zalaczniki">
                <div class="card-body">
                    <h4 class="header-title"><i class="mdi mdi-attachment me-1"></i> Podgląd załączników</h4>
                    <p>Załączniki dodane do zgłoszenia wyświetlane są z podglądem:</p>
                    <ul>
                        <li><strong>Zdjęcia</strong> (JPG, PNG, GIF, WebP) - miniaturka z możliwością powiększenia (kliknij aby otworzyć lightbox)</li>
                        <li><strong>Wideo</strong> (MP4, WebM) - odtwarzacz wideo</li>
                        <li><strong>Inne pliki</strong> - ikona z przyciskiem do pobrania</li>
                    </ul>
                </div>
            </div>

            {{-- Feature 4 --}}
            <div class="card" id="gwarancja">
                <div class="card-body">
                    <h4 class="header-title"><i class="mdi mdi-calendar-clock me-1"></i> Logika 18 miesięcy gwarancji</h4>
                    <p>System automatycznie sprawdza, czy od daty zakupu minęło 18 miesięcy:</p>
                    <ul>
                        <li><strong>Gwarancja aktywna</strong> - zielony alert z informacją ile dni pozostało</li>
                        <li><strong>Gwarancja wygasła</strong> - czerwony alert z informacją ile dni po terminie, z przyciskiem do wysłania e-maila o odrzuceniu</li>
                    </ul>
                    <p>Alert wyświetla się automatycznie na górze formularza edycji, gdy wypełniona jest data zakupu.</p>
                </div>
            </div>

            {{-- Feature 7 --}}
            <div class="card" id="emailing">
                <div class="card-body">
                    <h4 class="header-title"><i class="mdi mdi-email-outline me-1"></i> Szablony e-maili</h4>
                    <p>System szablonów e-maili umożliwia szybkie wysyłanie standardowych wiadomości:</p>
                    <ol>
                        <li>Przejdź do edycji zgłoszenia</li>
                        <li>W sekcji "Komunikacja e-mail" kliknij "Wyślij e-mail"</li>
                        <li>Wybierz szablon z listy - podgląd wyświetli się automatycznie</li>
                        <li>Wpisz adres e-mail i kliknij "Wyślij"</li>
                    </ol>
                    <p><strong>Dostępne szablony:</strong> Przyjęcie zgłoszenia, Odrzucenie, Prośba o dokumenty, Wizyta serwisanta, Informacja ogólna.</p>
                    <p><strong>Zarządzanie szablonami:</strong> W menu Ustawienia > Szablony e-maili możesz edytować istniejące lub dodać nowe szablony. Dostępne placeholdery: {client_name}, {case_number}, {address}, {phone}, {purchase_date}.</p>
                    <p>Historia wysłanych e-maili jest widoczna w sekcji "Komunikacja e-mail" oraz w notatkach.</p>
                </div>
            </div>

            {{-- Feature 1 --}}
            <div class="card" id="wordpress">
                <div class="card-body">
                    <h4 class="header-title"><i class="mdi mdi-wordpress me-1"></i> Integracja z WordPress</h4>
                    <p>Formularz na stronie WordPress może automatycznie tworzyć zgłoszenia w CRM.</p>
                    <p><strong>Konfiguracja:</strong></p>
                    <ol>
                        <li>Ustaw zmienną <code>WORDPRESS_API_TOKEN</code> w pliku <code>.env</code></li>
                        <li>Skonfiguruj formularz WordPress, aby wysyłał dane pod adres: <code>POST /api/reclamation</code></li>
                        <li>Dodaj nagłówek <code>Authorization: Bearer {token}</code></li>
                    </ol>
                    <p><strong>Pola formularza:</strong> name (wymagane), phone (wymagane), email, address, text (wymagane), purchase_date</p>
                    <p>Nowe zgłoszenia trafiają do kategorii "Oczekuje na weryfikację".</p>
                </div>
            </div>

            {{-- Feature 8 --}}
            <div class="card" id="pdf">
                <div class="card-body">
                    <h4 class="header-title"><i class="mdi mdi-file-pdf-box me-1"></i> Generowanie protokołu PDF</h4>
                    <p>Na stronie edycji zgłoszenia dostępny jest przycisk "Protokół PDF" (obok tytułu sprawy).</p>
                    <p><strong>Protokół zawiera:</strong></p>
                    <ul>
                        <li>Dane zgłoszenia (numer, data, kontakt, adres)</li>
                        <li>Dane klienta</li>
                        <li>Szczegóły reklamacji (gwarancja, data zakupu, kategoria usterki)</li>
                        <li>Opis wady i szczegółowy opis usterki</li>
                        <li>Historię notatek</li>
                        <li>Miejsce na podpisy (serwisanta i klienta)</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
