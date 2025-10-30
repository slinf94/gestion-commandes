<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\User;
use App\Models\Product;
use App\Enums\OrderStatus;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrderTestSeeder extends Seeder
{
    /**
     * Nombre de commandes à créer
     */
    public $totalOrders = 15;

    /**
     * Run the database seeds.
     *
     * Crée des commandes de test avec différents statuts pour tester le système
     */
    public function run(): void
    {
        $this->command->info('');
        $this->command->info('=== CRÉATION DES COMMANDES DE TEST ===');
        $this->command->info('');

        // Récupérer les clients actifs
        $clients = User::where('role', 'client')
            ->where('status', 'active')
            ->get();

        if ($clients->isEmpty()) {
            $this->command->warn('⚠️  Aucun client trouvé. Créez d\'abord des clients avant de créer des commandes.');
            $this->command->info('');
            return;
        }

        // Récupérer les produits actifs avec stock disponible
        $products = Product::where('status', 'active')
            ->where('stock_quantity', '>', 0)
            ->get();

        if ($products->isEmpty()) {
            $this->command->warn('⚠️  Aucun produit avec stock disponible trouvé.');
            $this->command->info('');
            return;
        }

        $this->command->info("📦 Clients disponibles: {$clients->count()}");
        $this->command->info("📦 Produits disponibles: {$products->count()}");
        $this->command->info('');

        // Statuts à tester
        $statuses = [
            OrderStatus::PENDING,
            OrderStatus::CONFIRMED,
            OrderStatus::PROCESSING,
            OrderStatus::SHIPPED,
            OrderStatus::DELIVERED,
            OrderStatus::CANCELLED,
        ];

        $ordersCreated = 0;
        $totalOrders = $this->totalOrders; // Nombre de commandes à créer

        for ($i = 0; $i < $totalOrders; $i++) {
            try {
                DB::beginTransaction();

                // Sélectionner un client aléatoire
                $client = $clients->random();

                // Sélectionner 1 à 4 produits aléatoires
                $selectedProducts = $products->random(min(4, $products->count()));

                // Calculer les totaux
                $subtotal = 0;
                $items = [];

                foreach ($selectedProducts as $product) {
                    $quantity = rand(1, min(3, $product->stock_quantity)); // Quantité entre 1 et 3 (max stock disponible)
                    $unitPrice = $product->price;
                    $totalPrice = $quantity * $unitPrice;
                    $subtotal += $totalPrice;

                    $items[] = [
                        'product' => $product,
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'total_price' => $totalPrice,
                    ];
                }

                // Générer des frais aléatoires
                $taxAmount = rand(0, 5000);
                $shippingCost = rand(1000, 5000);
                $discountAmount = rand(0, 10000);
                $totalAmount = $subtotal + $taxAmount + $shippingCost - $discountAmount;

                // Sélectionner un statut aléatoire
                $status = $statuses[array_rand($statuses)];

                // Générer des dates aléatoires dans les 30 derniers jours
                $createdAt = Carbon::now()->subDays(rand(0, 30))->subHours(rand(0, 23))->subMinutes(rand(0, 59));

                // Adresse de livraison basée sur le client
                $deliveryAddress = [
                    'street' => $client->localisation ?? 'Rue ' . rand(1, 200),
                    'city' => 'Douala',
                    'country' => 'Cameroun',
                ];

                // Créer la commande
                $order = Order::create([
                    'user_id' => $client->id,
                    'status' => $status,
                    'subtotal' => $subtotal,
                    'tax_amount' => $taxAmount,
                    'discount_amount' => $discountAmount,
                    'shipping_cost' => $shippingCost,
                    'total_amount' => $totalAmount,
                    'delivery_address' => $deliveryAddress,
                    'delivery_date' => Carbon::now()->addDays(rand(1, 7))->format('Y-m-d'),
                    'delivery_time_slot' => $this->getRandomTimeSlot(),
                    'notes' => $this->getRandomNote(),
                    'admin_notes' => rand(0, 1) ? $this->getRandomAdminNote() : null,
                    'processed_by' => rand(0, 1) ? User::where('role', 'admin')->first()?->id : null,
                    'processed_at' => $status !== OrderStatus::PENDING ? Carbon::now()->subDays(rand(0, 10)) : null,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);

                // Créer les articles de commande
                foreach ($items as $item) {
                    $product = $item['product'];

                    // Récupérer l'image principale
                    $mainImage = 'products/placeholder.svg';
                    if ($product->images && is_array($product->images) && count($product->images) > 0) {
                        $firstImage = $product->images[0];
                        if (is_string($firstImage) && !empty($firstImage)) {
                            $mainImage = $firstImage;
                        }
                    }

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'total_price' => $item['total_price'],
                        'product_name' => $product->name,
                        'product_image' => $mainImage,
                        'product_sku' => $product->sku,
                        'product_stock' => $product->stock_quantity,
                        'product_details' => [
                            'name' => $product->name,
                            'sku' => $product->sku,
                            'description' => $product->description,
                            'category_id' => $product->category_id,
                            'category_name' => $product->category->name ?? null,
                        ],
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ]);

                    // Pour les tests, on ne diminue pas vraiment le stock (pour éviter de vider le stock)
                    // Si vous voulez simuler vraiment, décommentez la ligne suivante :
                    // $product->decrement('stock_quantity', $item['quantity']);
                }

                // Créer l'historique des statuts
                $historyMessages = [
                    'Commande créée par le client',
                    'Commande reçue avec succès',
                    'Commande confirmée par l\'administrateur',
                    'Commande en cours de traitement',
                    'Commande expédiée',
                    'Commande livrée',
                    'Commande annulée',
                ];

                OrderStatusHistory::create([
                    'order_id' => $order->id,
                    'new_status' => $status,
                    'comment' => $historyMessages[array_rand($historyMessages)],
                    'changed_by' => $client->id,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);

                // Ajouter un historique supplémentaire pour les commandes non-pending
                if ($status !== OrderStatus::PENDING) {
                    $previousStatus = OrderStatus::PENDING;
                    OrderStatusHistory::create([
                        'order_id' => $order->id,
                        'previous_status' => $previousStatus,
                        'new_status' => $status,
                        'comment' => 'Statut modifié par l\'administrateur',
                        'changed_by' => User::where('role', 'admin')->first()?->id ?? $client->id,
                        'created_at' => $createdAt->addHours(rand(1, 24)),
                        'updated_at' => $createdAt,
                    ]);
                }

                DB::commit();
                $ordersCreated++;

                $this->command->info("✅ Commande #{$order->id} créée - Client: {$client->full_name} - Statut: {$status->value} - Total: " . number_format($totalAmount, 0, ',', ' ') . " FCFA");

            } catch (\Exception $e) {
                DB::rollBack();
                $this->command->error("❌ Erreur lors de la création de la commande: {$e->getMessage()}");
            }
        }

        $this->command->info('');
        $this->command->info("✅ {$ordersCreated} commandes de test créées avec succès!");
        $this->command->info('');
        $this->command->info('📋 STATISTIQUES DES COMMANDES:');

        foreach ($statuses as $status) {
            $count = Order::where('status', $status)->count();
            $this->command->info("   - {$status->value}: {$count} commande(s)");
        }

        $this->command->info('');
        $this->command->info('🎉 Vous pouvez maintenant tester votre système!');
        $this->command->info('   Allez sur: /admin/orders pour voir toutes les commandes');
        $this->command->info('');
    }

    /**
     * Retourne un créneau horaire aléatoire
     */
    private function getRandomTimeSlot(): string
    {
        $slots = [
            '8h-10h',
            '10h-12h',
            '12h-14h',
            '14h-16h',
            '16h-18h',
            '18h-20h',
        ];

        return $slots[array_rand($slots)];
    }

    /**
     * Retourne une note client aléatoire
     */
    private function getRandomNote(): ?string
    {
        $notes = [
            'Livrer le matin si possible',
            'Appeler avant de livrer',
            'Laisser chez le voisin si absent',
            'Livrer à l\'adresse indiquée',
            'Merci de livrer rapidement',
            'Contactez-moi au numéro indiqué',
            null,
            null,
        ];

        return $notes[array_rand($notes)];
    }

    /**
     * Retourne une note admin aléatoire
     */
    private function getRandomAdminNote(): string
    {
        $notes = [
            'Client prioritaire',
            'Vérifier le stock avant livraison',
            'Client régulier - fiable',
            'Première commande - suivre de près',
            'Commande urgente',
            'Vérifier l\'adresse de livraison',
        ];

        return $notes[array_rand($notes)];
    }
}

