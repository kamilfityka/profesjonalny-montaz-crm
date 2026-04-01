<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\ClientCategory;
use Faker\Factory;
use Illuminate\Database\Seeder;

class ClientCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        (new ClientCategory())->newQuery()->truncate();

        $this->command->info('Creating Client Categories ...');

        foreach(['Klienci', 'Faktury', 'Oferty'] as $name) {
            (new ClientCategory())->newQuery()->insert(['name' => $name]);
        }

        $this->command->info('');
    }
}
