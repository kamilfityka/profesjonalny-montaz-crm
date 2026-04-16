<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reclamations', function (Blueprint $table) {
            $table->boolean('warranty')->default(false)->after('priority');
            $table->date('purchase_date')->nullable()->after('warranty');
            $table->text('fault_description')->nullable()->after('purchase_date');
            $table->string('fault_category')->nullable()->after('fault_description');
            $table->string('urgency')->default('Niepilne')->after('fault_category');
        });
    }

    public function down(): void
    {
        Schema::table('reclamations', function (Blueprint $table) {
            $table->dropColumn(['warranty', 'purchase_date', 'fault_description', 'fault_category', 'urgency']);
        });
    }
};
