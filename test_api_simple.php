<?php

// Test simple des endpoints API
$baseUrl = 'http://192.168.100.73:8000/api/v1';

echo "=== TEST DES ENDPOINTS API ===\n\n";

// Test 1: Produits
echo "1. Test des produits...\n";
$response = file_get_contents($baseUrl . '/products');
if ($response !== false) {
    $data = json_decode($response, true);
    if ($data && isset($data['data'])) {
        echo "   ✅ Succès: " . count($data['data']) . " produits récupérés\n";

        // Vérifier les images
        $withImages = 0;
        foreach ($data['data'] as $product) {
            if (!empty($product['main_image']) || !empty($product['images'])) {
                $withImages++;
            }
        }
        echo "   📸 Produits avec images: $withImages/" . count($data['data']) . "\n";

        // Vérifier le stock
        $inStock = 0;
        foreach ($data['data'] as $product) {
            if ($product['stock_quantity'] > 0) {
                $inStock++;
            }
        }
        echo "   📦 Produits en stock: $inStock/" . count($data['data']) . "\n";
    } else {
        echo "   ❌ Format de réponse invalide\n";
    }
} else {
    echo "   ❌ Erreur de connexion\n";
}

echo "\n";

// Test 2: Produits en vedette
echo "2. Test des produits en vedette...\n";
$response = file_get_contents($baseUrl . '/products/featured');
if ($response !== false) {
    $data = json_decode($response, true);
    if ($data && isset($data['data'])) {
        echo "   ✅ Succès: " . count($data['data']) . " produits en vedette\n";

        // Vérifier qu'aucun produit épuisé
        $outOfStock = 0;
        foreach ($data['data'] as $product) {
            if ($product['stock_quantity'] <= 0) {
                $outOfStock++;
            }
        }
        echo "   📦 Produits épuisés dans les vedettes: $outOfStock\n";
    } else {
        echo "   ❌ Format de réponse invalide\n";
    }
} else {
    echo "   ❌ Erreur de connexion\n";
}

echo "\n";

// Test 3: Recherche
echo "3. Test de la recherche...\n";
$response = file_get_contents($baseUrl . '/products/search?q=phone');
if ($response !== false) {
    $data = json_decode($response, true);
    if ($data && isset($data['data'])) {
        echo "   ✅ Succès: " . count($data['data']) . " résultats de recherche\n";

        // Vérifier qu'aucun produit épuisé
        $outOfStock = 0;
        foreach ($data['data'] as $product) {
            if ($product['stock_quantity'] <= 0) {
                $outOfStock++;
            }
        }
        echo "   📦 Produits épuisés dans les résultats: $outOfStock\n";
    } else {
        echo "   ❌ Format de réponse invalide\n";
    }
} else {
    echo "   ❌ Erreur de connexion\n";
}

echo "\n=== FIN DES TESTS ===\n";
