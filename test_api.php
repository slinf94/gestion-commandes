<?php

// Script de test simple pour vérifier les endpoints API
$baseUrl = 'http://localhost:8000/api/v1';

echo "=== TEST DES ENDPOINTS API ===\n\n";

// Test 1: Récupérer les catégories (endpoint public)
echo "1. Test des catégories...\n";
$response = file_get_contents($baseUrl . '/categories');
$data = json_decode($response, true);
echo "Status: " . ($data['success'] ? 'SUCCESS' : 'FAILED') . "\n";
echo "Message: " . $data['message'] . "\n";
echo "Nombre de catégories: " . count($data['data']) . "\n\n";

// Test 2: Récupérer les produits (endpoint public)
echo "2. Test des produits...\n";
$response = file_get_contents($baseUrl . '/products');
$data = json_decode($response, true);
echo "Status: " . ($data['success'] ? 'SUCCESS' : 'FAILED') . "\n";
echo "Message: " . $data['message'] . "\n";
echo "Nombre de produits: " . count($data['data']) . "\n\n";

// Test 3: Recherche de produits
echo "3. Test de recherche de produits...\n";
$response = file_get_contents($baseUrl . '/products/search?q=test');
$data = json_decode($response, true);
echo "Status: " . ($data['success'] ? 'SUCCESS' : 'FAILED') . "\n";
echo "Message: " . $data['message'] . "\n";
echo "Résultats de recherche: " . count($data['data']) . "\n\n";

// Test 4: Produits en vedette
echo "4. Test des produits en vedette...\n";
$response = file_get_contents($baseUrl . '/products/featured');
$data = json_decode($response, true);
echo "Status: " . ($data['success'] ? 'SUCCESS' : 'FAILED') . "\n";
echo "Message: " . $data['message'] . "\n";
echo "Produits en vedette: " . count($data['data']) . "\n\n";

echo "=== TESTS TERMINÉS ===\n";





