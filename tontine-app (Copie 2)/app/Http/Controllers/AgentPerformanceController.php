<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class AgentPerformanceController extends Controller
{
    public function getMyRanking($agentId)
    {
        // Récupérer tous les agents avec leurs stats
        $agents = User::role('agent')
            ->select('users.*')
            ->withCount(['clients', 'tontines', 'payments' => function($q) {
                $q->where('status', 'validated');
            }])
            ->withSum(['payments as total_amount' => function($q) {
                $q->where('status', 'validated');
            }], 'amount')
            ->orderBy('clients_count', 'desc')
            ->orderBy('total_amount', 'desc')
            ->orderBy('tontines_count', 'desc')
            ->get();

        // Trouver le rang de l'agent
        $rank = 0;
        $myStats = null;
        foreach ($agents as $index => $agent) {
            if ($agent->id == $agentId) {
                $rank = $index + 1;
                $myStats = $agent;
                break;
            }
        }

        if (!$myStats) {
            return null;
        }

        // Calculer le score de performance
        $performanceScore = $this->calculatePerformanceScore($myStats);
        
        // Déterminer le badge
        $badge = $this->getBadge($performanceScore);
        
        // Calculer les objectifs pour le prochain niveau
        $nextLevel = $this->getNextLevelGoals($myStats, $badge);

        return [
            'rank' => $rank,
            'total_agents' => $agents->count(),
            'clients_count' => $myStats->clients_count,
            'tontines_count' => $myStats->tontines_count,
            'payments_count' => $myStats->payments_count,
            'total_amount' => $myStats->total_amount ?? 0,
            'performance_score' => $performanceScore,
            'badge' => $badge,
            'next_level' => $nextLevel,
            'top_3' => $rank <= 3,
            'top_10' => $rank <= 10,
        ];
    }

    private function calculatePerformanceScore($agent)
    {
        $clientsScore = min(($agent->clients_count / 50) * 30, 30);
        $tontinesScore = min(($agent->tontines_count / 30) * 25, 25);
        $paymentsScore = min(($agent->payments_count / 100) * 25, 25);
        $amountScore = min((($agent->total_amount ?? 0) / 1000000) * 20, 20);

        return round($clientsScore + $tontinesScore + $paymentsScore + $amountScore, 1);
    }

    private function getBadge($score)
    {
        if ($score >= 80) {
            return ['name' => 'Diamant', 'color' => 'blue', 'icon' => '💎', 'level' => 5];
        } elseif ($score >= 60) {
            return ['name' => 'Or', 'color' => 'yellow', 'icon' => '🥇', 'level' => 4];
        } elseif ($score >= 40) {
            return ['name' => 'Argent', 'color' => 'gray', 'icon' => '🥈', 'level' => 3];
        } elseif ($score >= 20) {
            return ['name' => 'Bronze', 'color' => 'orange', 'icon' => '🥉', 'level' => 2];
        } else {
            return ['name' => 'Débutant', 'color' => 'green', 'icon' => '🌱', 'level' => 1];
        }
    }

    private function getNextLevelGoals($agent, $currentBadge)
    {
        $goals = [];
        
        switch ($currentBadge['level']) {
            case 1: // Débutant -> Bronze (20%)
                $goals = [
                    'clients' => max(0, 10 - $agent->clients_count),
                    'tontines' => max(0, 6 - $agent->tontines_count),
                    'payments' => max(0, 20 - $agent->payments_count),
                    'amount' => max(0, 200000 - ($agent->total_amount ?? 0)),
                    'target_badge' => 'Bronze 🥉',
                ];
                break;
            case 2: // Bronze -> Argent (40%)
                $goals = [
                    'clients' => max(0, 20 - $agent->clients_count),
                    'tontines' => max(0, 12 - $agent->tontines_count),
                    'payments' => max(0, 40 - $agent->payments_count),
                    'amount' => max(0, 400000 - ($agent->total_amount ?? 0)),
                    'target_badge' => 'Argent 🥈',
                ];
                break;
            case 3: // Argent -> Or (60%)
                $goals = [
                    'clients' => max(0, 30 - $agent->clients_count),
                    'tontines' => max(0, 18 - $agent->tontines_count),
                    'payments' => max(0, 60 - $agent->payments_count),
                    'amount' => max(0, 600000 - ($agent->total_amount ?? 0)),
                    'target_badge' => 'Or 🥇',
                ];
                break;
            case 4: // Or -> Diamant (80%)
                $goals = [
                    'clients' => max(0, 40 - $agent->clients_count),
                    'tontines' => max(0, 24 - $agent->tontines_count),
                    'payments' => max(0, 80 - $agent->payments_count),
                    'amount' => max(0, 800000 - ($agent->total_amount ?? 0)),
                    'target_badge' => 'Diamant 💎',
                ];
                break;
            case 5: // Diamant (max level)
                $goals = [
                    'clients' => 0,
                    'tontines' => 0,
                    'payments' => 0,
                    'amount' => 0,
                    'target_badge' => 'Niveau Maximum!',
                    'max_level' => true,
                ];
                break;
        }

        return $goals;
    }
}
