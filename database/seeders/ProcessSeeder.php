<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Enums\Priority;
use App\Models\Process;
use App\Models\ProcessCategory;
use App\Models\ProcessType;
use App\Models\User;
use Faker\Factory;
use Illuminate\Database\Seeder;

class ProcessSeeder extends Seeder
{
    private int $numberOfProcesses = 50;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $faker = Factory::create('pl_PL');

        (new Process())->newQuery()->truncate();

        $this->command->info('Creating Processes ...');
        $bar = $this->command->getOutput()->createProgressBar($this->numberOfProcesses);

        for ($i = 0; $i <= $this->numberOfProcesses; $i++) {
            $model = new Process();
            $model->active = true;
            $model->user()->associate((new User())->newQuery()->inRandomOrder()->first());
            $model->client()->associate((new Client())->newQuery()->inRandomOrder()->first());
            $model->category()->associate((new ProcessCategory())->newQuery()->inRandomOrder()->first());
            $model->name = 'Proces #'.($i+1);
            $model->type()->associate((new ProcessType())->newQuery()->inRandomOrder()->first());
            $model->closed_at = $faker->dateTimeBetween('now', '+1 month');
            $model->value = $faker->numberBetween(10000, 70000);
            $model->localization = $faker->words(asText: true);
            $model->priority = $faker->randomKey(Priority::array());
            $model->text = $faker->words(asText: true);

            $model->save();

            $bar->advance();
        }

        $bar->finish();
        $this->command->info('');
    }
}
