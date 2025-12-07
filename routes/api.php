<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\NotificationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route de test de connectivité
Route::get('/ping', function () {
    return response()->json([
        'success' => true,
        'message' => 'Server is running',
        'timestamp' => now()->toIso8601String(),
        'server_ip' => $_SERVER['SERVER_ADDR'] ?? 'unknown'
    ]);
});

// Routes publiques
Route::prefix('v1')->group(function () {
    // Route de test de connectivité pour mobile
    Route::get('/ping', function () {
        return response()->json([
            'success' => true,
            'message' => 'API is running',
            'timestamp' => now()->toIso8601String(),
            'server_ip' => $_SERVER['SERVER_ADDR'] ?? 'unknown'
        ]);
    });

    // Route de santé pour la détection automatique d'IP
    Route::get('/health', function () {
        return response()->json([
            'success' => true,
            'status' => 'ok',
            'timestamp' => now()->toIso8601String(),
        ]);
    });

    // Authentification
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/auth/reset-password', [AuthController::class, 'resetPassword']);
    Route::get('/auth/quartiers', [AuthController::class, 'getQuartiers']); // Liste des quartiers
    Route::post('/auth/search-commercial', [AuthController::class, 'searchCommercial']); // Rechercher un commercial par téléphone

// Produits et catégories (lecture seule) - API simplifiée
Route::get('/products', [ProductApiController::class, 'index']);
Route::get('/products/search', [ProductApiController::class, 'search']);
Route::get('/products/{id}', [ProductApiController::class, 'show']);
Route::get('/products/category/{category}', [ProductApiController::class, 'byCategory']);
Route::get('/products/type/{productType}', [ProductApiController::class, 'byType']);

    // Catégories et types
    Route::get('/categories', [ProductApiController::class, 'categories']);
    Route::get('/product-types', [ProductApiController::class, 'productTypes']);
    Route::get('/attributes', [ProductApiController::class, 'attributes']);
    Route::get('/attribute-values', [ProductApiController::class, 'attributeValues']);

    // Valeurs distinctes pour les filtres
    Route::get('/products/filter-values', [ProductApiController::class, 'filterValues']);

    // Anciennes routes pour compatibilité
    Route::get('/categories/{id}', [CategoryController::class, 'show']);
    Route::get('/categories/{id}/products', [CategoryController::class, 'products']);

    // Panier (accessible sans authentification, utilise session_id)
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart/add', [CartController::class, 'add']);
    Route::put('/cart/update/{id}', [CartController::class, 'update']);
    Route::delete('/cart/remove/{id}', [CartController::class, 'remove']);
    Route::delete('/cart/clear', [CartController::class, 'clear']);
    Route::get('/cart/count', [CartController::class, 'count']);

});

// Routes protégées par authentification JWT
Route::prefix('v1')->middleware('jwt.auth')->group(function () {
    // Authentification
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/refresh', [AuthController::class, 'refresh']);
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::get('/auth/profile', [AuthController::class, 'profile']);
    Route::put('/auth/profile', [AuthController::class, 'updateProfile']);
    Route::post('/auth/change-password', [AuthController::class, 'changePassword']);

    // Profil utilisateur
    Route::post('/profile/photo', [AuthController::class, 'uploadProfilePhoto']);
    Route::delete('/profile/delete', [AuthController::class, 'deleteAccount']);

    // Commandes
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::put('/orders/{id}/status', [OrderController::class, 'updateStatus']);
    Route::put('/orders/{id}/cancel', [OrderController::class, 'cancelOrder']);
    Route::delete('/orders/{id}', [OrderController::class, 'deleteOrder']);
    Route::get('/orders/history', [OrderController::class, 'history']);

    // Favoris
    Route::get('/favorites', [ProductController::class, 'favorites']);
    Route::post('/favorites/add', [ProductController::class, 'addToFavorites']);
    Route::delete('/favorites/remove/{id}', [ProductController::class, 'removeFromFavorites']);
    Route::get('/favorites/check/{id}', [ProductController::class, 'checkFavorite']);

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
    Route::get('/notifications/{id}', [NotificationController::class, 'show']);
    Route::post('/notifications/{id}/mark-read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy']);
    Route::delete('/notifications', [NotificationController::class, 'clear']);
});

