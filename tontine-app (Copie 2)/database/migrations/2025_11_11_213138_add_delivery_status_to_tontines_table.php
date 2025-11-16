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
            $table->enum('delivery_status', ['pending', 'delivered'])->default('pending')->after('status');
            $table->timestamp('delivered_at')->nullable()->after('delivery_status');
            $table->foreignId('delivered_by')->nullable()->constrained('users')->after('delivered_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tontines', function (Blueprint $table) {
            $table->dropColumn(['delivery_status', 'delivered_at', 'delivered_by']);
        });
    }
};
