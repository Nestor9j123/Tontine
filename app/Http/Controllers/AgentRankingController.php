<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Client;
use App\Models\Payment;
use App\Models\Tontine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AgentRankingController extends Controller
{
    public function index(Request $request)
    {
        // Filtres
        $period = $request->get('period', 'all'); // all, month, week
        $sortBy = $request->get('sort_by', 'clients'); // clients, payments, amount
        
        // Requête de base pour les agents
        $query = User::role('agent')
            ->select('users.*')
            ->withCount([
                'clients' => function($q) use ($period) {
                    $this->applyPeriodFilter($q, $period);
                },
                'tontines' => function($q) use ($period) {
                    $this->applyPeriodFilter($q, $period);
                },
                'payments' => function($q) use ($period) {
                    $this->applyPeriodFilter($q, $period);
                    $q->where('status', 'validated');
                }
            ])
            ->withSum([
                'payments as total_amount' => function($q) use ($period) {
                    $this->applyPeriodFilter($q, $period);
                    $q->where('status', 'validated');
                }
            ], 'amount');

        // Tri selon le critère avec tri secondaire par montant collecté
        switch ($sortBy) {
            case 'payments':
                $query->orderBy('payments_count', 'desc')
                      ->orderBy('total_amount', 'desc')
                      ->orderBy('clients_count', 'desc');
                break;
            case 'amount':
                $query->orderBy('total_amount', 'desc')
                      ->orderBy('payments_count', 'desc')
                      ->orderBy('clients_count', 'desc');
                break;
            case 'tontines':
                $query->orderBy('tontines_count', 'desc')
                      ->orderBy('total_amount', 'desc')
                      ->orderBy('clients_count', 'desc');
                break;
            default:
                $query->orderBy('clients_count', 'desc')
                      ->orderBy('total_amount', 'desc')
                      ->orderBy('tontines_count', 'desc');
        }

        $agents = $query->paginate(20);

        // Calculer les rangs
        $rank = ($agents->currentPage() - 1) * $agents->perPage() + 1;
        foreach ($agents as $agent) {
            $agent->rank = $rank++;
            
            // Calculer le score de performance (0-100)
            $agent->performance_score = $this->calculatePerformanceScore($agent);
            
            // Déterminer le badge
            $agent->badge = $this->getBadge($agent->performance_score);
        }

        return view('agents.ranking', compact('agents', 'period', 'sortBy'));
    }

    private function applyPeriodFilter($query, $period)
    {
        switch ($period) {
            case 'week':
                $query->where('created_at', '>=', now()->subWeek());
                break;
            case 'month':
                $query->where('created_at', '>=', now()->subMonth());
                break;
            // 'all' ne nécessite pas de filtre
        }
    }

    private function calculatePerformanceScore($agent)
    {
        // Score basé sur plusieurs critères (sur 100)
        $clientsScore = min(($agent->clients_count / 50) * 30, 30); // Max 30 points
        $tontinesScore = min(($agent->tontines_count / 30) * 25, 25); // Max 25 points
        $paymentsScore = min(($agent->payments_count / 100) * 25, 25); // Max 25 points
        $amountScore = min((($agent->total_amount ?? 0) / 1000000) * 20, 20); // Max 20 points (1M FCFA)

        return round($clientsScore + $tontinesScore + $paymentsScore + $amountScore, 1);
    }

    private function getBadge($score)
    {
        if ($score >= 80) {
            return ['name' => 'Diamant', 'color' => 'blue', 'icon' => '💎'];
        } elseif ($score >= 60) {
            return ['name' => 'Or', 'color' => 'yellow', 'icon' => '🥇'];
        } elseif ($score >= 40) {
            return ['name' => 'Argent', 'color' => 'gray', 'icon' => '🥈'];
        } elseif ($score >= 20) {
            return ['name' => 'Bronze', 'color' => 'orange', 'icon' => '🥉'];
        } else {
            return ['name' => 'Débutant', 'color' => 'green', 'icon' => '🌱'];
        }
    }
}
