<?php

/**
 * Script de test pour l'AdminController
 * Teste tous les endpoints d'administration
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\Notification;

// Configuration de base
$baseUrl = 'http://localhost:8000/api/v1';
$adminToken = null;
$testUserId = null;

echo "🧪 TEST DE L'ADMINCONTROLLER - SYSTÈME DE GESTION DE COMMANDES\n";
echo "================================================================\n\n";

/**
 * Fonction pour faire des requêtes HTTP
 */
function makeRequest($url, $method = 'GET', $data = null, $headers = []) {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge([
        'Content-Type: application/json',
        'Accept: application/json'
    ], $headers));

    if ($method === 'POST' || $method === 'PUT') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return [
        'status_code' => $httpCode,
        'body' => json_decode($response, true)
    ];
}

/**
 * Test de connexion admin
 */
function testAdminLogin() {
    global $baseUrl, $adminToken;

    echo "🔐 Test de connexion admin...\n";

    $response = makeRequest("$baseUrl/auth/login", 'POST', [
        'email' => 'admin@example.com',
        'password' => 'password'
    ]);

    if ($response['status_code'] === 200 && $response['body']['success']) {
        $adminToken = $response['body']['data']['token'];
        echo "✅ Connexion admin réussie\n";
        echo "   Token: " . substr($adminToken, 0, 20) . "...\n\n";
        return true;
    } else {
        echo "❌ Échec de la connexion admin\n";
        echo "   Erreur: " . ($response['body']['message'] ?? 'Inconnue') . "\n\n";
        return false;
    }
}

/**
 * Test des statistiques générales
 */
function testStatistics() {
    global $baseUrl, $adminToken;

    echo "📊 Test des statistiques générales...\n";

    $response = makeRequest("$baseUrl/admin/statistics", 'GET', null, [
        "Authorization: Bearer $adminToken"
    ]);

    if ($response['status_code'] === 200 && $response['body']['success']) {
        $stats = $response['body']['data'];
        echo "✅ Statistiques récupérées avec succès\n";
        echo "   Utilisateurs: {$stats['users']['total']}\n";
        echo "   Commandes: {$stats['orders']['total']}\n";
        echo "   Produits: {$stats['products']['total']}\n";
        echo "   Revenus: {$stats['revenue']['total']} FCFA\n\n";
        return true;
    } else {
        echo "❌ Échec de récupération des statistiques\n";
        echo "   Erreur: " . ($response['body']['message'] ?? 'Inconnue') . "\n\n";
        return false;
    }
}

/**
 * Test de la gestion des utilisateurs
 */
function testUserManagement() {
    global $baseUrl, $adminToken, $testUserId;

    echo "👥 Test de la gestion des utilisateurs...\n";

    // Lister les utilisateurs
    $response = makeRequest("$baseUrl/admin/users", 'GET', null, [
        "Authorization: Bearer $adminToken"
    ]);

    if ($response['status_code'] === 200 && $response['body']['success']) {
        $users = $response['body']['data'];
        echo "✅ Liste des utilisateurs récupérée\n";
        echo "   Nombre d'utilisateurs: " . count($users) . "\n";

        if (!empty($users)) {
            $testUserId = $users[0]['id'];
            echo "   Premier utilisateur: {$users[0]['nom']} {$users[0]['prenom']}\n";
        }

        // Détails d'un utilisateur
        if ($testUserId) {
            $response = makeRequest("$baseUrl/admin/users/$testUserId", 'GET', null, [
                "Authorization: Bearer $adminToken"
            ]);

            if ($response['status_code'] === 200 && $response['body']['success']) {
                echo "✅ Détails utilisateur récupérés\n";
            } else {
                echo "❌ Échec de récupération des détails utilisateur\n";
            }
        }

        echo "\n";
        return true;
    } else {
        echo "❌ Échec de récupération des utilisateurs\n";
        echo "   Erreur: " . ($response['body']['message'] ?? 'Inconnue') . "\n\n";
        return false;
    }
}

/**
 * Test des statistiques des commandes
 */
