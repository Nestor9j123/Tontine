<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // En production, utiliser le seeder demo
        if (app()->environment('production')) {
            $this->call([
                RenderDemoSeeder::class,
            ]);
        } else {
            // En développement, utiliser les seeders normaux
            $this->call([
                RoleSeeder::class,
                UserSeeder::class,
                ProductSeeder::class,
            ]);
        }
    }
}
