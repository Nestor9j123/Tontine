<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TontineNotification;
use App\Models\Tontine;
use App\Models\Client;
use App\Models\User;

class CreateTestNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tontine:create-test-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crée des notifications de test pour démonstration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Création de notifications de test...');

        // Récupérer quelques données pour les tests
        $tontine = Tontine::with(['client', 'product', 'agent'])->first();
        $client = Client::first();
        $agent = User::role('agent')->first();

        if (!$tontine || !$client || !$agent) {
            $this->error('Données insuffisantes pour créer des notifications de test.');
            return Command::FAILURE;
        }

        // 1. Notification de paiement terminé
        TontineNotification::create([
            'tontine_id' => $tontine->id,
            'client_id' => $tontine->client_id,
            'agent_id' => $tontine->agent_id,
            'type' => 'payment_completed',
            'title' => 'Paiements terminés',
            'message' => "Le client {$tontine->client->full_name} a terminé ses paiements pour {$tontine->product->name}. Produit prêt à être livré.",
        ]);
        $this->line('✅ Notification de paiement terminé créée');

        // 2. Notification de rappel de livraison
        TontineNotification::create([
            'tontine_id' => $tontine->id,
            'client_id' => $tontine->client_id,
            'agent_id' => $tontine->agent_id,
            'type' => 'delivery_reminder',
            'title' => 'Livraison en attente',
            'message' => "Le produit {$tontine->product->name} pour {$tontine->client->full_name} est prêt à être livré depuis 2 jours.",
        ]);
        $this->line('📦 Notification de rappel de livraison créée');

        // 3. Notification générale
        TontineNotification::create([
            'tontine_id' => null,
            'client_id' => null,
            'agent_id' => $agent->id,
            'type' => 'general',
            'title' => 'Rapport mensuel disponible',
            'message' => "Le rapport mensuel de novembre 2025 est maintenant disponible. Consultez vos performances et statistiques.",
        ]);
        $this->line('📊 Notification générale créée');

        $this->info('✨ 3 notifications de test créées avec succès !');
        return Command::SUCCESS;
    }
}
