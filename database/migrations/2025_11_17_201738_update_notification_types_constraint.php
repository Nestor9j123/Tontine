<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Supprimer l'ancienne contrainte s'il y en a une
        DB::statement('ALTER TABLE tontine_notifications DROP CONSTRAINT IF EXISTS tontine_notifications_type_check');
        
        // Ajouter la nouvelle contrainte avec les nouveaux types
        DB::statement("ALTER TABLE tontine_notifications ADD CONSTRAINT tontine_notifications_type_check 
                      CHECK (type IN ('payment_completed', 'low_stock', 'delivery_reminder', 'general', 
                                     'monthly_report_auto', 'monthly_report_reminder', 'monthly_report_error'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Supprimer la contrainte mise à jour
        DB::statement('ALTER TABLE tontine_notifications DROP CONSTRAINT IF EXISTS tontine_notifications_type_check');
        
        // Remettre l'ancienne contrainte (types originaux seulement)
        DB::statement("ALTER TABLE tontine_notifications ADD CONSTRAINT tontine_notifications_type_check 
                      CHECK (type IN ('payment_completed', 'low_stock', 'delivery_reminder', 'general'))");
    }
};
