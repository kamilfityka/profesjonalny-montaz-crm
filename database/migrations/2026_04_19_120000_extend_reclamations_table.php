<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('reclamations', function (Blueprint $table) {
            $table->date('purchase_date')->nullable()->after('closed_at');
            $table->boolean('warranty')->default(false)->after('purchase_date');
            $table->text('fault_description')->nullable()->after('warranty');
            $table->string('responsibility_category', 64)->nullable()->after('fault_description');
            $table->string('source', 32)->default('manual')->after('responsibility_category');
        });
    }

    public function down(): void
    {
        Schema::table('reclamations', function (Blueprint $table) {
            $table->dropColumn([
                'purchase_date',
                'warranty',
                'fault_description',
                'responsibility_category',
                'source',
            ]);
        });
    }
};
