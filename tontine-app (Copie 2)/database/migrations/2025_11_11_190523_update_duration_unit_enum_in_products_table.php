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
        // PostgreSQL: Supprimer et recréer la colonne avec le nouveau type
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('duration_unit');
        });
        
        Schema::table('products', function (Blueprint $table) {
            $table->enum('duration_unit', ['days', 'weeks', 'months', 'years'])->default('months')->after('duration_value');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('duration_unit');
        });
        
        Schema::table('products', function (Blueprint $table) {
            $table->enum('duration_unit', ['days', 'weeks', 'months'])->default('months')->after('duration_value');
        });
    }
};
