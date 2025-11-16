<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Client;
use App\Models\Tontine;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DailyStatsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tontine:daily-stats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Génère un récapitulatif des statistiques quotidiennes (clients, tontines, paiements)";

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today();

        $stats = [
            'date' => $today->toDateString(),
            'clients' => [
                'total' => Client::count(),
                'created_today' => Client::whereDate('created_at', $today)->count(),
            ],
            'tontines' => [
                'active' => Tontine::where('status', 'active')->count(),
                'completed' => Tontine::where('status', 'completed')->count(),
                'created_today' => Tontine::whereDate('created_at', $today)->count(),
            ],
            'payments' => [
                'count_today' => Payment::whereDate('payment_date', $today)->count(),
                'amount_today' => (float) Payment::whereDate('payment_date', $today)->sum('amount'),
                'pending' => Payment::where('status', 'pending')->count(),
                'validated' => Payment::where('status', 'validated')->count(),
                'rejected' => Payment::where('status', 'rejected')->count(),
            ],
        ];

        Log::info('[DailyStats] Récapitulatif quotidien', $stats);

        $this->info('Statistiques quotidiennes générées et enregistrées dans les logs.');

        return Command::SUCCESS;
    }
}
