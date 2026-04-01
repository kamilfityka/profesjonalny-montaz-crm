<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Enums\Priority;
use App\Models\Sale;
use App\Models\SaleCategory;
use App\Models\SaleType;
use App\Models\User;
use Faker\Factory;
use Illuminate\Database\Seeder;

class SaleSeeder extends Seeder
{
    private int $numberOfSales = 50;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $faker = Factory::create('pl_PL');

        (new Sale())->newQuery()->truncate();

        $this->command->info('Creating Sales ...');
        $bar = $this->command->getOutput()->createProgressBar($this->numberOfSales);

        for ($i = 0; $i <= $this->numberOfSales; $i++) {
            $model = new Sale();
            $model->active = true;
            $model->user()->associate((new User())->newQuery()->inRandomOrder()->first());
            $model->client()->associate((new Client())->newQuery()->inRandomOrder()->first());
            $model->category()->associate((new SaleCategory())->newQuery()->inRandomOrder()->first());
            $model->name = 'Szansa sprzedaży #'.($i+1);
            $model->value = $faker->numberBetween(10000, 70000);
            $model->localization = $faker->words(asText: true);
            $model->closed_at = $faker->dateTimeBetween('now', '+1 month');
            $model->type()->associate((new SaleType())->newQuery()->inRandomOrder()->first());
            $model->priority = $faker->randomKey(Priority::array());
            $model->text = $faker->words(asText: true);

            $model->save();

            $bar->advance();
        }

        $bar->finish();
        $this->command->info('');
    }
}
