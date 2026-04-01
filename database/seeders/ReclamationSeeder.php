<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Enums\Priority;
use App\Models\Reclamation;
use App\Models\ReclamationCategory;
use App\Models\ReclamationType;
use App\Models\User;
use Faker\Factory;
use Illuminate\Database\Seeder;

class ReclamationSeeder extends Seeder
{
    private int $numberOfReclamations = 50;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $faker = Factory::create('pl_PL');

        (new Reclamation())->newQuery()->truncate();

        $this->command->info('Creating Reclamations ...');
        $bar = $this->command->getOutput()->createProgressBar($this->numberOfReclamations);

        for ($i = 0; $i <= $this->numberOfReclamations; $i++) {
            $model = new Reclamation();
            $model->active = true;
            $model->category()->associate((new ReclamationCategory())->newQuery()->inRandomOrder()->first());
            $model->client()->associate((new Client())->newQuery()->inRandomOrder()->first());
            $model->user()->associate((new User())->newQuery()->inRandomOrder()->first());
            $model->name = 'Reklamacja #'.($i+1);
            $model->type()->associate((new ReclamationType())->newQuery()->inRandomOrder()->first());
            $model->closed_at = $faker->dateTimeBetween('now', '+1 month');
            $model->priority = $faker->randomKey(Priority::array());
            $model->text = $faker->paragraphs(asText: true);

            $model->save();

            $bar->advance();
        }

        $bar->finish();
        $this->command->info('');
    }
}