function testOrderStatistics() {
    global $baseUrl, $adminToken;

    echo "📦 Test des statistiques des commandes...\n";

    $response = makeRequest("$baseUrl/admin/statistics/orders?period=month", 'GET', null, [
        "Authorization: Bearer $adminToken"
    ]);

    if ($response['status_code'] === 200 && $response['body']['success']) {
        $stats = $response['body']['data'];
        echo "✅ Statistiques des commandes récupérées\n";
        echo "   Période: {$stats['period']}\n";
        echo "   Répartition par statut: " . count($stats['status_breakdown']) . " statuts\n";
        echo "   Données quotidiennes: " . count($stats['daily_stats']) . " entrées\n";
        echo "   Top clients: " . count($stats['top_clients']) . " clients\n\n";
        return true;
    } else {
        echo "❌ Échec de récupération des statistiques des commandes\n";
        echo "   Erreur: " . ($response['body']['message'] ?? 'Inconnue') . "\n\n";
        return false;
    }
}

/**
 * Test des statistiques des produits
 */
function testProductStatistics() {
    global $baseUrl, $adminToken;

    echo "🛍️ Test des statistiques des produits...\n";

    $response = makeRequest("$baseUrl/admin/statistics/products?period=month", 'GET', null, [
        "Authorization: Bearer $adminToken"
    ]);

    if ($response['status_code'] === 200 && $response['body']['success']) {
        $stats = $response['body']['data'];
        echo "✅ Statistiques des produits récupérées\n";
        echo "   Période: {$stats['period']}\n";
        echo "   Top produits: " . count($stats['top_products']) . " produits\n";
        echo "   Statistiques par catégorie: " . count($stats['category_stats']) . " catégories\n";
        echo "   Alertes de stock: " . count($stats['stock_alerts']) . " alertes\n\n";
        return true;
    } else {
        echo "❌ Échec de récupération des statistiques des produits\n";
        echo "   Erreur: " . ($response['body']['message'] ?? 'Inconnue') . "\n\n";
        return false;
    }
}

/**
 * Test des statistiques des utilisateurs
 */
function testUserStatistics() {
    global $baseUrl, $adminToken;

    echo "👤 Test des statistiques des utilisateurs...\n";

    $response = makeRequest("$baseUrl/admin/statistics/users?period=month", 'GET', null, [
        "Authorization: Bearer $adminToken"
    ]);

    if ($response['status_code'] === 200 && $response['body']['success']) {
        $stats = $response['body']['data'];
        echo "✅ Statistiques des utilisateurs récupérées\n";
        echo "   Période: {$stats['period']}\n";
        echo "   Inscriptions: " . count($stats['registrations']) . " entrées\n";
        echo "   Répartition par rôle: " . count($stats['role_breakdown']) . " rôles\n";
        echo "   Utilisateurs actifs: " . count($stats['most_active_users']) . " utilisateurs\n\n";
        return true;
    } else {
        echo "❌ Échec de récupération des statistiques des utilisateurs\n";
        echo "   Erreur: " . ($response['body']['message'] ?? 'Inconnue') . "\n\n";
        return false;
    }
}

/**
 * Test de l'envoi de notifications
 */
function testSendNotification() {
    global $baseUrl, $adminToken;

    echo "🔔 Test d'envoi de notifications...\n";

    $response = makeRequest("$baseUrl/admin/notifications/send", 'POST', [
        'title' => 'Test de notification',
        'message' => 'Ceci est un test de notification depuis l\'AdminController',
        'type' => 'test',
        'target_users' => ['admins']
    ], [
        "Authorization: Bearer $adminToken"
    ]);

    if ($response['status_code'] === 200 && $response['body']['success']) {
        $data = $response['body']['data'];
        echo "✅ Notification envoyée avec succès\n";
        echo "   Notifications envoyées: {$data['notifications_sent']}\n";
        echo "   Utilisateurs ciblés: " . count($data['target_users']) . "\n\n";
        return true;
    } else {
        echo "❌ Échec d'envoi de notification\n";
        echo "   Erreur: " . ($response['body']['message'] ?? 'Inconnue') . "\n\n";
        return false;
    }
}

