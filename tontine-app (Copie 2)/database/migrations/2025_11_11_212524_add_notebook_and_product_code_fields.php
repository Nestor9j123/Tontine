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
        // Ajouter les champs pour le carnet physique aux clients
        Schema::table('clients', function (Blueprint $table) {
            $table->boolean('has_physical_notebook')->default(false)->after('is_active');
            $table->decimal('notebook_amount_paid', 10, 2)->default(0)->after('has_physical_notebook');
            $table->boolean('notebook_fully_paid')->default(false)->after('notebook_amount_paid');
        });
        
        // Ajouter le code unique aux produits et le stock
        Schema::table('products', function (Blueprint $table) {
            $table->string('code')->nullable()->after('id');
            $table->integer('stock_quantity')->default(0)->after('price');
            $table->integer('min_stock_alert')->default(10)->after('stock_quantity');
        });
        
        // Générer des codes pour les produits existants
        $products = DB::table('products')->get();
        foreach ($products as $product) {
            DB::table('products')
                ->where('id', $product->id)
                ->update(['code' => 'PROD-' . str_pad($product->id, 6, '0', STR_PAD_LEFT)]);
        }
        
        // Rendre le code unique et non nullable
        Schema::table('products', function (Blueprint $table) {
            $table->string('code')->unique()->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['has_physical_notebook', 'notebook_amount_paid', 'notebook_fully_paid']);
        });
        
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['code', 'stock_quantity', 'min_stock_alert']);
        });
    }
};
