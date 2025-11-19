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
        Schema::table('payments', function (Blueprint $table) {
            // Champs pour les paiements partiels
            $table->decimal('expected_amount', 10, 2)->nullable()->after('amount')->comment('Montant attendu/normal à payer');
            $table->decimal('missing_amount', 10, 2)->default(0)->after('expected_amount')->comment('Montant manquant');
            $table->boolean('is_partial_payment')->default(false)->after('missing_amount')->comment('Paiement partiel');
            $table->boolean('has_missing_payment')->default(false)->after('is_partial_payment')->comment('A un montant manquant');
            $table->decimal('missing_paid_amount', 10, 2)->default(0)->after('has_missing_payment')->comment('Montant manquant déjà payé');
            $table->timestamp('missing_completed_at')->nullable()->after('missing_paid_amount')->comment('Date de complément du paiement');
            $table->foreignId('completed_by')->nullable()->after('missing_completed_at')->constrained('users')->comment('Qui a complété le paiement');
            $table->text('missing_notes')->nullable()->after('completed_by')->comment('Notes sur le montant manquant');
            $table->enum('payment_status', ['complete', 'partial', 'missing_paid'])->default('complete')->after('missing_notes')->comment('Statut du paiement');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['completed_by']);
            $table->dropColumn([
                'expected_amount',
                'missing_amount', 
                'is_partial_payment',
                'has_missing_payment',
                'missing_paid_amount',
                'missing_completed_at',
                'completed_by',
                'missing_notes',
                'payment_status'
            ]);
        });
    }
};
