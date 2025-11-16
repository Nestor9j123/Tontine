<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Payment;
use App\Models\Tontine;
use App\Models\Client;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\PaymentReminderEmail;
use App\Mail\LowStockAlertEmail;
use App\Mail\DailyReportEmail;
use Illuminate\Support\Facades\DB;

class AutomationCommand extends Command
{
    protected $signature = 'automation:run {--type=all : Type d\'automatisation à exécuter}';
    protected $description = 'Exécuter les tâches d\'automatisation avancées';

    public function handle()
    {
        $type = $this->option('type');
        
        $this->info('🚀 Démarrage de l\'automatisation...');
        
        switch ($type) {
            case 'payments':
                $this->automatePaymentReminders();
                break;
            case 'reports':
                $this->automateDailyReports();
                break;
            case 'workflows':
                $this->automateWorkflows();
                break;
            case 'fraud':
                $this->detectFraud();
                break;
            case 'cleanup':
                $this->cleanupOldData();
                break;
            case 'all':
            default:
                $this->automatePaymentReminders();
                $this->automateDailyReports();
                $this->automateWorkflows();
                $this->detectFraud();
                $this->cleanupOldData();
                break;
        }
        
        $this->info('✅ Automatisation terminée avec succès!');
    }

    private function automatePaymentReminders()
    {
        $this->info('📧 Envoi des rappels de paiements...');
        
        // Paiements en retard
        $overduePayments = Payment::where('status', 'pending')
            ->where('due_date', '<', Carbon::now())
            ->where('reminder_sent', false)
            ->with(['client', 'tontine'])
            ->get();

        foreach ($overduePayments as $payment) {
            try {
                Mail::to($payment->client->email)->send(new PaymentReminderEmail($payment));
                $payment->update(['reminder_sent' => true]);
                $this->line("✅ Rappel envoyé à {$payment->client->name}");
            } catch (\Exception $e) {
                $this->error("❌ Erreur envoi rappel à {$payment->client->name}: {$e->getMessage()}");
            }
        }

        // Paiements dus dans 3 jours
        $upcomingPayments = Payment::where('status', 'pending')
            ->whereBetween('due_date', [Carbon::now(), Carbon::now()->addDays(3)])
            ->where('advance_reminder_sent', false)
            ->with(['client', 'tontine'])
            ->get();

        foreach ($upcomingPayments as $payment) {
            try {
                Mail::to($payment->client->email)->send(new PaymentReminderEmail($payment, true));
                $payment->update(['advance_reminder_sent' => true]);
                $this->line("✅ Rappel avancé envoyé à {$payment->client->name}");
            } catch (\Exception $e) {
                $this->error("❌ Erreur rappel avancé à {$payment->client->name}: {$e->getMessage()}");
            }
        }

        $this->info('📧 Rappels de paiements terminés');
    }

    private function automateDailyReports()
    {
        $this->info('📊 Génération des rapports quotidiens...');
        
        $today = Carbon::today();
        $stats = [
            'date' => $today->format('d/m/Y'),
            'total_payments' => Payment::whereDate('created_at', $today)->count(),
            'paid_payments' => Payment::whereDate('created_at', $today)->where('status', 'paid')->count(),
            'pending_payments' => Payment::whereDate('created_at', $today)->where('status', 'pending')->count(),
            'total_revenue' => Payment::whereDate('created_at', $today)->where('status', 'paid')->sum('amount'),
            'new_clients' => Client::whereDate('created_at', $today)->count(),
            'active_tontines' => Tontine::where('status', 'active')->count(),
            'overdue_payments' => Payment::where('due_date', '<', $today)->where('status', 'pending')->count(),
        ];

        // Envoyer le rapport aux administrateurs
        $admins = User::role('super_admin')->get();
        
        foreach ($admins as $admin) {
            try {
                Mail::to($admin->email)->send(new DailyReportEmail($stats));
                $this->line("✅ Rapport envoyé à {$admin->name}");
            } catch (\Exception $e) {
                $this->error("❌ Erreur envoi rapport à {$admin->name}: {$e->getMessage()}");
            }
        }

        $this->info('📊 Rapports quotidiens générés');
    }

    private function automateWorkflows()
    {
        $this->info('⚙️ Exécution des workflows automatisés...');
        
        // Workflow 1: Validation automatique des petits paiements
        $this->validateSmallPayments();
        
        // Workflow 2: Clôture automatique des tontines terminées
        $this->closeCompletedTontines();
        
        // Workflow 3: Mise à jour automatique des statuts
        $this->updatePaymentStatuses();
        
        $this->info('⚙️ Workflows terminés');
    }

