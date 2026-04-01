<?php

namespace Database\Seeders;

use App\Models\Calendar;
use App\Models\CalendarCategory;
use App\Models\Client;
use App\Models\Enums\CalendarType;
use App\Models\Enums\Priority;
use App\Models\User;
use Faker\Factory;
use Illuminate\Database\Seeder;

class CalendarSeeder extends Seeder
{
    private int $numberOfCalendars = 50;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $faker = Factory::create('pl_PL');

        (new Calendar())->newQuery()->truncate();

        $this->command->info('Creating Calendars ...');
        $bar = $this->command->getOutput()->createProgressBar($this->numberOfCalendars);

        for ($i = 0; $i <= $this->numberOfCalendars; $i++) {
            $model = new Calendar();
            $model->active = true;
            $model->user()->associate((new User())->newQuery()->inRandomOrder()->first());
            $model->client()->associate((new Client())->newQuery()->inRandomOrder()->first());
            $model->created_at = $faker->dateTimeBetween('now', '+1 month');
            $model->type = $faker->randomKey(CalendarType::array());
            $model->name = $model->type == CalendarType::TYPE_NOTE->name ? 'Note #'.($i+1) : 'Event #'.($i+1);
            $model->priority = $faker->randomKey(Priority::array());
            $model->category()->associate((new CalendarCategory())->newQuery()->inRandomOrder()->first());
            $model->text = $faker->words(asText: true);
            $model->save();

            $bar->advance();
        }

        $bar->finish();
        $this->command->info('');
    }
}
