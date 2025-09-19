<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductType;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::all();
        $productTypes = ProductType::all();

        $products = [
            [
                'name' => 'iPhone 15 Pro',
                'description' => 'Le dernier iPhone avec puce A17 Pro et caméra 48MP',
                'price' => 899000,
                'cost_price' => 750000,
                'stock_quantity' => 50,
                'min_stock_alert' => 5,
                'sku' => 'IPH15PRO-128',
                'barcode' => '1234567890123',
                'images' => ['https://via.placeholder.com/400x300/1E3A8A/FFFFFF?text=iPhone+15+Pro'],
                'status' => 'active',
                'is_featured' => true,
                'meta_title' => 'iPhone 15 Pro - Smartphone Apple',
                'meta_description' => 'Découvrez le iPhone 15 Pro avec ses performances exceptionnelles',
                'tags' => ['smartphone', 'apple', 'premium', '5g'],
            ],
            [
                'name' => 'Samsung Galaxy S24',
                'description' => 'Smartphone Android haut de gamme avec IA intégrée',
                'price' => 799000,
                'cost_price' => 650000,
                'stock_quantity' => 30,
                'min_stock_alert' => 5,
                'sku' => 'SGS24-256',
                'barcode' => '1234567890124',
                'images' => ['https://via.placeholder.com/400x300/60A5FA/FFFFFF?text=Galaxy+S24'],
                'status' => 'active',
                'is_featured' => true,
                'meta_title' => 'Samsung Galaxy S24 - Smartphone Android',
                'meta_description' => 'Samsung Galaxy S24 avec IA et performances exceptionnelles',
                'tags' => ['smartphone', 'samsung', 'android', 'ia'],
            ],
            [
                'name' => 'MacBook Air M2',
                'description' => 'Ordinateur portable Apple avec puce M2 et écran 13 pouces',
                'price' => 1299000,
                'cost_price' => 1100000,
                'stock_quantity' => 15,
                'min_stock_alert' => 3,
                'sku' => 'MBA-M2-256',
                'barcode' => '1234567890125',
                'images' => ['https://via.placeholder.com/400x300/1E3A8A/FFFFFF?text=MacBook+Air+M2'],
                'status' => 'active',
                'is_featured' => true,
                'meta_title' => 'MacBook Air M2 - Ordinateur portable Apple',
                'meta_description' => 'MacBook Air avec puce M2 pour des performances exceptionnelles',
                'tags' => ['ordinateur', 'apple', 'macbook', 'm2'],
            ],
            [
                'name' => 'T-shirt Allo Mobile',
                'description' => 'T-shirt en coton 100% avec logo Allo Mobile',
                'price' => 15000,
                'cost_price' => 8000,
                'stock_quantity' => 100,
                'min_stock_alert' => 10,
                'sku' => 'TSH-AM-BLK-M',
                'barcode' => '1234567890126',
                'images' => ['https://via.placeholder.com/400x300/60A5FA/FFFFFF?text=T-shirt+Allo'],
                'status' => 'active',
                'is_featured' => false,
                'meta_title' => 'T-shirt Allo Mobile - Vêtement officiel',
                'meta_description' => 'T-shirt officiel Allo Mobile en coton de qualité',
                'tags' => ['vetement', 'tshirt', 'allo-mobile', 'cotton'],
            ],
            [
                'name' => 'Toyota Corolla 2024',
                'description' => 'Voiture compacte fiable et économique',
                'price' => 15000000,
                'cost_price' => 13000000,
                'stock_quantity' => 2,
                'min_stock_alert' => 1,
                'sku' => 'TC-2024-AUTO',
                'barcode' => '1234567890127',
                'images' => ['https://via.placeholder.com/400x300/1E3A8A/FFFFFF?text=Toyota+Corolla'],
                'status' => 'active',
                'is_featured' => true,
                'meta_title' => 'Toyota Corolla 2024 - Voiture compacte',
                'meta_description' => 'Toyota Corolla 2024, voiture fiable et économique',
                'tags' => ['voiture', 'toyota', 'corolla', '2024'],
            ],
        ];

        foreach ($products as $productData) {
            $product = Product::create([
                'name' => $productData['name'],
                'description' => $productData['description'],
                'price' => $productData['price'],
                'cost_price' => $productData['cost_price'],
                'stock_quantity' => $productData['stock_quantity'],
                'min_stock_alert' => $productData['min_stock_alert'],
                'category_id' => $categories->random()->id,
                'product_type_id' => $productTypes->random()->id,
                'sku' => $productData['sku'],
                'barcode' => $productData['barcode'],
                'images' => $productData['images'],
                'status' => $productData['status'],
                'is_featured' => $productData['is_featured'],
                'meta_title' => $productData['meta_title'],
                'meta_description' => $productData['meta_description'],
                'tags' => $productData['tags'],
            ]);
        }

        $this->command->info('✅ Produits créés avec succès !');
    }
}
