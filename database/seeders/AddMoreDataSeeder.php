<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
use App\Models\Attribute;
use App\Models\Category as CategoryModel;
use Illuminate\Support\Str;

class AddMoreDataSeeder extends Seeder
{
    public function run(): void
    {
        // Ajouter plus de catégories
        $categories = [
            ['name' => 'Électroménager', 'description' => 'Appareils électroménagers pour la maison'],
            ['name' => 'Bricolage & Outillage', 'description' => 'Outils et matériel de bricolage'],
            ['name' => 'Jardinage', 'description' => 'Plantes, outils de jardin et accessoires'],
            ['name' => 'Cuisine & Maison', 'description' => 'Articles pour la cuisine et la maison'],
            ['name' => 'Décoration', 'description' => 'Objets de décoration pour la maison'],
            ['name' => 'Literie & Bains', 'description' => 'Linge de lit et accessoires de salle de bain'],
            ['name' => 'Éclairage', 'description' => 'Lampes et luminaires'],
            ['name' => 'Meubles', 'description' => 'Mobilier pour la maison et le bureau'],
            ['name' => 'Automobile', 'description' => 'Accessoires et pièces automobiles'],
            ['name' => 'Bijoux & Montres', 'description' => 'Bijoux, montres et accessoires'],
            ['name' => 'Parfums & Cosmétiques', 'description' => 'Parfums et produits cosmétiques'],
            ['name' => 'Optique', 'description' => 'Lunettes et équipements optiques'],
            ['name' => 'Sécurité & Protection', 'description' => 'Systèmes de sécurité et protection'],
            ['name' => 'Bébé & Enfant', 'description' => 'Articles pour bébé et enfants'],
            ['name' => 'Animalerie', 'description' => 'Produits pour les animaux domestiques'],
            ['name' => 'Bureau & Papeterie', 'description' => 'Fournitures de bureau et papeterie'],
            ['name' => 'Livres & Magazines', 'description' => 'Livres, magazines et publications'],
            ['name' => 'Musique & Instruments', 'description' => 'Instruments de musique et accessoires'],
            ['name' => 'Cinéma & DVD', 'description' => 'Films, séries et DVD'],
            ['name' => 'Jeux & Jouets', 'description' => 'Jouets et jeux pour tous âges'],
            ['name' => 'Vélos & Scooters', 'description' => 'Vélos, scooters et accessoires'],
            ['name' => 'Fitness & Musculation', 'description' => 'Équipements de fitness et musculation'],
            ['name' => 'Pêche & Chasse', 'description' => 'Équipements de pêche et chasse'],
            ['name' => 'Randonnée & Camping', 'description' => 'Équipements de randonnée et camping'],
            ['name' => 'Natation', 'description' => 'Équipements de natation et aquatiques'],
            ['name' => 'Ski & Neige', 'description' => 'Équipements de ski et sports d\'hiver'],
            ['name' => 'Apiculture', 'description' => 'Équipements apicoles et produits de la ruche'],
            ['name' => 'Securité Incendie', 'description' => 'Équipements de sécurité incendie'],
            ['name' => 'Téléphonie Fixe', 'description' => 'Téléphones fixes et accessoires'],
            ['name' => 'Câbles & Connectiques', 'description' => 'Câbles et accessoires de connexion'],
        ];

        foreach ($categories as $cat) {
            CategoryModel::updateOrCreate(
                ['name' => $cat['name']],
                array_merge($cat, [
                    'slug' => Str::slug($cat['name']),
                    'is_active' => true,
                    'is_featured' => false,
                    'sort_order' => rand(1, 100)
                ])
            );
        }

        // Ajouter plus d'attributs
        $attributes = [
            ['name' => 'Marque', 'type' => 'text'],
            ['name' => 'Poids', 'type' => 'text'],
            ['name' => 'Dimensions', 'type' => 'text'],
            ['name' => 'Matière', 'type' => 'select'],
            ['name' => 'Style', 'type' => 'select'],
            ['name' => 'Saison', 'type' => 'select'],
            ['name' => 'Genre', 'type' => 'select'],
            ['name' => 'Usage', 'type' => 'select'],
            ['name' => 'Pays d\'origine', 'type' => 'select'],
            ['name' => 'Garantie', 'type' => 'text'],
            ['name' => 'Compatibilité', 'type' => 'text'],
            ['name' => 'Format', 'type' => 'select'],
            ['name' => 'Capacité', 'type' => 'text'],
            ['name' => 'Poids supporté', 'type' => 'text'],
            ['name' => 'Certification', 'type' => 'text'],
            ['name' => 'Type de batterie', 'type' => 'select'],
            ['name' => 'Autonomie', 'type' => 'text'],
            ['name' => 'Résolution', 'type' => 'text'],
            ['name' => 'Puissance', 'type' => 'text'],
            ['name' => 'Vitesse', 'type' => 'text'],
            ['name' => 'Volume', 'type' => 'text'],
            ['name' => 'Température', 'type' => 'text'],
            ['name' => 'Pression', 'type' => 'text'],
            ['name' => 'Débattement', 'type' => 'text'],
            ['name' => 'Technologie', 'type' => 'select'],
            ['name' => 'Connectivité', 'type' => 'select'],
            ['name' => 'Protection IP', 'type' => 'text'],
            ['name' => 'Classe d\'efficacité', 'type' => 'select'],
            ['name' => 'Certification écologique', 'type' => 'select'],
            ['name' => 'Caractéristiques techniques', 'type' => 'text'],
        ];

        foreach ($attributes as $attr) {
            Attribute::updateOrCreate(
                ['name' => $attr['name']],
                [
                    'slug' => Str::slug($attr['name']),
                    'type' => $attr['type'],
                    'is_active' => true,
                ]
            );
        }

        // Ajouter plus de produits
        $products = [
            ['name' => 'iPhone 15 Pro', 'description' => 'Smartphone haut de gamme Apple', 'price' => 1299.99, 'category_id' => CategoryModel::where('name', 'LIKE', '%Téléphonie%')->first()->id ?? 1],
            ['name' => 'Samsung Galaxy S24', 'description' => 'Smartphone Android premium', 'price' => 999.99, 'category_id' => CategoryModel::where('name', 'LIKE', '%Téléphonie%')->first()->id ?? 1],
            ['name' => 'MacBook Pro 16"', 'description' => 'Ordinateur portable professionnel', 'price' => 2499.99, 'category_id' => CategoryModel::where('name', 'LIKE', '%Ordinateurs%')->first()->id ?? 1],
            ['name' => 'Dell XPS 15', 'description' => 'Laptop Dell ultrabook', 'price' => 1799.99, 'category_id' => CategoryModel::where('name', 'LIKE', '%Ordinateurs%')->first()->id ?? 1],
            ['name' => 'LG OLED 55"', 'description' => 'Télévision OLED 4K', 'price' => 1299.99, 'category_id' => CategoryModel::where('name', 'LIKE', '%Télévisions%')->first()->id ?? 1],
            ['name' => 'Sony WH-1000XM5', 'description' => 'Casque sans fil avec réduction de bruit', 'price' => 399.99, 'category_id' => CategoryModel::where('name', 'LIKE', '%Audio%')->first()->id ?? 1],
            ['name' => 'Canon EOS R6', 'description' => 'Appareil photo hybride professionnel', 'price' => 2499.99, 'category_id' => CategoryModel::where('name', 'LIKE', '%Photo%')->first()->id ?? 1],
            ['name' => 'PlayStation 5', 'description' => 'Console de jeux Sony', 'price' => 499.99, 'category_id' => CategoryModel::where('name', 'LIKE', '%Consoles%')->first()->id ?? 1],
            ['name' => 'iPad Pro 12.9"', 'description' => 'Tablette iPad Pro', 'price' => 1099.99, 'category_id' => CategoryModel::where('name', 'LIKE', '%Tablettes%')->first()->id ?? 1],
            ['name' => 'DJI Mini 3 Pro', 'description' => 'Drone avec caméra 4K', 'price' => 899.99, 'category_id' => CategoryModel::where('name', 'LIKE', '%Drones%')->first()->id ?? 1],
            ['name' => 'Apple Watch Series 9', 'description' => 'Montre connectée Apple', 'price' => 399.99, 'category_id' => CategoryModel::where('name', 'LIKE', '%Montres%')->first()->id ?? 1],
            ['name' => 'Samsung Galaxy Tab S9', 'description' => 'Tablette Android Samsung', 'price' => 799.99, 'category_id' => CategoryModel::where('name', 'LIKE', '%Tablettes%')->first()->id ?? 1],
            ['name' => 'Sony WF-1000XM5', 'description' => 'Écouteurs sans fil Sony', 'price' => 299.99, 'category_id' => CategoryModel::where('name', 'LIKE', '%Audio%')->first()->id ?? 1],
            ['name' => 'Nikon D850', 'description' => 'Appareil photo reflex numérique', 'price' => 3299.99, 'category_id' => CategoryModel::where('name', 'LIKE', '%Photo%')->first()->id ?? 1],
            ['name' => 'Xbox Series X', 'description' => 'Console Microsoft', 'price' => 499.99, 'category_id' => CategoryModel::where('name', 'LIKE', '%Consoles%')->first()->id ?? 1],
            ['name' => 'DJI Mavic 3', 'description' => 'Drone professionnel avec caméra Hasselblad', 'price' => 1799.99, 'category_id' => CategoryModel::where('name', 'LIKE', '%Drones%')->first()->id ?? 1],
            ['name' => 'Garmin Fenix 7', 'description' => 'Montre GPS multi-sports', 'price' => 699.99, 'category_id' => CategoryModel::where('name', 'LIKE', '%Montres%')->first()->id ?? 1],
            ['name' => 'iPad Air', 'description' => 'Tablette iPad Air Apple', 'price' => 599.99, 'category_id' => CategoryModel::where('name', 'LIKE', '%Tablettes%')->first()->id ?? 1],
            ['name' => 'Sony A7 IV', 'description' => 'Appareil photo hybride Sony', 'price' => 2499.99, 'category_id' => CategoryModel::where('name', 'LIKE', '%Photo%')->first()->id ?? 1],
            ['name' => 'Nintendo Switch OLED', 'description' => 'Console Nintendo', 'price' => 349.99, 'category_id' => CategoryModel::where('name', 'LIKE', '%Consoles%')->first()->id ?? 1],
            ['name' => 'AirPods Pro 2', 'description' => 'Écouteurs sans fil Apple', 'price' => 249.99, 'category_id' => CategoryModel::where('name', 'LIKE', '%Audio%')->first()->id ?? 1],
            ['name' => 'Samsung Galaxy S23 Ultra', 'description' => 'Smartphone Samsung premium', 'price' => 1199.99, 'category_id' => CategoryModel::where('name', 'LIKE', '%Téléphonie%')->first()->id ?? 1],
            ['name' => 'OnePlus 12', 'description' => 'Smartphone OnePlus', 'price' => 799.99, 'category_id' => CategoryModel::where('name', 'LIKE', '%Téléphonie%')->first()->id ?? 1],
            ['name' => 'Google Pixel 8 Pro', 'description' => 'Smartphone Google', 'price' => 999.99, 'category_id' => CategoryModel::where('name', 'LIKE', '%Téléphonie%')->first()->id ?? 1],
            ['name' => 'Xiaomi 14 Pro', 'description' => 'Smartphone Xiaomi', 'price' => 899.99, 'category_id' => CategoryModel::where('name', 'LIKE', '%Téléphonie%')->first()->id ?? 1],
            ['name' => 'ThinkPad X1 Carbon', 'description' => 'Laptop professionnel Lenovo', 'price' => 1699.99, 'category_id' => CategoryModel::where('name', 'LIKE', '%Ordinateurs%')->first()->id ?? 1],
            ['name' => 'HP Spectre x360', 'description' => 'Laptop 2-en-1 HP', 'price' => 1399.99, 'category_id' => CategoryModel::where('name', 'LIKE', '%Ordinateurs%')->first()->id ?? 1],
            ['name' => 'Asus ROG Zephyrus', 'description' => 'Gaming laptop Asus', 'price' => 1999.99, 'category_id' => CategoryModel::where('name', 'LIKE', '%Ordinateurs%')->first()->id ?? 1],
            ['name' => 'Samsung Neo QLED 65"', 'description' => 'TV QLED Samsung', 'price' => 1699.99, 'category_id' => CategoryModel::where('name', 'LIKE', '%Télévisions%')->first()->id ?? 1],
            ['name' => 'LG C3 OLED 77"', 'description' => 'TV OLED LG grande taille', 'price' => 2999.99, 'category_id' => CategoryModel::where('name', 'LIKE', '%Télévisions%')->first()->id ?? 1],
            ['name' => 'Bose QuietComfort 45', 'description' => 'Casque Bluetooth Bose', 'price' => 329.99, 'category_id' => CategoryModel::where('name', 'LIKE', '%Audio%')->first()->id ?? 1],
            ['name' => 'Fujifilm X-T5', 'description' => 'Appareil photo hybride Fujifilm', 'price' => 1699.99, 'category_id' => CategoryModel::where('name', 'LIKE', '%Photo%')->first()->id ?? 1],
            ['name' => 'DJI Mini 4 Pro', 'description' => 'Drone compact DJI', 'price' => 1099.99, 'category_id' => CategoryModel::where('name', 'LIKE', '%Drones%')->first()->id ?? 1],
            ['name' => 'Fitbit Versa 4', 'description' => 'Montre connectée Fitbit', 'price' => 199.99, 'category_id' => CategoryModel::where('name', 'LIKE', '%Montres%')->first()->id ?? 1],
        ];

        foreach ($products as $product) {
            Product::create([
                'name' => $product['name'],
                'description' => $product['description'],
                'price' => $product['price'],
                'category_id' => $product['category_id'],
                'status' => 'active',
                'sku' => 'SKU-' . strtoupper(Str::random(8)),
                'stock_quantity' => rand(10, 100),
            ]);
        }

        $this->command->info('✅ 30+ catégories créées !');
        $this->command->info('✅ 30+ attributs créés !');
        $this->command->info('✅ 30+ produits créés !');
    }
}

