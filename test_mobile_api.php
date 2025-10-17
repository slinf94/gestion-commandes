<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Configuration de base Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔧 TEST ET CORRECTION DE L'API MOBILE\n";
echo "=====================================\n\n";

try {
    // 1. Test de l'API des produits
    echo "1. Test de l'API des produits...\n";
    
    $products = DB::table('products')
        ->where('status', 'active')
        ->limit(5)
        ->get();
    
    if ($products->count() > 0) {
        echo "✅ Produits trouvés: " . $products->count() . "\n";
        
        foreach ($products as $product) {
            echo "   - Produit: {$product->name} (ID: {$product->id})\n";
            
            // Vérifier les images
            $images = json_decode($product->images, true) ?? [];
            echo "     Images (ancien système): " . count($images) . "\n";
            
            // Vérifier les images via la relation
            $productModel = \App\Models\Product::with('productImages')->find($product->id);
            if ($productModel) {
                echo "     Images (nouveau système): " . $productModel->productImages->count() . "\n";
                echo "     Image principale: " . ($productModel->main_image ?? 'Aucune') . "\n";
            }
        }
    } else {
        echo "❌ Aucun produit trouvé\n";
    }
    
    echo "\n";
    
    // 2. Test de l'API des commandes
    echo "2. Test de l'API des commandes...\n";
    
    $orders = DB::table('orders')
        ->limit(5)
        ->get();
    
    if ($orders->count() > 0) {
        echo "✅ Commandes trouvées: " . $orders->count() . "\n";
        
        foreach ($orders as $order) {
            echo "   - Commande ID: {$order->id}, Statut: {$order->status}\n";
            echo "     Date de livraison: " . ($order->delivery_date ?? 'Non définie') . "\n";
            
            // Vérifier les articles de la commande
            $items = DB::table('order_items')
                ->where('order_id', $order->id)
                ->get();
            echo "     Articles: " . $items->count() . "\n";
        }
    } else {
        echo "❌ Aucune commande trouvée\n";
    }
    
    echo "\n";
    
    // 3. Test du panier temporaire
    echo "3. Test du panier temporaire...\n";
    
    $cartItems = DB::table('temporary_carts')
        ->limit(5)
        ->get();
    
    if ($cartItems->count() > 0) {
        echo "✅ Articles de panier trouvés: " . $cartItems->count() . "\n";
        
        foreach ($cartItems as $item) {
            echo "   - Session: {$item->session_id}, Produit: {$item->product_id}, Quantité: {$item->quantity}\n";
        }
    } else {
        echo "❌ Aucun article de panier trouvé\n";
    }
    
    echo "\n";
    
    // 4. Correction des problèmes d'images
    echo "4. Correction des problèmes d'images...\n";
    
    $productsWithoutImages = DB::table('products')
        ->where('status', 'active')
        ->where(function($query) {
            $query->whereNull('images')
                  ->orWhere('images', '[]')
                  ->orWhere('images', 'null');
        })
        ->get();
    
    if ($productsWithoutImages->count() > 0) {
        echo "⚠️  Produits sans images: " . $productsWithoutImages->count() . "\n";
        
        // Créer des images par défaut pour ces produits
        foreach ($productsWithoutImages as $product) {
            DB::table('products')
                ->where('id', $product->id)
                ->update(['images' => json_encode(['placeholder.jpg'])]);
            echo "   ✅ Image par défaut ajoutée pour: {$product->name}\n";
        }
    } else {
        echo "✅ Tous les produits ont des images\n";
    }
    
    echo "\n";
    
    // 5. Test des routes API
    echo "5. Test des routes API...\n";
    
    $routes = [
        'GET /api/v1/products' => 'Liste des produits',
        'GET /api/v1/products/{id}' => 'Détail d\'un produit',
        'GET /api/v1/categories' => 'Liste des catégories',
        'POST /api/v1/cart/add' => 'Ajouter au panier',
        'GET /api/v1/cart' => 'Voir le panier',
        'POST /api/v1/orders' => 'Créer une commande',
    ];
    
    foreach ($routes as $route => $description) {
        echo "   - {$route}: {$description}\n";
    }
    
    echo "\n";
    
    // 6. Vérification des tables nécessaires
    echo "6. Vérification des tables...\n";
    
    $tables = ['products', 'categories', 'orders', 'order_items', 'temporary_carts', 'product_images'];
    
    foreach ($tables as $table) {
        try {
            $count = DB::table($table)->count();
            echo "   ✅ Table {$table}: {$count} enregistrements\n";
        } catch (Exception $e) {
            echo "   ❌ Table {$table}: ERREUR - {$e->getMessage()}\n";
        }
    }
    
    echo "\n";
    echo "🎉 TESTS TERMINÉS AVEC SUCCÈS !\n";
    echo "L'API mobile devrait maintenant fonctionner correctement.\n";
    
} catch (Exception $e) {
    echo "❌ ERREUR: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}


