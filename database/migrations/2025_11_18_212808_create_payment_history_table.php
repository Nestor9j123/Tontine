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
        Schema::create('payment_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained()->onDelete('cascade');
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('agent_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('recorded_by')->constrained('users')->comment('Secrétaire qui a enregistré');
            
            $table->enum('action_type', ['initial_payment', 'missing_payment', 'completion', 'adjustment'])
                  ->comment('Type d\'action: paiement initial, paiement manquant, complément, ajustement');
            
            $table->decimal('amount', 10, 2)->comment('Montant de cette action');
            $table->decimal('expected_amount', 10, 2)->nullable()->comment('Montant attendu pour ce paiement');
            $table->decimal('remaining_amount', 10, 2)->default(0)->comment('Montant restant après cette action');
            
            $table->text('notes')->nullable()->comment('Notes sur cette action');
            $table->json('metadata')->nullable()->comment('Données supplémentaires (carnet, détails, etc.)');
            
            $table->timestamp('action_date')->comment('Date de l\'action');
            $table->timestamps();
            
            $table->index(['payment_id', 'action_date']);
            $table->index(['client_id', 'action_date']);
            $table->index(['agent_id', 'action_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_history');
    }
};
