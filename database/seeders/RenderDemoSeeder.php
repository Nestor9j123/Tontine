<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;
use App\Models\Client;
use App\Models\Tontine;
use App\Models\Payment;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class RenderDemoSeeder extends Seeder
{
    public function run()
    {
        // Créer les rôles
        $roles = ['super_admin', 'secretary', 'agent'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // Créer l'utilisateur admin
        $admin = User::firstOrCreate([
            'email' => 'admin@tontine-app.com'
        ], [
            'name' => 'Super Admin',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('super_admin');

        // Créer un secrétaire
        $secretary = User::firstOrCreate([
            'email' => 'secretary@tontine-app.com'
        ], [
            'name' => 'Secrétaire Demo',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $secretary->assignRole('secretary');

        // Créer des agents
        for ($i = 1; $i <= 3; $i++) {
            $agent = User::firstOrCreate([
                'email' => "agent{$i}@tontine-app.com"
            ], [
                'name' => "Agent Demo {$i}",
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]);
            $agent->assignRole('agent');
        }

        // Créer des produits de démonstration
        $products = [
            ['name' => 'Tontine Standard', 'price' => 50000, 'duration_months' => 12, 'description' => 'Tontine classique 12 mois'],
            ['name' => 'Tontine Express', 'price' => 75000, 'duration_months' => 6, 'description' => 'Tontine rapide 6 mois'],
            ['name' => 'Tontine Premium', 'price' => 100000, 'duration_months' => 18, 'description' => 'Tontine premium 18 mois'],
            ['name' => 'Tontine Starter', 'price' => 25000, 'duration_months' => 8, 'description' => 'Tontine pour débuter'],
        ];

        foreach ($products as $productData) {
            Product::firstOrCreate([
                'name' => $productData['name']
            ], [
                'price' => $productData['price'],
                'duration_months' => $productData['duration_months'],
                'description' => $productData['description'],
                'stock_quantity' => rand(50, 200),
                'is_active' => true,
            ]);
        }

        // Créer des clients de démonstration
        $agents = User::role('agent')->get();
        $products = Product::all();

        for ($i = 1; $i <= 20; $i++) {
            $client = Client::firstOrCreate([
                'email' => "client{$i}@example.com"
            ], [
                'code' => 'CL' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'first_name' => 'Client',
                'last_name' => "Demo {$i}",
                'phone' => '+228' . rand(10000000, 99999999),
                'phone_secondary' => rand(0, 1) ? '+228' . rand(10000000, 99999999) : null,
                'address' => "Adresse du client {$i}",
                'city' => ['Lomé', 'Kara', 'Sokodé', 'Kpalimé'][rand(0, 3)],
                'id_card_number' => 'CNI' . rand(100000, 999999),
                'agent_id' => $agents->random()->id,
                'is_active' => true,
            ]);

            // Créer des tontines pour certains clients
            if (rand(0, 1)) {
                $product = $products->random();
                $startDate = now()->subMonths(rand(0, 6));
                
                $tontine = Tontine::create([
                    'code' => 'TON' . str_pad(($i * 10 + rand(1, 9)), 6, '0', STR_PAD_LEFT),
                    'client_id' => $client->id,
                    'product_id' => $product->id,
                    'agent_id' => $client->agent_id,
                    'start_date' => $startDate,
                    'end_date' => $startDate->copy()->addMonths($product->duration_months),
                    'total_amount' => $product->price,
                    'paid_amount' => 0,
                    'remaining_amount' => $product->price,
                    'total_payments' => $product->duration_months,
                    'completed_payments' => 0,
                    'status' => 'active',
                ]);

                // Créer quelques paiements
                $paymentsCount = rand(0, min(6, $product->duration_months));
                for ($p = 1; $p <= $paymentsCount; $p++) {
                    $paymentDate = $startDate->copy()->addMonths($p - 1);
                    $amount = $product->price / $product->duration_months;
                    
                    Payment::create([
                        'reference' => 'PAY' . str_pad(($i * 100 + $p), 8, '0', STR_PAD_LEFT),
                        'tontine_id' => $tontine->id,
                        'client_id' => $client->id,
                        'collected_by' => $client->agent_id,
                        'amount' => $amount,
                        'payment_date' => $paymentDate,
                        'payment_method' => ['cash', 'mobile_money', 'bank_transfer'][rand(0, 2)],
                        'status' => 'validated',
                        'validated_by' => $admin->id,
                        'validated_at' => $paymentDate->copy()->addHours(rand(1, 24)),
                    ]);

                    // Mettre à jour la tontine
                    $tontine->increment('completed_payments');
                    $tontine->increment('paid_amount', $amount);
                    $tontine->decrement('remaining_amount', $amount);
                }
            }
        }

        $this->command->info('🎉 Données de démonstration créées avec succès!');
        $this->command->info('👤 Admin: admin@tontine-app.com / password');
        $this->command->info('👤 Secrétaire: secretary@tontine-app.com / password');
        $this->command->info('👤 Agents: agent1@tontine-app.com / password (1-3)');
    }
}
