<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

// Configuration de base Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔧 CORRECTION DES PROBLÈMES MOBILES\n";
echo "===================================\n\n";

try {
    // 1. Créer une image placeholder si elle n'existe pas
    echo "1. Vérification de l'image placeholder...\n";
    
    $placeholderPath = storage_path('app/public/products/placeholder.jpg');
    if (!file_exists($placeholderPath)) {
        // Créer une image placeholder simple (1x1 pixel transparent)
        $placeholderData = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==');
        file_put_contents($placeholderPath, $placeholderData);
        echo "✅ Image placeholder créée\n";
    } else {
        echo "✅ Image placeholder existe déjà\n";
    }
    
    echo "\n";
    
    // 2. Corriger les produits sans images
    echo "2. Correction des produits sans images...\n";
    
    $productsWithoutImages = DB::table('products')
        ->where('status', 'active')
        ->where(function($query) {
            $query->whereNull('images')
                  ->orWhere('images', '[]')
                  ->orWhere('images', 'null')
                  ->orWhere('images', '');
        })
        ->get();
    
    foreach ($productsWithoutImages as $product) {
        // Ajouter une image par défaut
        $defaultImages = ['products/placeholder.jpg'];
        DB::table('products')
            ->where('id', $product->id)
            ->update(['images' => json_encode($defaultImages)]);
        echo "   ✅ Image par défaut ajoutée pour: {$product->name}\n";
    }
    
    if ($productsWithoutImages->count() == 0) {
        echo "✅ Tous les produits ont des images\n";
    }
    
    echo "\n";
    
    // 3. Corriger les produits avec des images invalides
    echo "3. Correction des images invalides...\n";
    
    $products = DB::table('products')->where('status', 'active')->get();
    $fixedCount = 0;
    
    foreach ($products as $product) {
        $images = json_decode($product->images, true);
        $fixed = false;
        
        if (is_array($images)) {
            $validImages = [];
            foreach ($images as $image) {
                if (!empty($image) && $image !== 'null' && $image !== '') {
                    $validImages[] = $image;
                }
            }
            
            // Si aucune image valide, ajouter placeholder
            if (empty($validImages)) {
                $validImages = ['products/placeholder.jpg'];
                $fixed = true;
            }
            
            if ($fixed || count($validImages) !== count($images)) {
                DB::table('products')
                    ->where('id', $product->id)
                    ->update(['images' => json_encode($validImages)]);
                $fixedCount++;
                echo "   ✅ Images corrigées pour: {$product->name}\n";
            }
        }
    }
    
    if ($fixedCount == 0) {
        echo "✅ Toutes les images sont valides\n";
    }
    
    echo "\n";
    
    // 4. Créer des données de test pour le panier
    echo "4. Création de données de test pour le panier...\n";
    
    // Nettoyer les anciens paniers expirés
    DB::table('temporary_carts')->where('expires_at', '<', now())->delete();
    
    // Créer un panier de test
    $testSessionId = 'test_mobile_' . time();
    $products = DB::table('products')->where('status', 'active')->limit(3)->get();
    
    foreach ($products as $product) {
        DB::table('temporary_carts')->insert([
            'session_id' => $testSessionId,
            'product_id' => $product->id,
            'quantity' => rand(1, 3),
            'unit_price' => $product->price,
            'expires_at' => now()->addHours(24),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
    
    echo "✅ Panier de test créé avec session: {$testSessionId}\n";
    echo "\n";
    
    // 5. Test de l'API avec les corrections
    echo "5. Test de l'API des produits...\n";
    
    $testProduct = DB::table('products')->where('status', 'active')->first();
    if ($testProduct) {
        $images = json_decode($testProduct->images, true);
        echo "   ✅ Produit test: {$testProduct->name}\n";
        echo "   ✅ Images: " . count($images) . "\n";
        foreach ($images as $image) {
            echo "      - {$image}\n";
        }
    }
    
    echo "\n";
    
    // 6. Vérification des timeouts
    echo "6. Configuration des timeouts...\n";
    
    // Vérifier la configuration de la base de données
    $dbTimeout = DB::select("SELECT @@wait_timeout as wait_timeout, @@interactive_timeout as interactive_timeout")[0];
    echo "   ✅ Timeout base de données: {$dbTimeout->wait_timeout}s\n";
    
    echo "\n";
    
    // 7. Création d'un script de test pour l'API mobile
    echo "7. Création d'un script de test API mobile...\n";
    
    $testScript = '<?php
// Test API Mobile
$baseUrl = "http://192.168.100.73:8000/api/v1";

// Headers pour l\'application mobile
$headers = [
    "Accept: application/json",
    "Content-Type: application/json",
    "X-Mobile-App: true"
];

// Test des produits
echo "Test des produits:\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . "/products");
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Code HTTP: $httpCode\n";
if ($httpCode == 200) {
    $data = json_decode($response, true);
    if ($data["success"]) {
        echo "✅ API des produits fonctionne\n";
        echo "✅ Nombre de produits: " . count($data["data"]) . "\n";
        if (!empty($data["data"])) {
            $firstProduct = $data["data"][0];
            echo "✅ Premier produit: " . $firstProduct["name"] . "\n";
            echo "✅ Image principale: " . ($firstProduct["main_image"] ?? "Aucune") . "\n";
        }
    } else {
        echo "❌ Erreur API: " . ($data["message"] ?? "Inconnue") . "\n";
    }
} else {
    echo "❌ Erreur HTTP: $httpCode\n";
}

echo "\nTest terminé.\n";
?>';
    
    file_put_contents('test_mobile_api_simple.php', $testScript);
    echo "✅ Script de test créé: test_mobile_api_simple.php\n";
    
    echo "\n";
    echo "🎉 CORRECTION TERMINÉE AVEC SUCCÈS !\n";
    echo "=====================================\n";
    echo "✅ Images placeholder créées\n";
    echo "✅ Produits sans images corrigés\n";
    echo "✅ Images invalides corrigées\n";
    echo "✅ Panier de test créé\n";
    echo "✅ Script de test API créé\n";
    echo "\nPour tester l'API mobile, exécutez:\n";
    echo "php test_mobile_api_simple.php\n";
    
} catch (Exception $e) {
    echo "❌ ERREUR: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}


