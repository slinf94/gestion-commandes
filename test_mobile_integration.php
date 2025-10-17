<?php

// Script de test pour vérifier l'intégration mobile-backend
require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Http;

class MobileIntegrationTest
{
    private $baseUrl = 'http://192.168.100.73:8000/api/v1';

    public function testAllEndpoints()
    {
        echo "=== TEST D'INTÉGRATION MOBILE-BACKEND ===\n\n";

        // Test 1: Récupération des produits
        $this->testProductsEndpoint();

        // Test 2: Récupération des produits en vedette
        $this->testFeaturedProductsEndpoint();

        // Test 3: Recherche de produits
        $this->testSearchEndpoint();

        // Test 4: Test d'authentification
        $this->testAuthEndpoint();

        // Test 5: Test du panier
        $this->testCartEndpoint();

        echo "\n=== FIN DES TESTS ===\n";
    }

    private function testProductsEndpoint()
    {
        echo "1. Test des produits...\n";

        try {
            $response = Http::timeout(10)->get($this->baseUrl . '/products');

            if ($response->successful()) {
                $data = $response->json();
                echo "   ✅ Succès: " . count($data['data']) . " produits récupérés\n";

                // Vérifier que les produits ont des images
                $productsWithImages = 0;
                foreach ($data['data'] as $product) {
                    if (!empty($product['main_image']) || !empty($product['images'])) {
                        $productsWithImages++;
                    }
                }
                echo "   📸 Produits avec images: $productsWithImages/" . count($data['data']) . "\n";

                // Vérifier le filtrage des produits épuisés
                $inStockProducts = array_filter($data['data'], function($product) {
                    return $product['stock_quantity'] > 0;
                });
                echo "   📦 Produits en stock: " . count($inStockProducts) . "/" . count($data['data']) . "\n";

            } else {
                echo "   ❌ Erreur: " . $response->status() . " - " . $response->body() . "\n";
            }
        } catch (Exception $e) {
            echo "   ❌ Exception: " . $e->getMessage() . "\n";
        }

        echo "\n";
    }

    private function testFeaturedProductsEndpoint()
    {
        echo "2. Test des produits en vedette...\n";

        try {
            $response = Http::timeout(10)->get($this->baseUrl . '/products/featured');

            if ($response->successful()) {
                $data = $response->json();
                echo "   ✅ Succès: " . count($data['data']) . " produits en vedette\n";

                // Vérifier qu'aucun produit épuisé n'est en vedette
                $outOfStock = array_filter($data['data'], function($product) {
                    return $product['stock_quantity'] <= 0;
                });

                if (empty($outOfStock)) {
                    echo "   ✅ Aucun produit épuisé dans les produits en vedette\n";
                } else {
                    echo "   ⚠️  " . count($outOfStock) . " produits épuisés trouvés dans les produits en vedette\n";
                }

            } else {
                echo "   ❌ Erreur: " . $response->status() . " - " . $response->body() . "\n";
            }
        } catch (Exception $e) {
            echo "   ❌ Exception: " . $e->getMessage() . "\n";
        }

        echo "\n";
    }

    private function testSearchEndpoint()
    {
        echo "3. Test de la recherche...\n";

        try {
            $response = Http::timeout(10)->get($this->baseUrl . '/products/search', [
                'q' => 'phone'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                echo "   ✅ Succès: " . count($data['data']) . " résultats de recherche\n";

                // Vérifier qu'aucun produit épuisé n'est dans les résultats
                $outOfStock = array_filter($data['data'], function($product) {
                    return $product['stock_quantity'] <= 0;
                });

                if (empty($outOfStock)) {
                    echo "   ✅ Aucun produit épuisé dans les résultats de recherche\n";
                } else {
                    echo "   ⚠️  " . count($outOfStock) . " produits épuisés trouvés dans les résultats\n";
                }

            } else {
                echo "   ❌ Erreur: " . $response->status() . " - " . $response->body() . "\n";
            }
        } catch (Exception $e) {
            echo "   ❌ Exception: " . $e->getMessage() . "\n";
        }

        echo "\n";
    }

    private function testAuthEndpoint()
    {
        echo "4. Test de l'authentification...\n";

        try {
            // Test de connexion avec des données de test
            $response = Http::timeout(10)->post($this->baseUrl . '/auth/login', [
                'email' => 'test@example.com',
                'password' => 'password123'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if ($data['success']) {
                    echo "   ✅ Authentification fonctionnelle\n";
                } else {
                    echo "   ⚠️  Authentification échouée: " . ($data['message'] ?? 'Raison inconnue') . "\n";
                }
            } else {
                echo "   ⚠️  Erreur d'authentification: " . $response->status() . "\n";
            }
        } catch (Exception $e) {
            echo "   ❌ Exception: " . $e->getMessage() . "\n";
        }

        echo "\n";
    }

    private function testCartEndpoint()
    {
        echo "5. Test du panier...\n";

        try {
            $sessionId = 'test_' . time();
            $response = Http::timeout(10)->get($this->baseUrl . '/cart', [
                'session_id' => $sessionId
            ]);

            if ($response->successful()) {
                $data = $response->json();
                echo "   ✅ Endpoint panier accessible\n";
                echo "   📦 Panier vide (normal pour une nouvelle session)\n";
            } else {
                echo "   ❌ Erreur panier: " . $response->status() . " - " . $response->body() . "\n";
            }
        } catch (Exception $e) {
            echo "   ❌ Exception: " . $e->getMessage() . "\n";
        }

        echo "\n";
    }
}

// Exécuter les tests
$tester = new MobileIntegrationTest();
$tester->testAllEndpoints();
