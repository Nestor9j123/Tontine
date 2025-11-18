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
        // Augmenter la précision des colonnes financières dans tontines
        Schema::table('tontines', function (Blueprint $table) {
            $table->decimal('total_amount', 12, 2)->change(); // De 10,2 à 12,2
            $table->decimal('paid_amount', 12, 2)->change(); // De 10,2 à 12,2
            $table->decimal('remaining_amount', 12, 2)->change(); // De 10,2 à 12,2
        });

        // Augmenter la précision des colonnes financières dans payments
        Schema::table('payments', function (Blueprint $table) {
            $table->decimal('amount', 12, 2)->change(); // De 10,2 à 12,2
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revenir aux anciennes précisions
        Schema::table('tontines', function (Blueprint $table) {
            $table->decimal('total_amount', 10, 2)->change();
            $table->decimal('paid_amount', 10, 2)->change();
            $table->decimal('remaining_amount', 10, 2)->change();
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->decimal('amount', 10, 2)->change();
        });
    }
};
