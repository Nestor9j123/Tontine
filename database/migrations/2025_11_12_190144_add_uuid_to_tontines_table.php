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
        Schema::table('tontines', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->after('id');
        });

        // Générer des UUIDs pour les tontines existantes
        \App\Models\Tontine::whereNull('uuid')->each(function ($tontine) {
            $tontine->update(['uuid' => \Illuminate\Support\Str::uuid()]);
        });

        // Ajouter un index unique
        Schema::table('tontines', function (Blueprint $table) {
            $table->unique('uuid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tontines', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
    }
};
