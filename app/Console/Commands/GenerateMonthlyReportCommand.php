<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MonthlyReport;
use App\Models\Product;
use App\Models\Payment;
use App\Models\Tontine;
use App\Models\MonthlyExpense;
use App\Models\User;
use App\Models\TontineNotification;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GenerateMonthlyReportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:generate-monthly {--month=} {--year=} {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Générer automatiquement le rapport mensuel s\'il n\'existe pas et envoyer une notification';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Déterminer le mois et l'année à traiter
        $month = $this->option('month') ?: Carbon::now()->subMonth()->month;
        $year = $this->option('year') ?: Carbon::now()->subMonth()->year;
        $force = $this->option('force');

        $this->info("🕒 Génération du rapport mensuel pour {$month}/{$year}...");

        try {
            // Vérifier si le rapport existe déjà
            $existingReport = MonthlyReport::forMonth($month, $year)->first();
            
            if ($existingReport && !$force) {
                $this->warn("⚠️  Un rapport pour {$month}/{$year} existe déjà.");
                
                // Envoyer une notification de rappel si le rapport existe mais n'a pas été consulté récemment
                $this->sendReminderNotification($existingReport);
                return self::SUCCESS;
            }

            if ($existingReport && $force) {
                $this->warn("🗑️  Suppression du rapport existant (mode force activé)...");
                $existingReport->forceDelete();
            }

            DB::beginTransaction();

            // Générer le rapport automatiquement
            $report = $this->generateMonthlyReport($month, $year);
            
            // Créer une notification pour informer de la génération automatique
            $this->sendReportGeneratedNotification($report);

            DB::commit();

            $this->info("✅ Rapport mensuel généré automatiquement pour {$month}/{$year}");
            $this->info("📧 Notification envoyée aux administrateurs");
            
            return self::SUCCESS;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("❌ Erreur lors de la génération du rapport : " . $e->getMessage());
            
            // Envoyer une notification d'erreur
            $this->sendErrorNotification($month, $year, $e->getMessage());
            
            return self::FAILURE;
        }
    }

    /**
     * Générer le rapport mensuel (copie de la logique du contrôleur)
     */
    private function generateMonthlyReport($month, $year)
    {
        $startOfMonth = Carbon::create($year, $month, 1)->startOfDay();
        $endOfMonth = $startOfMonth->copy()->endOfMonth()->endOfDay();

        // Stock initial (fin du mois précédent)
        $initialStock = [];
        $products = Product::all();
        foreach ($products as $product) {
            $initialStock[$product->id] = [
                'name' => $product->name,
                'quantity' => $this->getStockAtDate($product->id, $startOfMonth->copy()->subSecond()),
            ];
        }

        // Stock final (fin du mois actuel)
        $finalStock = [];
        foreach ($products as $product) {
            $finalStock[$product->id] = [
                'name' => $product->name,
                'quantity' => $this->getStockAtDate($product->id, $endOfMonth),
            ];
        }

        // Produits vendus (tontines complétées dans le mois)
        $completedTontines = Tontine::whereBetween('validated_at', [$startOfMonth, $endOfMonth])
                                   ->where('status', 'completed')
                                   ->with('product')
                                   ->get();

        $productsSold = [];
        foreach ($completedTontines as $tontine) {
            $productId = $tontine->product_id;
            if (!isset($productsSold[$productId])) {
                $productsSold[$productId] = [
                    'name' => $tontine->product->name,
                    'quantity' => 0,
                    'revenue' => 0,
                ];
            }
            $productsSold[$productId]['quantity']++;
            $productsSold[$productId]['revenue'] += $tontine->total_amount;
        }

        // Chiffre d'affaires total
        $totalRevenue = Payment::whereBetween('payment_date', [$startOfMonth, $endOfMonth])
                              ->where('status', 'validated')
                              ->sum('amount');

        // Total des charges
        $totalExpenses = MonthlyExpense::forMonth($month, $year)->sum('amount');

        // Statistiques des paiements
        $paymentStats = [
            'total_payments' => Payment::whereBetween('payment_date', [$startOfMonth, $endOfMonth])->count(),
            'validated_payments' => Payment::whereBetween('payment_date', [$startOfMonth, $endOfMonth])
                                          ->where('status', 'validated')->count(),
            'pending_payments' => Payment::whereBetween('payment_date', [$startOfMonth, $endOfMonth])
                                        ->where('status', 'pending')->count(),
            'rejected_payments' => Payment::whereBetween('payment_date', [$startOfMonth, $endOfMonth])
                                         ->where('status', 'rejected')->count(),
        ];

        // Performance des agents
        $agents = User::role('agent')->get();
        $agentPerformance = [];
        foreach ($agents as $agent) {
            $agentPayments = Payment::whereBetween('payment_date', [$startOfMonth, $endOfMonth])
                                  ->where('collected_by', $agent->id)
                                  ->where('status', 'validated');
            
            $agentExpenses = MonthlyExpense::forMonth($month, $year)
                                         ->where('user_id', $agent->id)
                                         ->sum('amount');

            $agentPerformance[$agent->id] = [
                'name' => $agent->name,
                'payments_count' => $agentPayments->count(),
                'payments_amount' => $agentPayments->sum('amount'),
                'expenses' => $agentExpenses,
                'clients_count' => Tontine::where('agent_id', $agent->id)
                                         ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                                         ->distinct('client_id')
                                         ->count('client_id'),
            ];
        }

        // Obtenir un utilisateur système pour la génération automatique
        $systemUser = User::role('super_admin')->first() ?? User::first();

        // Créer le rapport
        $report = MonthlyReport::create([
            'report_month' => $month,
            'report_year' => $year,
            'initial_stock' => $initialStock,
            'final_stock' => $finalStock,
            'products_sold' => $productsSold,
            'total_revenue' => $totalRevenue,
            'total_expenses' => $totalExpenses,
            'net_result' => $totalRevenue - $totalExpenses,
            'payment_stats' => $paymentStats,
            'agent_performance' => $agentPerformance,
            'generated_by' => $systemUser->id,
            'generated_at' => now(),
        ]);

        return $report;
    }

    /**
     * Obtenir le stock d'un produit à une date donnée
     */
    private function getStockAtDate($productId, $date)
    {
        $product = Product::find($productId);
        return $product ? $product->stock_quantity : 0;
    }

    /**
     * Envoyer une notification de rapport généré automatiquement
     */
    private function sendReportGeneratedNotification($report)
    {
        $monthNames = [
            1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
            5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
            9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
        ];

        $monthName = $monthNames[$report->report_month];
        $revenue = number_format($report->total_revenue, 0, ',', ' ');
        $result = $report->net_result >= 0 ? 'bénéfice' : 'déficit';
        $resultAmount = number_format(abs($report->net_result), 0, ',', ' ');

        // Envoyer aux super admins et secrétaires
        $recipients = User::role(['super_admin', 'secretary'])->get();

        foreach ($recipients as $user) {
            TontineNotification::create([
                'tontine_id' => null,
                'client_id' => null,
                'agent_id' => null, // Notification générale pour admin
                'type' => 'monthly_report_auto',
                'title' => "📊 Rapport mensuel généré automatiquement",
                'message' => "Le rapport mensuel pour {$monthName} {$report->report_year} a été généré automatiquement. " .
                           "Chiffre d'affaires : {$revenue} FCFA. " .
                           "Résultat : {$resultAmount} FCFA de {$result}. " .
                           "Cliquez pour consulter les détails.",
            ]);
        }

        $this->info("📧 Notifications envoyées à " . $recipients->count() . " administrateur(s)");
    }

    /**
     * Envoyer une notification de rappel si le rapport existe mais n'est pas consulté
     */
    private function sendReminderNotification($report)
    {
        $monthNames = [
            1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
            5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
            9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
        ];

        // Vérifier s'il y a déjà une notification récente pour ce rapport
        $recentNotification = TontineNotification::where('type', 'monthly_report_reminder')
            ->where('created_at', '>=', now()->subDays(7))
            ->where('message', 'like', "%{$monthNames[$report->report_month]} {$report->report_year}%")
            ->exists();

        if (!$recentNotification) {
            $monthName = $monthNames[$report->report_month];
            
            // Envoyer aux super admins et secrétaires
            $recipients = User::role(['super_admin', 'secretary'])->get();

            foreach ($recipients as $user) {
                TontineNotification::create([
                    'tontine_id' => null,
                    'client_id' => null,
                    'agent_id' => null,
                    'type' => 'monthly_report_reminder',
                    'title' => "🔔 Rappel : Rapport mensuel disponible",
                    'message' => "Le rapport mensuel pour {$monthName} {$report->report_year} est disponible et n'attend que votre consultation. " .
                               "N'oubliez pas de le consulter pour suivre les performances de votre entreprise.",
                ]);
            }

            $this->info("📫 Notifications de rappel envoyées");
        }
    }

    /**
     * Envoyer une notification d'erreur
     */
    private function sendErrorNotification($month, $year, $error)
    {
        $monthNames = [
            1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
            5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
            9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
        ];

        $monthName = $monthNames[$month];

        // Envoyer uniquement aux super admins
        $recipients = User::role('super_admin')->get();

        foreach ($recipients as $user) {
            TontineNotification::create([
                'tontine_id' => null,
                'client_id' => null,
                'agent_id' => null,
                'type' => 'monthly_report_error',
                'title' => "❌ Erreur génération rapport automatique",
                'message' => "Échec de la génération automatique du rapport pour {$monthName} {$year}. " .
                           "Erreur : " . substr($error, 0, 200) . "... " .
                           "Veuillez générer le rapport manuellement.",
            ]);
        }
    }
}
