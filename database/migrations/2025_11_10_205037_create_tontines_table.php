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
        Schema::create('tontines', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Code unique de la tontine
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('agent_id')->constrained('users')->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('total_amount', 10, 2); // Montant total à payer
            $table->decimal('paid_amount', 10, 2)->default(0); // Montant payé
            $table->decimal('remaining_amount', 10, 2); // Montant restant
            $table->integer('total_payments'); // Nombre total de paiements
            $table->integer('completed_payments')->default(0); // Paiements effectués
            $table->enum('status', ['active', 'completed', 'suspended', 'cancelled'])->default('active');
            $table->text('notes')->nullable();
            $table->foreignId('validated_by')->nullable()->constrained('users'); // Secrétaire qui valide
            $table->timestamp('validated_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tontines');
    }
};
