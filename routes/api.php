<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\AdminController;

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

// Routes publiques
Route::prefix('v1')->group(function () {
    // Authentification
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/auth/reset-password', [AuthController::class, 'resetPassword']);
    Route::get('/auth/quartiers', [AuthController::class, 'getQuartiers']); // Liste des quartiers

    // Produits et catégories (lecture seule)
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/featured', [ProductController::class, 'featured']);
    Route::get('/products/search', [ProductController::class, 'search']);
    Route::get('/products/{id}', [ProductController::class, 'show']);
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{id}', [CategoryController::class, 'show']);
    Route::get('/categories/{id}/products', [CategoryController::class, 'products']);

    // Panier (accessible sans authentification)
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
    Route::get('/notifications', [AuthController::class, 'notifications']);
    Route::post('/notifications/mark-read/{id}', [AuthController::class, 'markNotificationRead']);
    Route::post('/notifications/mark-all-read', [AuthController::class, 'markAllNotificationsRead']);
});

// Routes d'administration (admin uniquement)
Route::prefix('v1/admin')->middleware(['jwt.auth', 'admin'])->group(function () {
    // Gestion des utilisateurs
    Route::get('/users', [AdminController::class, 'users']);
    Route::get('/users/{id}', [AdminController::class, 'showUser']);
    Route::put('/users/{id}/status', [AdminController::class, 'updateUserStatus']);
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
