<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Super Admin
        $superAdmin = User::create([
            'name' => 'Super Administrateur',
            'email' => 'admin@tontine.com',
            'password' => Hash::make('password'),
            'phone' => '+237 690 000 001',
            'address' => 'Yaoundé, Cameroun',
            'city' => 'Yaoundé',
            'is_active' => true,
        ]);
        $superAdmin->assignRole('super_admin');

        // Secrétaire
        $secretary = User::create([
            'name' => 'Marie Secrétaire',
            'email' => 'secretary@tontine.com',
            'password' => Hash::make('password'),
            'phone' => '+237 690 000 002',
            'address' => 'Douala, Cameroun',
            'city' => 'Douala',
            'is_active' => true,
        ]);
        $secretary->assignRole('secretary');

        // Agents
        $agent1 = User::create([
            'name' => 'Jean Agent',
            'email' => 'agent1@tontine.com',
            'password' => Hash::make('password'),
            'phone' => '+237 690 000 003',
            'address' => 'Yaoundé, Cameroun',
            'city' => 'Yaoundé',
            'is_active' => true,
        ]);
        $agent1->assignRole('agent');

        $agent2 = User::create([
            'name' => 'Paul Agent',
            'email' => 'agent2@tontine.com',
            'password' => Hash::make('password'),
            'phone' => '+237 690 000 004',
            'address' => 'Douala, Cameroun',
            'city' => 'Douala',
            'is_active' => true,
        ]);
        $agent2->assignRole('agent');

        $agent3 = User::create([
            'name' => 'Sophie Agent',
            'email' => 'agent3@tontine.com',
            'password' => Hash::make('password'),
            'phone' => '+237 690 000 005',
            'address' => 'Bafoussam, Cameroun',
            'city' => 'Bafoussam',
            'is_active' => true,
        ]);
        $agent3->assignRole('agent');
    }
}
