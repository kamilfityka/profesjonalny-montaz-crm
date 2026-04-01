<?php

namespace Database\Seeders;

use App\Models\Document;
use App\Models\Enums\DocumentFormat;
use App\Models\User;
use Faker\Factory;
use Illuminate\Database\Seeder;

class DocumentSeeder extends Seeder
{
    private int $numberOfDocuments = 50;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $faker = Factory::create('pl_PL');

        (new Document())->newQuery()->truncate();

        $this->command->info('Creating Documents ...');
        $bar = $this->command->getOutput()->createProgressBar($this->numberOfDocuments);

        for ($i = 0; $i <= $this->numberOfDocuments; $i++) {
            $model = new Document();
            $model->active = true;
            $model->client()->associate((new User())->newQuery()->inRandomOrder()->first());
            $model->name = 'Dokument #'.($i+1);
            $model->format = $faker->randomKey(DocumentFormat::array());
            $model->save();

            $bar->advance();
        }

        $bar->finish();
        $this->command->info('');
    }
}
