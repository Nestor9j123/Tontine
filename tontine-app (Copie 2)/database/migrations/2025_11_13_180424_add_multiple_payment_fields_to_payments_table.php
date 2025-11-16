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
            $table->decimal('daily_amount', 15, 2)->nullable()->after('amount');
            $table->integer('days_count')->nullable()->after('daily_amount');
            $table->boolean('is_multiple_payment')->default(false)->after('days_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['daily_amount', 'days_count', 'is_multiple_payment']);
        });
    }
};
