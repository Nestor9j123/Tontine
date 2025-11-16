<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use Illuminate\Support\Facades\Log;

class CheckLowStockCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tontine:check-low-stock';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Vérifie les produits en stock faible ou en rupture et enregistre une alerte dans les logs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $lowStockQuery = Product::where('is_active', true)
            ->whereNotNull('stock_quantity')
            ->whereNotNull('min_stock_alert')
            ->whereColumn('stock_quantity', '<=', 'min_stock_alert');

        $outOfStockQuery = Product::where('is_active', true)
            ->where('stock_quantity', 0);

        $lowStock = $lowStockQuery->get();
        $outOfStock = $outOfStockQuery->get();

        if ($lowStock->isEmpty() && $outOfStock->isEmpty()) {
            $this->info('Aucun produit en stock critique ou faible.');
            return Command::SUCCESS;
        }

        $payload = [
            'timestamp' => now()->toISOString(),
            'low_stock_count' => $lowStock->count(),
            'out_of_stock_count' => $outOfStock->count(),
            'low_stock_products' => $lowStock->map(fn($p) => [
                'code' => $p->code,
                'name' => $p->name,
                'stock_quantity' => $p->stock_quantity,
                'min_stock_alert' => $p->min_stock_alert,
            ]),
            'out_of_stock_products' => $outOfStock->map(fn($p) => [
                'code' => $p->code,
                'name' => $p->name,
            ]),
        ];

        Log::warning('[StockAlert] Produits en stock faible ou rupture détectés', $payload);

        $this->warn(sprintf(
            'Stock critique: %d produits en rupture, %d en stock faible.',
            $outOfStock->count(),
            $lowStock->count()
        ));

        return Command::SUCCESS;
    }
}
