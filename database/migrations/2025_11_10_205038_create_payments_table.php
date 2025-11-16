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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique(); // Référence unique du paiement
            $table->foreignId('tontine_id')->constrained()->onDelete('cascade');
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('collected_by')->constrained('users'); // Agent qui collecte
            $table->decimal('amount', 10, 2);
            $table->date('payment_date');
            $table->enum('payment_method', ['cash', 'mobile_money', 'bank_transfer'])->default('cash');
            $table->string('transaction_id')->nullable(); // Pour mobile money/bank
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'validated', 'rejected'])->default('pending');
            $table->foreignId('validated_by')->nullable()->constrained('users'); // Secrétaire
            $table->timestamp('validated_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
