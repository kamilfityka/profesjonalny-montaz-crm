<?php namespace Database\Seeders;

class DatabaseSeeder extends \Praust\Database\Seeders\PraustDatabaseSeeder
{
    public function run()
    {
        $this->call(ClientCategorySeeder::class);
        $this->call(ClientSeeder::class);

        $this->call(CalendarCategorySeeder::class);
        $this->call(CalendarSeeder::class);

        $this->call(ProcessTypeSeeder::class);
        $this->call(ProcessSeeder::class);

        $this->call(ReclamationTypeSeeder::class);
        $this->call(ReclamationSeeder::class);

        $this->call(SaleTypeSeeder::class);
        $this->call(SaleSeeder::class);

        $this->call(DocumentTypeSeeder::class);
        $this->call(DocumentSeeder::class);
    }
}
