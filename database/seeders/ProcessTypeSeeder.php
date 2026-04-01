<?php

namespace Database\Seeders;

use App\Models\ProcessType;
use Illuminate\Database\Seeder;

class ProcessTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        (new ProcessType())->newQuery()->truncate();

        $this->command->info('Creating Process Types ...');

        foreach(['Reklamacje w trakcie realizacji', 'Sprzedaż zamówionych towarów', 'Realizacja zamówień'] as $name) {
            (new ProcessType())->newQuery()->create(['name' => $name]);
        }

        $this->command->info('');
    }
}
