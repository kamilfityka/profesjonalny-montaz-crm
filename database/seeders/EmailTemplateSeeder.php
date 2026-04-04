<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    public function run(): void
    {
        (new EmailTemplate())->newQuery()->truncate();

        $templates = [
            [
                'name' => 'Przyjęcie zgłoszenia',
                'subject' => 'Potwierdzenie przyjęcia zgłoszenia nr {case_number}',
                'body' => '<p>Szanowny/a {client_name},</p><p>Potwierdzamy przyjęcie Państwa zgłoszenia nr <strong>{case_number}</strong> dotyczącego adresu: {address}.</p><p>Skontaktujemy się z Państwem w celu ustalenia dalszych kroków.</p><p>Z poważaniem,<br>Zespół serwisowy</p>',
            ],
            [
                'name' => 'Odrzucenie zgłoszenia (po 18 miesiącach)',
                'subject' => 'Informacja dotycząca zgłoszenia nr {case_number}',
                'body' => '<p>Szanowny/a {client_name},</p><p>W nawiązaniu do zgłoszenia nr <strong>{case_number}</strong> informujemy, że po weryfikacji ustaliliśmy, iż okres bezpłatnej regulacji (18 miesięcy od daty zakupu: {purchase_date}) upłynął.</p><p>W związku z powyższym, dalsza obsługa będzie realizowana odpłatnie. Prosimy o kontakt w celu ustalenia kosztorysu.</p><p>Z poważaniem,<br>Zespół serwisowy</p>',
            ],
            [
                'name' => 'Prośba o dokumenty',
                'subject' => 'Prośba o dodatkowe dokumenty - zgłoszenie nr {case_number}',
                'body' => '<p>Szanowny/a {client_name},</p><p>W celu dalszego procedowania zgłoszenia nr <strong>{case_number}</strong>, prosimy o przesłanie następujących dokumentów:</p><ul><li>Dowód zakupu (faktura/paragon)</li><li>Zdjęcia usterki</li></ul><p>Dokumenty prosimy przesłać na ten adres e-mail.</p><p>Z poważaniem,<br>Zespół serwisowy</p>',
            ],
            [
                'name' => 'Wizyta serwisanta',
                'subject' => 'Umówienie wizyty serwisowej - zgłoszenie nr {case_number}',
                'body' => '<p>Szanowny/a {client_name},</p><p>W nawiązaniu do zgłoszenia nr <strong>{case_number}</strong> informujemy, że wizyta serwisowa została zaplanowana.</p><p>Adres: {address}<br>Telefon kontaktowy: {phone}</p><p>Prosimy o potwierdzenie dostępności w ustalonym terminie.</p><p>Z poważaniem,<br>Zespół serwisowy</p>',
            ],
            [
                'name' => 'Informacja ogólna',
                'subject' => 'Informacja dotycząca zgłoszenia nr {case_number}',
                'body' => '<p>Szanowny/a {client_name},</p><p>W nawiązaniu do zgłoszenia nr <strong>{case_number}</strong>, przekazujemy poniższą informację:</p><p>[Treść wiadomości]</p><p>Z poważaniem,<br>Zespół serwisowy</p>',
            ],
        ];

        foreach ($templates as $template) {
            $model = new EmailTemplate();
            $model->active = true;
            $model->name = $template['name'];
            $model->subject = $template['subject'];
            $model->body = $template['body'];
            $model->save();
        }
    }
}