// Routes pour la gestion des permissions (admin uniquement)
Route::prefix('v1/admin')->middleware(['jwt.auth', 'admin'])->group(function () {
    // Rôles et Permissions
    Route::get('/roles', [\App\Http\Controllers\Api\PermissionController::class, 'roles']);
    Route::get('/roles/{id}', [\App\Http\Controllers\Api\PermissionController::class, 'showRole']);
    Route::post('/roles', [\App\Http\Controllers\Api\PermissionController::class, 'createRole']);
    Route::put('/roles/{id}/permissions', [\App\Http\Controllers\Api\PermissionController::class, 'updateRolePermissions']);
    Route::delete('/roles/{id}', [\App\Http\Controllers\Api\PermissionController::class, 'deleteRole']);

    Route::get('/permissions', [\App\Http\Controllers\Api\PermissionController::class, 'permissions']);

    // Gestion des rôles/permissions des utilisateurs
    Route::get('/users/{id}/permissions', [\App\Http\Controllers\Api\PermissionController::class, 'getUserPermissions']);
    Route::put('/users/{id}/roles', [\App\Http\Controllers\Api\PermissionController::class, 'updateUserRoles']);
    Route::post('/users/{id}/permissions', [\App\Http\Controllers\Api\PermissionController::class, 'addPermissionToUser']);
    Route::delete('/users/{id}/permissions', [\App\Http\Controllers\Api\PermissionController::class, 'removePermissionFromUser']);
});

// Routes d'administration (admin uniquement)
Route::prefix('v1/admin')->middleware(['jwt.auth', 'admin'])->group(function () {
    // Gestion des utilisateurs
    Route::get('/users', [AdminController::class, 'users']);
    Route::get('/users/{id}', [AdminController::class, 'showUser']);
    Route::put('/users/{id}/status', [AdminController::class, 'updateUserStatus']);
    Route::put('/users/{id}/toggle-status', [AdminController::class, 'toggleUserStatus']);
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser']);

    // Gestion des produits
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);
    Route::post('/products/{id}/images', [ProductController::class, 'uploadImages']);

    // Gestion des catégories
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::put('/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);

    // Gestion des commandes
    Route::get('/orders', [OrderController::class, 'adminIndex']);
    Route::put('/orders/{id}', [OrderController::class, 'update']);
    Route::get('/orders/{id}/history', [OrderController::class, 'statusHistory']);

    // Statistiques
    Route::get('/statistics', [AdminController::class, 'statistics']);
    Route::get('/statistics/orders', [AdminController::class, 'orderStatistics']);
    Route::get('/statistics/products', [AdminController::class, 'productStatistics']);
    Route::get('/statistics/users', [AdminController::class, 'userStatistics']);

    // Notifications
    Route::get('/notifications', [AdminController::class, 'notifications']);
    Route::post('/notifications/send', [AdminController::class, 'sendNotification']);
});


// Routes pour les commerciaux
Route::middleware(['auth:api', 'role:commercial'])->group(function () {
    Route::get('commercial/orders', [\App\Http\Controllers\Api\CommercialOrderController::class, 'index']);
    Route::get('commercial/orders/{id}', [\App\Http\Controllers\Api\CommercialOrderController::class, 'show']);

    Route::middleware('permission:invoices.edit')->group(function () {
        Route::put('invoices/{id}', [\App\Http\Controllers\Api\InvoiceController::class, 'update']);
    });
});

// Routes pour les factures
Route::prefix('v1')->middleware(['jwt.auth'])->group(function () {
    Route::get('/invoices', [\App\Http\Controllers\Api\InvoiceController::class, 'index']);
    Route::get('/invoices/{id}', [\App\Http\Controllers\Api\InvoiceController::class, 'show']);
    Route::post('/invoices', [\App\Http\Controllers\Api\InvoiceController::class, 'store']);
    Route::put('/invoices/{id}', [\App\Http\Controllers\Api\InvoiceController::class, 'update']);
    Route::post('/invoices/{id}/payment', [\App\Http\Controllers\Api\InvoiceController::class, 'addPayment']);
    Route::delete('/invoices/{id}', [\App\Http\Controllers\Api\InvoiceController::class, 'destroy']);
});
