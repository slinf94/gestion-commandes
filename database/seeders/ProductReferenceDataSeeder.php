<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductReferenceDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Ce seeder crée des produits de référence pour peupler les champs Select2
     * avec les marques, gammes, formats, types d'accessoires et compatibilités
     */
    public function run(): void
    {
        // Récupérer la première catégorie "Téléphones" et "Accessoires"
        $phoneCategory = DB::table('categories')
            ->where('name', 'LIKE', '%téléphone%')
            ->orWhere('name', 'LIKE', '%phone%')
            ->orWhere('name', 'LIKE', '%mobile%')
            ->first();
        
        $accessoryCategory = DB::table('categories')
            ->where('name', 'LIKE', '%accessoire%')
            ->orWhere('name', 'LIKE', '%accessory%')
            ->first();

        // Si pas de catégories, utiliser la première disponible
        if (!$phoneCategory) {
            $phoneCategory = DB::table('categories')->first();
        }
        if (!$accessoryCategory) {
            $accessoryCategory = DB::table('categories')->first();
        }

        $phoneCategoryId = $phoneCategory->id ?? 1;
        $accessoryCategoryId = $accessoryCategory->id ?? 1;

        // Marques de téléphones
        $brands = [
            'Tecno', 'Infinix', 'Itel', 'Samsung', 'iPhone', 'Xiaomi', 'Huawei',
            'Oppo', 'Vivo', 'Nokia', 'Realme', 'OnePlus', 'Lenovo', 'Alcatel',
            'Sony Xperia', 'LG', 'ZTE', 'Gionee', 'Wiko', 'Blackview', 'Doogee',
            'Cubot', 'Ulefone', 'Honor', 'Google Pixel', 'Motorola', 'Umidigi',
            'Asus', 'Lava', 'Turing', 'Redmi', 'Poco'
        ];

        // Gammes/Séries par marque
        $rangesByBrand = [
            'Tecno' => ['Spark', 'Camon', 'Phantom', 'Pop'],
            'Infinix' => ['Hot', 'Note', 'Zero', 'Smart'],
            'Itel' => ['A-series', 'S-series', 'P-series'],
            'Samsung' => ['Galaxy A', 'Galaxy M', 'Galaxy S', 'Galaxy Note', 'Z Fold'],
            'iPhone' => ['iPhone 6', 'iPhone 7', 'iPhone 8', 'iPhone X', 'iPhone XR', 'iPhone 11', 'iPhone 12', 'iPhone 13', 'iPhone 14', 'iPhone 15'],
            'Xiaomi' => ['Redmi Note', 'Redmi A', 'Poco X', 'Poco F'],
            'Redmi' => ['Redmi Note', 'Redmi A'],
            'Poco' => ['Poco X', 'Poco F'],
            'Huawei' => ['Y-series', 'Nova', 'P-series', 'Mate'],
            'Oppo' => ['A-series', 'Reno', 'F-series'],
            'Vivo' => ['Y-series', 'V-series', 'X-series'],
            'Nokia' => ['C-series', 'G-series', 'XR-series'],
            'Realme' => ['Narzo', 'C-series', 'GT', 'Note'],
            'OnePlus' => ['Nord', '8', '9', '10', '11'],
            'Lenovo' => ['K-series', 'A-series', 'Tab M'],
            'LG' => ['G-series', 'Velvet', 'K-series'],
            'Google Pixel' => ['Pixel 4', 'Pixel 5', 'Pixel 6', 'Pixel 7', 'Pixel 8'],
        ];

        // Types d'accessoires
        $accessoryTypes = [
            'Chargeur mural',
            'Câble USB',
            'Adaptateur secteur',
            'Écouteurs filaires',
            'Écouteurs Bluetooth',
            'Casque audio',
            'Batterie externe (Power Bank)',
            'Coque de protection',
            'Film protecteur (verre trempé)',
            'Support téléphone voiture',
            'Trépied photo / selfie stick',
            'Haut-parleur Bluetooth',
            'Clé USB OTG',
            'Adaptateur Type-C / Micro USB',
            'Station de charge sans fil',
            'Smartwatch',
            'Bracelet connecté',
            'Anneau lumineux (Ring Light)',
            'Carte mémoire (SD / microSD)',
            'Hub USB',
            'Dock de recharge multiple',
            'Étui tablette',
            'Câble HDMI mobile',
            'Support bureau pliable',
            'Mini ventilateur USB',
            'Câble auxiliaire audio (jack 3.5 mm)',
            'Batterie interne (remplaçable)',
            'Chargeur allume-cigare',
            'Connecteur magnétique',
            'Adaptateur SIM / Ejecteur SIM'
        ];

        // Compatibilités
        $compatibilities = [
            'Android universel',
            'iPhone (Lightning)',
            'Type-C universel',
            'Micro-USB universel',
            'Infinix / Tecno / Itel',
            'Samsung Galaxy',
            'iPhone 11 à 15',
            'Huawei Y & P series',
            'Redmi / Poco',
            'Oppo A & F series',
            'Vivo Y series',
            'Nokia C & G series',
            'Lenovo Tab',
            'LG G & K series',
            'OnePlus Nord / 8 / 9',
            'Realme C / Narzo',
            'Honor Magic / X',
            'Google Pixel (4 à 8)',
            'Motorola Moto G / E',
            'Sony Xperia',
            'Ulefone Armor',
            'Doogee S series',
            'Blackview BV series',
            'Wiko Sunny / Jerry / Y',
            'iPad (toutes générations)',
            'Tablettes Android 10"',
            'Smartwatch universelle',
            'Accessoires audio Bluetooth 5.0',
            'Casques jack 3.5 mm',
            'Appareils à touches (Itel, Nokia 105, Tecno T series)'
        ];

        // Formats
        $formats = ['tactile', 'à touches', 'tablette Android'];

        // Créer des produits de référence pour les marques et gammes (téléphones)
        foreach ($brands as $brand) {
            $ranges = $rangesByBrand[$brand] ?? [];
            
            if (!empty($ranges)) {
                foreach ($ranges as $range) {
                    // Vérifier si le produit existe déjà
                    $exists = DB::table('products')
                        ->where('brand', $brand)
                        ->where('range', $range)
                        ->where('status', 'draft')
                        ->exists();
                    
                    if (!$exists) {
                        DB::table('products')->insert([
                            'name' => "$brand $range - Référence",
                            'slug' => Str::slug("$brand $range reference"),
                            'description' => "Produit de référence pour $brand $range",
                            'price' => 0,
                            'stock_quantity' => 0,
                            'category_id' => $phoneCategoryId,
                            'status' => 'draft',
                            'brand' => $brand,
                            'range' => $range,
                            'format' => 'tactile',
                            'sku' => 'REF-' . strtoupper(Str::random(8)),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            } else {
                // Créer un produit de référence pour la marque seule
                $exists = DB::table('products')
                    ->where('brand', $brand)
                    ->whereNull('range')
                    ->where('status', 'draft')
                    ->exists();
                
                if (!$exists) {
                    DB::table('products')->insert([
                        'name' => "$brand - Référence",
                        'slug' => Str::slug("$brand reference"),
                        'description' => "Produit de référence pour $brand",
                        'price' => 0,
                        'stock_quantity' => 0,
                        'category_id' => $phoneCategoryId,
                        'status' => 'draft',
                        'brand' => $brand,
                        'format' => 'tactile',
                        'sku' => 'REF-' . strtoupper(Str::random(8)),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // Créer des produits de référence pour les types d'accessoires
        foreach ($accessoryTypes as $type) {
            $exists = DB::table('products')
                ->where('type_accessory', $type)
                ->where('status', 'draft')
                ->exists();
            
            if (!$exists) {
                DB::table('products')->insert([
                    'name' => "$type - Référence",
                    'slug' => Str::slug("$type reference"),
                    'description' => "Accessoire de référence : $type",
                    'price' => 0,
                    'stock_quantity' => 0,
                    'category_id' => $accessoryCategoryId,
                    'status' => 'draft',
                    'type_accessory' => $type,
                    'compatibility' => 'Android universel',
                    'sku' => 'REF-' . strtoupper(Str::random(8)),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Créer des produits de référence pour les compatibilités
        foreach ($compatibilities as $compatibility) {
            $exists = DB::table('products')
                ->where('compatibility', $compatibility)
                ->where('status', 'draft')
                ->exists();
            
            if (!$exists) {
                DB::table('products')->insert([
                    'name' => "Accessoire compatible $compatibility - Référence",
                    'slug' => Str::slug("accessoire $compatibility reference"),
                    'description' => "Accessoire de référence compatible avec $compatibility",
                    'price' => 0,
                    'stock_quantity' => 0,
                    'category_id' => $accessoryCategoryId,
                    'status' => 'draft',
                    'type_accessory' => 'Adaptateur Type-C / Micro USB',
                    'compatibility' => $compatibility,
                    'sku' => 'REF-' . strtoupper(Str::random(8)),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Créer des produits de référence pour les formats
        foreach ($formats as $format) {
            $exists = DB::table('products')
                ->where('format', $format)
                ->where('status', 'draft')
                ->exists();
            
            if (!$exists) {
                DB::table('products')->insert([
                    'name' => "Téléphone $format - Référence",
                    'slug' => Str::slug("telephone $format reference"),
                    'description' => "Produit de référence format $format",
                    'price' => 0,
                    'stock_quantity' => 0,
                    'category_id' => $phoneCategoryId,
                    'status' => 'draft',
                    'brand' => 'Tecno',
                    'format' => $format,
                    'sku' => 'REF-' . strtoupper(Str::random(8)),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info('✅ Données de référence chargées avec succès !');
        $this->command->info('   - ' . count($brands) . ' marques');
        $this->command->info('   - ' . count($accessoryTypes) . ' types d\'accessoires');
        $this->command->info('   - ' . count($compatibilities) . ' compatibilités');
        $this->command->info('   - ' . count($formats) . ' formats');
    }
}
