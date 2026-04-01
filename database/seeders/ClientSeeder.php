<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\ClientCategory;
use Faker\Factory;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    private int $numberOfClients = 50;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $faker = Factory::create('pl_PL');

        (new Client())->newQuery()->truncate();

        $this->command->info('Creating Clients ...');
        $bar = $this->command->getOutput()->createProgressBar($this->numberOfClients);

        for ($i = 0; $i <= $this->numberOfClients; $i++) {
            $model = new Client();
            $model->category()->associate((new ClientCategory())->newQuery()->inRandomOrder()->first());
            $model->active = true;
            $model->company_name = $faker->company();
            $model->nip = $faker->numerify('###-##-##-##');
            $model->name = $faker->firstName().' '.$faker->lastName();
            $model->function = $faker->title();
            $model->street = $faker->streetName();
            $model->postcode = $faker->postcode();
            $model->city = $faker->city();
            $model->email = $faker->email();
            $model->phone = $faker->numerify('###-###-###');
            $model->phone2 = $faker->numerify('###-###-###');
            $model->www = $faker->domainName();
            $model->prefix = '';
            $model->source = '';
            $model->discount = $faker->numberBetween(0, 100);
            $model->save();

            $bar->advance();
        }

        $bar->finish();
        $this->command->info('');
    }
}
