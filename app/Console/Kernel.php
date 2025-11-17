<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Tâches existantes
        $schedule->command('tontine:daily-stats')->dailyAt('23:55');
        $schedule->command('tontine:check-low-stock')->hourlyAt(5);

        // Nouvelles tâches d'automatisation avancées
        $schedule->command('automation:run --type=payments')
                 ->dailyAt('09:00')
                 ->description('Envoi des rappels de paiements');

        $schedule->command('automation:run --type=reports')
                 ->dailyAt('18:00')
                 ->description('Génération des rapports quotidiens');

        $schedule->command('automation:run --type=workflows')
                 ->hourly()
                 ->description('Exécution des workflows automatisés');

        $schedule->command('automation:run --type=fraud')
                 ->everyThirtyMinutes()
                 ->description('Détection de fraudes');

        $schedule->command('automation:run --type=cleanup')
                 ->weekly()
                 ->sundays()
                 ->at('02:00')
                 ->description('Nettoyage des anciennes données');

        // Génération automatique des rapports mensuels le 28 de chaque mois
        $schedule->command('reports:generate-monthly')
                 ->monthlyOn(28, '08:00')
                 ->description('Génération automatique du rapport mensuel avec notification')
                 ->emailOutputOnFailure(config('mail.admin_email', 'admin@example.com'))
                 ->appendOutputTo(storage_path('logs/monthly-reports.log'));

        // Tâches supplémentaires
        $schedule->command('cache:prune-stale-tags')->hourly();
        $schedule->command('sanctum:prune-expired --hours=24')->daily();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
