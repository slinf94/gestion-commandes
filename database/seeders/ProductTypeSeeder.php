<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $productTypes = [
            [
                'name' => 'Ordinateur',
                'slug' => 'ordinateur',
                'description' => 'Ordinateurs portables et de bureau',
                'icon' => 'laptop',
                'is_active' => true
            ],
            [
                'name' => 'Habit',
                'slug' => 'habit',
                'description' => 'Vêtements et accessoires',
                'icon' => 'shirt',
                'is_active' => true
            ],
            [
                'name' => 'Voiture',
                'slug' => 'voiture',
                'description' => 'Véhicules automobiles',
                'icon' => 'car',
                'is_active' => true
            ],
            [
                'name' => 'Smartphone',
                'slug' => 'smartphone',
                'description' => 'Téléphones intelligents et accessoires',
                'icon' => 'phone',
                'is_active' => true
            ],
            [
                'name' => 'Meuble',
                'slug' => 'meuble',
                'description' => 'Mobilier et décoration',
                'icon' => 'chair',
                'is_active' => true
            ]
        ];

        foreach ($productTypes as $type) {
            \App\Models\ProductType::create($type);
        }
    }
}
