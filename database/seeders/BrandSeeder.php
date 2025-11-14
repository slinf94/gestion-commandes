<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Liste des marques de téléphones populaires au Burkina Faso et mondiales
     */
    public function run(): void
    {
        $brands = [
            // Marques mondiales
            'Samsung',
            'Apple',
            'Xiaomi',
            'Oppo',
            'Vivo',
            'Realme',
            'Motorola',
            'OnePlus',
            'Google',
            'Honor',
            'Huawei',
            'Sony',
            'Asus',
            'ZTE',
            'Alcatel',
            'Lenovo',
            'Meizu',
            'Blu',
            'Gionee',
            
            // Marques très populaires au Burkina Faso
            'Tecno',
            'Infinix',
            'Itel',
            
            // Marques spécialisées
            'Nokia',
            'Cat',
            'Energizer',
            'Mara',
            'Condor',
        ];

        // Créer un tableau de données pour insertion
        $data = [];
        foreach ($brands as $index => $brand) {
            $data[] = [
                'name' => $brand,
                'slug' => \Illuminate\Support\Str::slug($brand),
                'is_active' => true,
                'sort_order' => $index + 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Insérer dans une table brands si elle existe, sinon on utilisera juste les valeurs pour les filtres
        // Pour l'instant, on stocke les marques dans la colonne 'brand' des produits directement
        // Ce seeder sert de référence pour les options de filtre
    }
}
