<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $exists = DB::table('reclamation_categories')->where('name', 'Oczekuje na weryfikację')->exists();

        if (!$exists) {
            DB::table('reclamation_categories')->insert([
                'name' => 'Oczekuje na weryfikację',
                'bgColor' => '#ffc107',
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('reclamation_categories')->where('name', 'Oczekuje na weryfikację')->delete();
    }
};
