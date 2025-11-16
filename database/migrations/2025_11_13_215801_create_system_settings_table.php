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
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, integer, boolean, json
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Insérer les paramètres par défaut
        DB::table('system_settings')->insert([
            [
                'key' => 'company_name',
                'value' => 'Tontine App',
                'type' => 'string',
                'description' => 'Nom de l\'entreprise affiché dans l\'application',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'low_stock_threshold',
                'value' => '10',
                'type' => 'integer',
                'description' => 'Seuil d\'alerte pour les stocks faibles',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'primary_color',
                'value' => '#3B82F6',
                'type' => 'string',
                'description' => 'Couleur primaire de l\'application',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'secondary_color',
                'value' => '#EAB308',
                'type' => 'string',
                'description' => 'Couleur secondaire de l\'application',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'theme_mode',
                'value' => 'light',
                'type' => 'string',
                'description' => 'Mode de thème (light/dark)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
