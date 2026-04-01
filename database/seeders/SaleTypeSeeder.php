<?php

namespace Database\Seeders;

use App\Models\SaleType;
use Illuminate\Database\Seeder;

class SaleTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        (new SaleType())->newQuery()->truncate();

        $this->command->info('Creating Sale Types ...');

        foreach(['Usługa', 'Sprzedaż produktu', 'Kontrakt', 'Inna'] as $name) {
            (new SaleType())->newQuery()->create(['name' => $name]);
        }

        $this->command->info('');
    }
}
