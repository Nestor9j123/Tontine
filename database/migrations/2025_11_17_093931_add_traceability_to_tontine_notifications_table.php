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
        Schema::table('tontine_notifications', function (Blueprint $table) {
            // Champs pour la traçabilité de suppression
            $table->softDeletes(); // deleted_at
            $table->unsignedBigInteger('deleted_by')->nullable();
            
            // Champs pour tracer qui a marqué comme livré
            $table->unsignedBigInteger('marked_delivered_by')->nullable();
            $table->timestamp('marked_delivered_at')->nullable();
            
            // Statut de livraison
            $table->boolean('is_delivered')->default(false);
            
            // Clés étrangères
            $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('marked_delivered_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tontine_notifications', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropForeign(['deleted_by']);
            $table->dropForeign(['marked_delivered_by']);
            $table->dropColumn([
                'deleted_by',
                'marked_delivered_by', 
                'marked_delivered_at',
                'is_delivered'
            ]);
        });
    }
};
