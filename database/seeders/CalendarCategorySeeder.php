<?php

namespace Database\Seeders;

use App\Models\CalendarCategory;
use Illuminate\Database\Seeder;

class CalendarCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        (new CalendarCategory())->newQuery()->truncate();

        $this->command->info('Creating Calendar Types ...');

        foreach(['Kontakt z klientem', 'Negocjacje', 'Potwierdzić przyjęcie zamówienia', 'Ustalić datę montażu', 'Wizytka u klienta', 'Zamówić Towar'] as $name) {
            (new CalendarCategory())->newQuery()->insert(['name' => $name]);
        }

        $this->command->info('');
    }
}
