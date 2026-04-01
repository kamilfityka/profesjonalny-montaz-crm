<?php

namespace Database\Seeders;

use App\Models\ReclamationType;
use Illuminate\Database\Seeder;

class ReclamationTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        (new ReclamationType())->newQuery()->truncate();

        $this->command->info('Creating Reclamation Types ...');

        foreach(['Uwagi do oferty', 'Podziękowanie', 'Wniosek o umówienie terminu', 'Zgłoszenie reklamacji produktu', 'Zgłoszenie reklamacji usługi', 'Zapytanie o sposób rozwiązania', 'Uwagi w sprawie działania produktu', 'Problem do rozwiązania', 'Zwrot'] as $name) {
            (new ReclamationType())->newQuery()->create(['name' => $name]);
        }

        $this->command->info('');
    }
}
