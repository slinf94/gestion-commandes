<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductType;
use App\Models\Category;
use Illuminate\Support\Str;

class CreateProductTypesSeeder extends Seeder
{
    public function run(): void
    {
        $productTypes = [
            // Électronique & Informatique
            ['name' => 'Smartphone', 'description' => 'Téléphones intelligents et accessoires', 'category_id' => Category::where('name', 'LIKE', '%Téléphonie%')->first()->id ?? null],
            ['name' => 'Ordinateur Portable', 'description' => 'Laptops et notebooks', 'category_id' => Category::where('name', 'LIKE', '%Ordinateurs%')->first()->id ?? null],
            ['name' => 'Ordinateur de Bureau', 'description' => 'PC de bureau et composants', 'category_id' => Category::where('name', 'LIKE', '%Ordinateurs%')->first()->id ?? null],
            ['name' => 'Tablette', 'description' => 'Tablettes tactiles et accessoires', 'category_id' => Category::where('name', 'LIKE', '%Tablettes%')->first()->id ?? null],
            ['name' => 'Enceinte Bluetooth', 'description' => 'Haut-parleurs sans fil', 'category_id' => Category::where('name', 'LIKE', '%Audio%')->first()->id ?? null],
            ['name' => 'Casque Audio', 'description' => 'Écouteurs et casques audio', 'category_id' => Category::where('name', 'LIKE', '%Audio%')->first()->id ?? null],
            ['name' => 'Téléviseur', 'description' => 'Écrans TV et accessoires', 'category_id' => Category::where('name', 'LIKE', '%Télévisions%')->first()->id ?? null],
            ['name' => 'Drone', 'description' => 'Drones et gadgets volants', 'category_id' => Category::where('name', 'LIKE', '%Drones%')->first()->id ?? null],
            ['name' => 'Montre Connectée', 'description' => 'Smartwatches et wearables', 'category_id' => Category::where('name', 'LIKE', '%Montres%')->first()->id ?? null],
            ['name' => 'Appareil Photo', 'description' => 'Caméras et équipements photo', 'category_id' => Category::where('name', 'LIKE', '%Photo%')->first()->id ?? null],

            // Console de Jeux
            ['name' => 'Console de Jeux', 'description' => 'PlayStation, Xbox, Nintendo', 'category_id' => Category::where('name', 'LIKE', '%Consoles%')->first()->id ?? null],
            ['name' => 'Manette de Jeu', 'description' => 'Contrôleurs et accessoires gaming', 'category_id' => Category::where('name', 'LIKE', '%Consoles%')->first()->id ?? null],

            // Mode & Vêtements
            ['name' => 'T-Shirt', 'description' => 'T-shirts homme, femme, enfant', 'category_id' => Category::where('name', 'LIKE', '%Vêtements%')->first()->id ?? null],
            ['name' => 'Jean', 'description' => 'Jeans et pantalons', 'category_id' => Category::where('name', 'LIKE', '%Vêtements%')->first()->id ?? null],
            ['name' => 'Robe', 'description' => 'Robes et vêtements féminins', 'category_id' => Category::where('name', 'LIKE', '%Vêtements%')->first()->id ?? null],
            ['name' => 'Chaussures', 'description' => 'Chaussures de sport et habillées', 'category_id' => Category::where('name', 'LIKE', '%Vêtements%')->first()->id ?? null],
            ['name' => 'Sac à Main', 'description' => 'Sacs et accessoires mode', 'category_id' => Category::where('name', 'LIKE', '%Mode%')->first()->id ?? null],

            // Maison & Jardin
            ['name' => 'Meuble de Salon', 'description' => 'Canapés, tables et mobilier salon', 'category_id' => Category::where('name', 'LIKE', '%Maison%')->first()->id ?? null],
            ['name' => 'Literie', 'description' => 'Matelas, oreillers et linge de lit', 'category_id' => Category::where('name', 'LIKE', '%Literie%')->first()->id ?? null],
            ['name' => 'Décoration', 'description' => 'Objets décoratifs pour la maison', 'category_id' => Category::where('name', 'LIKE', '%Décoration%')->first()->id ?? null],
            ['name' => 'Éclairage', 'description' => 'Lampes et luminaires', 'category_id' => Category::where('name', 'LIKE', '%Éclairage%')->first()->id ?? null],
            ['name' => 'Électroménager', 'description' => 'Appareils ménagers', 'category_id' => Category::where('name', 'LIKE', '%Électroménager%')->first()->id ?? null],
            ['name' => 'Plante d\'Intérieur', 'description' => 'Plantes et accessoires jardin', 'category_id' => Category::where('name', 'LIKE', '%Jardinage%')->first()->id ?? null],

            // Sports & Fitness
            ['name' => 'Vélo', 'description' => 'Vélos et accessoires cyclisme', 'category_id' => Category::where('name', 'LIKE', '%Vélos%')->first()->id ?? null],
            ['name' => 'Équipement Fitness', 'description' => 'Matériel de musculation et fitness', 'category_id' => Category::where('name', 'LIKE', '%Fitness%')->first()->id ?? null],
            ['name' => 'Tapis de Yoga', 'description' => 'Accessoires yoga et pilates', 'category_id' => Category::where('name', 'LIKE', '%Sports%')->first()->id ?? null],
            ['name' => 'Ballon de Sport', 'description' => 'Ballons et équipements sportifs', 'category_id' => Category::where('name', 'LIKE', '%Sports%')->first()->id ?? null],

            // Cuisine
            ['name' => 'Cuisine & Ustensiles', 'description' => 'Ustensiles et accessoires cuisine', 'category_id' => Category::where('name', 'LIKE', '%Cuisine%')->first()->id ?? null],
            ['name' => 'Casserole & Poêle', 'description' => 'Casseroles et poêles anti-adhésives', 'category_id' => Category::where('name', 'LIKE', '%Cuisine%')->first()->id ?? null],

            // Bijoux & Accessoires
            ['name' => 'Bijoux', 'description' => 'Bijoux en or et argent', 'category_id' => Category::where('name', 'LIKE', '%Bijoux%')->first()->id ?? null],
            ['name' => 'Montre Classique', 'description' => 'Montres automatiques et à quartz', 'category_id' => Category::where('name', 'LIKE', '%Montres%')->first()->id ?? null],

            // Beauté & Parfums
            ['name' => 'Parfum', 'description' => 'Parfums homme et femme', 'category_id' => Category::where('name', 'LIKE', '%Parfums%')->first()->id ?? null],
            ['name' => 'Produit Cosmétique', 'description' => 'Maquillage et produits beauté', 'category_id' => Category::where('name', 'LIKE', '%Cosmétiques%')->first()->id ?? null],
            ['name' => 'Soin Visage', 'description' => 'Crèmes et soins pour le visage', 'category_id' => Category::where('name', 'LIKE', '%Parfums%')->first()->id ?? null],

            // Automobile
            ['name' => 'Accessoire Auto', 'description' => 'Accessoires automobile', 'category_id' => Category::where('name', 'LIKE', '%Automobile%')->first()->id ?? null],
            ['name' => 'Pièce Automobile', 'description' => 'Pièces et composants auto', 'category_id' => Category::where('name', 'LIKE', '%Automobile%')->first()->id ?? null],

            // Livres & Médias
            ['name' => 'Livre', 'description' => 'Livres et publications', 'category_id' => Category::where('name', 'LIKE', '%Livres%')->first()->id ?? null],
            ['name' => 'Jeu de Société', 'description' => 'Jeux de société et puzzles', 'category_id' => Category::where('name', 'LIKE', '%Jouets%')->first()->id ?? null],

            // Outillage
            ['name' => 'Outil Bricolage', 'description' => 'Outils de bricolage et fixation', 'category_id' => Category::where('name', 'LIKE', '%Bricolage%')->first()->id ?? null],

            // Animalerie
            ['name' => 'Nourriture Animale', 'description' => 'Alimentation pour animaux', 'category_id' => Category::where('name', 'LIKE', '%Animalerie%')->first()->id ?? null],
            ['name' => 'Accessoire Animal', 'description' => 'Accessoires pour animaux domestiques', 'category_id' => Category::where('name', 'LIKE', '%Animalerie%')->first()->id ?? null],

            // Bébé & Enfant
            ['name' => 'Vêtement Bébé', 'description' => 'Vêtements bébé et nourrisson', 'category_id' => Category::where('name', 'LIKE', '%Bébé%')->first()->id ?? null],
            ['name' => 'Jouet Enfant', 'description' => 'Jouets et jeux pour enfants', 'category_id' => Category::where('name', 'LIKE', '%Enfant%')->first()->id ?? null],

            // Sécurité
            ['name' => 'Système de Sécurité', 'description' => 'Alarmes et systèmes de sécurité', 'category_id' => Category::where('name', 'LIKE', '%Sécurité%')->first()->id ?? null],
            ['name' => 'Équipement de Sécurité', 'description' => 'Équipements de protection', 'category_id' => Category::where('name', 'LIKE', '%Sécurité%')->first()->id ?? null],
        ];

        foreach ($productTypes as $type) {
            ProductType::updateOrCreate(
                [
                    'name' => $type['name']
                ],
                [
                    'slug' => Str::slug($type['name']),
                    'description' => $type['description'],
                    'category_id' => $type['category_id'],
                    'is_active' => true,
                    'sort_order' => rand(1, 100)
                ]
            );
        }

        $this->command->info('✅ ' . count($productTypes) . ' types de produits créés avec succès !');
    }
}

