<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use App\Models\OrderItem;
use App\Enums\OrderStatus;

class TestOrdersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "=== CRÉATION DE COMMANDES DE TEST ===\n\n";

        // Récupérer un utilisateur existant ou en créer un
        $user = User::where('role', 'client')->first();
        if (!$user) {
            echo "❌ Aucun utilisateur client trouvé. Création d'un utilisateur de test...\n";
            $user = User::create([
                'nom' => 'Test',
                'prenom' => 'User',
                'email' => 'test@example.com',
                'password' => bcrypt('password'),
                'numero_telephone' => '+22612345678',
                'role' => 'client',
                'status' => 'active',
                'email_verified_at' => now(),
            ]);
            echo "✅ Utilisateur créé: {$user->email}\n";
        } else {
            echo "✅ Utilisateur trouvé: {$user->email}\n";
        }

        // Récupérer des produits existants
        $products = Product::limit(3)->get();
        if ($products->isEmpty()) {
            echo "❌ Aucun produit trouvé. Création de produits de test...\n";
            $products = collect([
                Product::create([
                    'name' => 'Produit Test 1',
                    'description' => 'Description du produit test 1',
                    'price' => 10000,
                    'sku' => 'TEST-001',
                    'stock_quantity' => 100,
                    'status' => 'active',
                    'category_id' => 1,
                ]),
                Product::create([
                    'name' => 'Produit Test 2',
                    'description' => 'Description du produit test 2',
                    'price' => 15000,
                    'sku' => 'TEST-002',
                    'stock_quantity' => 50,
                    'status' => 'active',
                    'category_id' => 1,
                ]),
                Product::create([
                    'name' => 'Produit Test 3',
                    'description' => 'Description du produit test 3',
                    'price' => 20000,
                    'sku' => 'TEST-003',
                    'stock_quantity' => 25,
                    'status' => 'active',
                    'category_id' => 1,
                ]),
            ]);
            echo "✅ Produits créés\n";
        }

        // Créer des commandes de test
        $statuses = [OrderStatus::PENDING, OrderStatus::CONFIRMED, OrderStatus::PROCESSING, OrderStatus::SHIPPED];

        for ($i = 1; $i <= 5; $i++) {
            $order = Order::create([
                'order_number' => 'TEST-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'user_id' => $user->id,
                'status' => $statuses[array_rand($statuses)],
                'subtotal' => 0,
                'tax_amount' => 0,
                'discount_amount' => 0,
                'shipping_cost' => 1000,
                'total_amount' => 0,
                'delivery_address' => [
                    'street' => 'Rue Test ' . $i,
                    'city' => 'Ouagadougou',
                    'country' => 'Burkina Faso'
                ],
                'notes' => 'Commande de test ' . $i,
            ]);

            // Ajouter des articles à la commande
            $subtotal = 0;
            $selectedProducts = $products->random(rand(1, 3));

            foreach ($selectedProducts as $product) {
                $quantity = rand(1, 3);
                $unitPrice = $product->price;
                $totalPrice = $unitPrice * $quantity;
                $subtotal += $totalPrice;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_image' => $product->mainImage ?? '',
                    'product_sku' => $product->sku,
                    'product_stock' => $product->stock_quantity,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                ]);
            }

            // Mettre à jour le total de la commande
            $order->update([
                'subtotal' => $subtotal,
                'total_amount' => $subtotal + $order->shipping_cost,
            ]);

            echo "✅ Commande créée: #{$order->order_number} - {$order->getStatusLabel()} - {$order->total_amount} FCFA\n";
        }

        echo "\n=== COMMANDES DE TEST CRÉÉES AVEC SUCCÈS ===\n";
        echo "Nombre total de commandes: " . Order::count() . "\n";
    }
}
