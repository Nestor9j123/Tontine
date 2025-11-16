<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\TontineNotification;
use App\Services\NotificationService;
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
    protected $description = 'Vérifie les produits en stock faible ou en rupture et crée des notifications';

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

        $notificationService = new NotificationService();
        $notificationsCreated = 0;

        // Créer des notifications pour les produits en stock faible
        foreach ($lowStock as $product) {
            // Vérifier si une notification récente existe déjà
            $existingNotification = TontineNotification::where('type', 'low_stock')
                ->where('message', 'like', "%{$product->name}%")
                ->where('created_at', '>=', now()->subDays(1))
                ->first();

            if (!$existingNotification) {
                TontineNotification::createLowStockNotification($product);
                $notificationsCreated++;
                $this->line("📦 Notification créée pour: {$product->name} (Stock: {$product->stock_quantity})");
            }
        }

        // Créer des notifications pour les produits en rupture
        foreach ($outOfStock as $product) {
            // Vérifier si une notification récente existe déjà
            $existingNotification = TontineNotification::where('type', 'low_stock')
                ->where('message', 'like', "%{$product->name}%")
                ->where('created_at', '>=', now()->subDays(1))
                ->first();

            if (!$existingNotification) {
                TontineNotification::create([
                    'tontine_id' => null,
                    'client_id' => null,
                    'agent_id' => null,
                    'type' => 'low_stock',
                    'title' => 'Rupture de stock',
                    'message' => "Le produit {$product->name} est en rupture de stock (0 restant).",
                ]);
                $notificationsCreated++;
                $this->error("🚨 Notification créée pour: {$product->name} (RUPTURE)");
            }
        }

        $payload = [
            'timestamp' => now()->toISOString(),
            'low_stock_count' => $lowStock->count(),
            'out_of_stock_count' => $outOfStock->count(),
            'notifications_created' => $notificationsCreated,
        ];

        Log::warning('[StockAlert] Produits en stock faible ou rupture détectés', $payload);

        $this->warn(sprintf(
            'Stock critique: %d produits en rupture, %d en stock faible. %d notifications créées.',
            $outOfStock->count(),
            $lowStock->count(),
            $notificationsCreated
        ));

        return Command::SUCCESS;
    }
}
