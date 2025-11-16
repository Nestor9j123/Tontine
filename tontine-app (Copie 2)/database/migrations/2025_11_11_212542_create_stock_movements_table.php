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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Qui a fait le mouvement
            $table->enum('type', ['in', 'out', 'adjustment']); // Entrée, Sortie, Ajustement
            $table->integer('quantity'); // Quantité (positif ou négatif)
            $table->integer('stock_before'); // Stock avant le mouvement
            $table->integer('stock_after'); // Stock après le mouvement
            $table->string('reference')->nullable(); // Référence (bon de livraison, etc.)
            $table->text('reason')->nullable(); // Raison du mouvement
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