    private function validateSmallPayments()
    {
        $threshold = config('automation.auto_validation_threshold', 1000);
        
        $smallPayments = Payment::where('status', 'pending')
            ->where('amount', '<=', $threshold)
            ->where('created_at', '<', Carbon::now()->subHours(24))
            ->get();

        foreach ($smallPayments as $payment) {
            $payment->update([
                'status' => 'validated',
                'validated_at' => now(),
                'validator_id' => null, // Validation système
                'validation_notes' => 'Validation automatique - montant inférieur au seuil'
            ]);
            
            $this->line("✅ Paiement {$payment->id} validé automatiquement");
        }
    }

    private function closeCompletedTontines()
    {
        $completedTontines = Tontine::where('status', 'active')
            ->where('end_date', '<', Carbon::now())
            ->whereDoesntHave('payments', function($query) {
                $query->where('status', 'pending');
            })
            ->get();

        foreach ($completedTontines as $tontine) {
            $tontine->update([
                'status' => 'completed',
                'completed_at' => now()
            ]);
            
            $this->line("✅ Tontine {$tontine->name} clôturée automatiquement");
        }
    }

    private function updatePaymentStatuses()
    {
        // Marquer les paiements comme en retard
        $overduePayments = Payment::where('status', 'pending')
            ->where('due_date', '<', Carbon::now())
            ->whereNull('overdue_notified_at')
            ->get();

        foreach ($overduePayments as $payment) {
            $payment->update(['overdue_notified_at' => now()]);
            $this->line("⚠️ Paiement {$payment->id} marqué comme en retard");
        }
    }

    private function detectFraud()
    {
        $this->info('🔍 Détection de fraudes...');
        
        // Détection 1: Multiples paiements identiques
        $suspiciousPayments = DB::table('payments')
            ->select('client_id', 'amount', 'payment_date', DB::raw('COUNT(*) as count'))
            ->where('created_at', '>', Carbon::now()->subDays(7))
            ->groupBy('client_id', 'amount', 'payment_date')
            ->having('count', '>', 1)
            ->get();

        foreach ($suspiciousPayments as $suspicious) {
            $this->warn("⚠️ Activité suspecte détectée: Client {$suspicious->client_id} - {$suspicious->count} paiements identiques");
        }

        // Détection 2: Montants anormalement élevés
        $avgPayment = Payment::where('created_at', '>', Carbon::now()->subDays(30))
            ->where('status', 'paid')
            ->avg('amount');
        
        $threshold = $avgPayment * 5; // 5x la moyenne
        
        $largePayments = Payment::where('created_at', '>', Carbon::now()->subHours(24))
            ->where('amount', '>', $threshold)
            ->where('status', 'pending')
            ->get();

        foreach ($largePayments as $payment) {
            $this->warn("⚠️ Montant élevé suspect: Paiement {$payment->id} - {$payment->amount}");
        }

        // Détection 3: Activité inhabituelle d'un agent
        $agents = User::role('agent')->get();
        
        foreach ($agents as $agent) {
            $recentPayments = Payment::where('collector_id', $agent->id)
                ->where('created_at', '>', Carbon::now()->subHours(24))
                ->count();
            
            if ($recentPayments > 50) { // Plus de 50 paiements en 24h
                $this->warn("⚠️ Activité inhabituelle: Agent {$agent->name} - {$recentPayments} paiements en 24h");
            }
        }

        $this->info('🔍 Détection de fraudes terminée');
    }

    private function cleanupOldData()
    {
        $this->info('🧹 Nettoyage des anciennes données...');
        
        // Nettoyer les anciennes notifications
        $deletedNotifications = DB::table('notifications')
            ->where('created_at', '<', Carbon::now()->subDays(90))
            ->delete();
        
        $this->line("🗑️ {$deletedNotifications} anciennes notifications supprimées");

        // Nettoyer les anciennes sessions
        $deletedSessions = DB::table('sessions')
            ->where('last_activity', '<', Carbon::now()->subDays(30)->timestamp)
            ->delete();
        
        $this->line("🗑️ {$deletedSessions} anciennes sessions supprimées");

        // Archiver les anciens logs (implémentation simple)
        $logFile = storage_path('logs/laravel.log');
        if (file_exists($logFile) && filesize($logFile) > 10 * 1024 * 1024) { // 10MB
            $archiveFile = storage_path('logs/laravel-' . Carbon::now()->format('Y-m-d') . '.log');
            rename($logFile, $archiveFile);
            $this->line("🗑️ Log archivé: " . basename($archiveFile));
        }

        $this->info('🧹 Nettoyage terminé');
    }
}