/**
 * Test de récupération des notifications
 */
function testGetNotifications() {
    global $baseUrl, $adminToken;

    echo "📬 Test de récupération des notifications...\n";

    $response = makeRequest("$baseUrl/admin/notifications", 'GET', null, [
        "Authorization: Bearer $adminToken"
    ]);

    if ($response['status_code'] === 200 && $response['body']['success']) {
        $notifications = $response['body']['data'];
        echo "✅ Notifications récupérées avec succès\n";
        echo "   Nombre de notifications: " . count($notifications) . "\n\n";
        return true;
    } else {
        echo "❌ Échec de récupération des notifications\n";
        echo "   Erreur: " . ($response['body']['message'] ?? 'Inconnue') . "\n\n";
        return false;
    }
}

/**
 * Test de mise à jour du statut utilisateur
 */
function testUpdateUserStatus() {
    global $baseUrl, $adminToken, $testUserId;

    if (!$testUserId) {
        echo "⚠️ Pas d'utilisateur de test disponible pour le test de statut\n\n";
        return false;
    }

    echo "🔄 Test de mise à jour du statut utilisateur...\n";

    $response = makeRequest("$baseUrl/admin/users/$testUserId/status", 'PUT', [
        'status' => 'active',
        'reason' => 'Test de mise à jour de statut depuis AdminController'
    ], [
        "Authorization: Bearer $adminToken"
    ]);

    if ($response['status_code'] === 200 && $response['body']['success']) {
        echo "✅ Statut utilisateur mis à jour avec succès\n";
        echo "   Nouveau statut: {$response['body']['data']['status']}\n\n";
        return true;
    } else {
        echo "❌ Échec de mise à jour du statut utilisateur\n";
        echo "   Erreur: " . ($response['body']['message'] ?? 'Inconnue') . "\n\n";
        return false;
    }
}

/**
 * Test de sécurité - accès sans authentification
 */
function testSecurity() {
    global $baseUrl;

    echo "🔒 Test de sécurité (accès sans authentification)...\n";

    $response = makeRequest("$baseUrl/admin/statistics", 'GET');

    if ($response['status_code'] === 401) {
        echo "✅ Sécurité OK - Accès refusé sans authentification\n\n";
        return true;
    } else {
        echo "❌ Problème de sécurité - Accès autorisé sans authentification\n\n";
        return false;
    }
}

/**
 * Exécution des tests
 */
function runTests() {
    $tests = [
        'Connexion admin' => 'testAdminLogin',
        'Sécurité' => 'testSecurity',
        'Statistiques générales' => 'testStatistics',
        'Gestion des utilisateurs' => 'testUserManagement',
        'Statistiques des commandes' => 'testOrderStatistics',
        'Statistiques des produits' => 'testProductStatistics',
        'Statistiques des utilisateurs' => 'testUserStatistics',
        'Envoi de notifications' => 'testSendNotification',
        'Récupération des notifications' => 'testGetNotifications',
        'Mise à jour statut utilisateur' => 'testUpdateUserStatus',
    ];

    $passed = 0;
    $total = count($tests);

    foreach ($tests as $testName => $testFunction) {
        echo "🧪 $testName\n";
        echo str_repeat('-', strlen($testName) + 4) . "\n";

        if (call_user_func($testFunction)) {
            $passed++;
        }
    }

    echo "📋 RÉSULTATS FINAUX\n";
    echo "==================\n";
    echo "Tests réussis: $passed/$total\n";
    echo "Pourcentage de réussite: " . round(($passed / $total) * 100, 2) . "%\n\n";

    if ($passed === $total) {
        echo "🎉 TOUS LES TESTS SONT PASSÉS ! L'AdminController est 100% fonctionnel !\n";
    } else {
        echo "⚠️ Certains tests ont échoué. Vérifiez la configuration.\n";
    }
}

// Vérification des prérequis
if (!function_exists('curl_init')) {
    echo "❌ cURL n'est pas installé. Impossible d'exécuter les tests.\n";
    exit(1);
}

// Exécution des tests
runTests();

