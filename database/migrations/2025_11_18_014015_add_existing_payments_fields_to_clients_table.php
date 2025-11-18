<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            // Ajouter seulement les colonnes manquantes
            $table->boolean('has_existing_payments')->default(false);
            $table->integer('existing_payments_count')->nullable();
            $table->decimal('existing_payments_amount', 8, 2)->nullable();
            $table->date('existing_payments_start_date')->nullable();
            $table->text('existing_payments_notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn([
                'has_existing_payments',
                'existing_payments_count',
                'existing_payments_amount',
                'existing_payments_start_date',
                'existing_payments_notes'
            ]);
        });
    }
};
