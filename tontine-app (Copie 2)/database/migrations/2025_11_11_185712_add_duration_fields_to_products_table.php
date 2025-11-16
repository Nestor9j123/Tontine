<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Ajouter les nouveaux champs
            $table->integer('duration_value')->default(1)->after('price');
            $table->enum('duration_unit', ['days', 'weeks', 'months'])->default('months')->after('duration_value');
            
            // Migrer les données existantes
            // duration_months -> duration_value et duration_unit = 'months'
        });
        
        // Migrer les données existantes
        DB::table('products')->update([
            'duration_value' => DB::raw('duration_months'),
            'duration_unit' => 'months'
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['duration_value', 'duration_unit']);
        });
    }
};
