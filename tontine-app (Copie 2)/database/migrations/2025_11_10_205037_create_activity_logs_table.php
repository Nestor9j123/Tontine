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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('action'); // create, update, delete, validate, etc.
            $table->string('model_type'); // Type de modèle (Client, Payment, etc.)
            $table->unsignedBigInteger('model_id')->nullable(); // ID du modèle
            $table->json('old_values')->nullable(); // Anciennes valeurs
            $table->json('new_values')->nullable(); // Nouvelles valeurs
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
