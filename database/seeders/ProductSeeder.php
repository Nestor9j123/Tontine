<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Tontine Journalière 50 000 FCFA',
                'description' => 'Cotisation journalière de 1 000 FCFA pendant 50 jours',
                'price' => 50000,
                'duration_months' => 2,
                'type' => 'daily',
                'is_active' => true,
            ],
            [
                'name' => 'Tontine Journalière 100 000 FCFA',
                'description' => 'Cotisation journalière de 2 000 FCFA pendant 50 jours',
                'price' => 100000,
                'duration_months' => 2,
                'type' => 'daily',
                'is_active' => true,
            ],
            [
                'name' => 'Tontine Journalière 200 000 FCFA',
                'description' => 'Cotisation journalière de 4 000 FCFA pendant 50 jours',
                'price' => 200000,
                'duration_months' => 2,
                'type' => 'daily',
                'is_active' => true,
            ],
            [
                'name' => 'Tontine Hebdomadaire 100 000 FCFA',
                'description' => 'Cotisation hebdomadaire de 5 000 FCFA pendant 20 semaines',
                'price' => 100000,
                'duration_months' => 5,
                'type' => 'weekly',
                'is_active' => true,
            ],
            [
                'name' => 'Tontine Hebdomadaire 200 000 FCFA',
                'description' => 'Cotisation hebdomadaire de 10 000 FCFA pendant 20 semaines',
                'price' => 200000,
                'duration_months' => 5,
                'type' => 'weekly',
                'is_active' => true,
            ],
            [
                'name' => 'Tontine Mensuelle 300 000 FCFA',
                'description' => 'Cotisation mensuelle de 30 000 FCFA pendant 10 mois',
                'price' => 300000,
                'duration_months' => 10,
                'type' => 'monthly',
                'is_active' => true,
            ],
            [
                'name' => 'Tontine Mensuelle 500 000 FCFA',
                'description' => 'Cotisation mensuelle de 50 000 FCFA pendant 10 mois',
                'price' => 500000,
                'duration_months' => 10,
                'type' => 'monthly',
                'is_active' => true,
            ],
            [
                'name' => 'Tontine Mensuelle 1 000 000 FCFA',
                'description' => 'Cotisation mensuelle de 100 000 FCFA pendant 10 mois',
                'price' => 1000000,
                'duration_months' => 10,
                'type' => 'monthly',
                'is_active' => true,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
