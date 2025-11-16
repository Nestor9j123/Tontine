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
        Schema::table('products', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->after('id');
        });

        // Générer des UUIDs pour les produits existants
        \App\Models\Product::whereNull('uuid')->each(function ($product) {
            $product->update(['uuid' => \Illuminate\Support\Str::uuid()]);
        });

        // Ajouter un index unique
        Schema::table('products', function (Blueprint $table) {
            $table->unique('uuid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
    }
};
