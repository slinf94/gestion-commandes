<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductVariantController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\QuartierController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\AdminActivityController;
use App\Http\Controllers\MobileRedirectController;
use App\Http\Controllers\PasswordResetController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Redirection automatique vers l'interface admin (sécurité)
Route::get('/', function () {
    return redirect()->route('admin.login');
});

// Routes de réinitialisation de mot de passe
Route::get('/password/reset', [PasswordResetController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/password/email', [PasswordResetController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');

// Routes Admin
Route::prefix('admin')->group(function () {
    // Page de connexion
    Route::get('/login', [DashboardController::class, 'login'])->name('admin.login');
    Route::post('/login', [DashboardController::class, 'authenticate'])->name('admin.authenticate');

    // Routes protégées par authentification
    Route::middleware(['auth'])->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
        Route::post('/logout', [DashboardController::class, 'logout'])->name('admin.logout');

        // Gestion des utilisateurs - Uniquement pour Super Admin et Admin
        Route::middleware(['role:super-admin,admin'])->group(function () {
            Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
        Route::get('/users/by-quartier', [UserController::class, 'byQuartier'])->name('admin.users.by-quartier');
        Route::get('/users/create', [UserController::class, 'create'])->name('admin.users.create');
        Route::post('/users', [UserController::class, 'store'])->name('admin.users.store');
        Route::get('/users/{user}', [UserController::class, 'show'])->name('admin.users.show');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('admin.users.update');
        Route::post('/users/{user}/quick-activate', [UserController::class, 'quickActivate'])->name('admin.users.quick-activate');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');
        Route::post('/users/{id}/restore', [UserController::class, 'restore'])->name('admin.users.restore');

        // Export des clients
        Route::get('/users/export/csv', [UserController::class, 'exportCsv'])->name('admin.users.export.csv');
        Route::get('/users/export/by-quartier/csv', [UserController::class, 'exportByQuartierCsv'])->name('admin.users.export.by-quartier.csv');
        Route::get('/users/export/quartier/{quartier}/csv', [UserController::class, 'exportQuartierClientsCsv'])->name('admin.users.export.quartier.csv');

        // API pour basculer le statut utilisateur (pour AJAX)
        Route::put('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('admin.users.toggle-status');
        });

        // Gestion des produits - Super Admin, Admin, Gestionnaire et Vendeur peuvent voir
        Route::middleware(['role:super-admin,admin,gestionnaire,vendeur'])->group(function () {
            Route::get('/products', [ProductController::class, 'index'])->name('admin.products.index');
            Route::get('/products/{id}', [ProductController::class, 'show'])->name('admin.products.show');
        });

        // Création, modification et suppression - Super Admin, Admin et Gestionnaire uniquement
        Route::middleware(['role:super-admin,admin,gestionnaire'])->group(function () {
            Route::get('/products/create', [ProductController::class, 'create'])->name('admin.products.create');
            Route::post('/products', [ProductController::class, 'store'])->name('admin.products.store');

            // Import/Export des produits
            Route::get('/products/import-export', [\App\Http\Controllers\Admin\ProductImportExportController::class, 'index'])->name('admin.products.import-export');
            Route::get('/products/export/csv', [\App\Http\Controllers\Admin\ProductImportExportController::class, 'exportCsv'])->name('admin.products.export.csv');
            Route::post('/products/import/csv', [\App\Http\Controllers\Admin\ProductImportExportController::class, 'importCsv'])->name('admin.products.import.csv');
            Route::get('/products/template/csv', [\App\Http\Controllers\Admin\ProductImportExportController::class, 'downloadTemplate'])->name('admin.products.template.csv');
            Route::post('/products/bulk-update', [\App\Http\Controllers\Admin\ProductImportExportController::class, 'bulkUpdate'])->name('admin.products.bulk-update');
            Route::get('/products/statistics/export', [\App\Http\Controllers\Admin\ProductImportExportController::class, 'exportStatistics'])->name('admin.products.statistics.export');

            Route::get('/products/{id}/edit', [ProductController::class, 'edit'])->name('admin.products.edit');
            Route::put('/products/{id}', [ProductController::class, 'update'])->name('admin.products.update');
            Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('admin.products.destroy');
            Route::post('/products/{id}/restore', [ProductController::class, 'restore'])->name('admin.products.restore');
        });

    // Gestion des images de produits
    Route::post('/products/{product}/images', [ProductController::class, 'uploadImages'])->name('admin.products.upload-images');
    Route::post('/products/{product}/images/{image}/set-main', [ProductController::class, 'setMainImage'])->name('admin.products.set-main-image');
    Route::delete('/products/{product}/images/{image}', [ProductController::class, 'deleteImage'])->name('admin.products.delete-image');

    // Gestion des variantes de produits
    Route::get('/products/{product}/variants', [ProductVariantController::class, 'index'])->name('admin.products.variants.index');
    Route::get('/products/{product}/variants/create', [ProductVariantController::class, 'create'])->name('admin.products.variants.create');
    Route::post('/products/{product}/variants', [ProductVariantController::class, 'store'])->name('admin.products.variants.store');
    Route::get('/products/{product}/variants/{variant}', [ProductVariantController::class, 'show'])->name('admin.products.variants.show');
    Route::get('/products/{product}/variants/{variant}/edit', [ProductVariantController::class, 'edit'])->name('admin.products.variants.edit');
    Route::put('/products/{product}/variants/{variant}', [ProductVariantController::class, 'update'])->name('admin.products.variants.update');
    Route::delete('/products/{product}/variants/{variant}', [ProductVariantController::class, 'destroy'])->name('admin.products.variants.destroy');
    Route::post('/products/{product}/variants/{variant}/toggle-status', [ProductVariantController::class, 'toggleStatus'])->name('admin.products.variants.toggle-status');
    Route::delete('/products/{product}/variants/{variant}/images/{imageIndex}', [ProductVariantController::class, 'deleteImage'])->name('admin.products.variants.delete-image');

        // Gestion des commandes - Tous les rôles peuvent voir
        Route::get('/orders', [OrderController::class, 'index'])->name('admin.orders.index');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('admin.orders.show');
        Route::post('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('admin.orders.status');

        // Suppression de commandes - Admin et Super Admin uniquement
        Route::middleware(['role:super-admin,admin'])->group(function () {
            Route::put('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('admin.orders.cancel');
            Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('admin.orders.destroy');
        });

        // Gestion des clients CRM - Super Admin, Admin et Gestionnaire uniquement
        Route::middleware(['role:super-admin,admin,gestionnaire'])->group(function () {
            Route::get('/clients', [ClientController::class, 'index'])->name('admin.clients.index');
            Route::get('/clients/search', [ClientController::class, 'search'])->name('admin.clients.search');
            Route::get('/clients/{client}', [ClientController::class, 'show'])->name('admin.clients.show');
            Route::get('/clients/{client}/orders/filter', [ClientController::class, 'filterOrders'])->name('admin.clients.orders.filter');
        });

        // Gestion des quartiers - Super Admin, Admin et Gestionnaire
        Route::middleware(['role:super-admin,admin,gestionnaire'])->group(function () {
            Route::get('/quartiers', [QuartierController::class, 'index'])->name('admin.quartiers.index');
            Route::get('/quartiers/create', [QuartierController::class, 'create'])->name('admin.quartiers.create');
            Route::post('/quartiers', [QuartierController::class, 'store'])->name('admin.quartiers.store');
            Route::get('/quartiers/{quartier}', [QuartierController::class, 'show'])->name('admin.quartiers.show');
            Route::get('/quartiers/{quartier}/edit', [QuartierController::class, 'edit'])->name('admin.quartiers.edit');
            Route::put('/quartiers/{quartier}', [QuartierController::class, 'update'])->name('admin.quartiers.update');
            Route::delete('/quartiers/{quartier}', [QuartierController::class, 'destroy'])->name('admin.quartiers.destroy');
            Route::get('/quartiers/{quartier}/clients', [QuartierController::class, 'clients'])->name('admin.quartiers.clients');
            Route::post('/users/{user}/reassign-quartier', [QuartierController::class, 'reassignClient'])->name('admin.users.reassign-quartier');
            Route::get('/quartiers/statistics', [QuartierController::class, 'statistics'])->name('admin.quartiers.statistics');
            Route::get('/quartiers/{quartier}/export-clients', [QuartierController::class, 'exportClients'])->name('admin.quartiers.export-clients');
        });

        // Gestion du profil admin
        Route::get('/profile', [ProfileController::class, 'show'])->name('admin.profile.show');
        Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('admin.profile.edit');
        Route::put('/profile', [ProfileController::class, 'update'])->name('admin.profile.update');
        Route::get('/profile/password', [ProfileController::class, 'editPassword'])->name('admin.profile.password');
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('admin.profile.password.update');

                // Journal des activités - Super Admin et Admin uniquement
                Route::middleware(['role:super-admin,admin'])->group(function () {
                    Route::get('/activity-logs', [AdminActivityController::class, 'index'])->name('admin.activity-logs.index');
                    Route::get('/activity-logs/{activityLog}', [AdminActivityController::class, 'show'])->name('admin.activity-logs.show');
                    Route::get('/activity-logs-ajax', [AdminActivityController::class, 'getLogs'])->name('admin.activity-logs.get-logs');
                    Route::get('/activity-logs-statistics', [AdminActivityController::class, 'statistics'])->name('admin.activity-logs.statistics');
                    Route::post('/activity-logs-cleanup', [AdminActivityController::class, 'cleanup'])->name('admin.activity-logs.cleanup');
                    Route::get('/activity-logs-export', [AdminActivityController::class, 'exportCsv'])->name('admin.activity-logs.export');
                });

                // Gestion des catégories - Super Admin, Admin et Gestionnaire
                Route::middleware(['role:super-admin,admin,gestionnaire'])->group(function () {
                    Route::get('/categories', [\App\Http\Controllers\Admin\CategoryController::class, 'index'])->name('admin.categories.index');
                    Route::get('/categories/create', [\App\Http\Controllers\Admin\CategoryController::class, 'create'])->name('admin.categories.create');
                    Route::post('/categories', [\App\Http\Controllers\Admin\CategoryController::class, 'store'])->name('admin.categories.store');
                    Route::get('/categories/{id}', [\App\Http\Controllers\Admin\CategoryController::class, 'show'])->name('admin.categories.show');
                    Route::get('/categories/{id}/edit', [\App\Http\Controllers\Admin\CategoryController::class, 'edit'])->name('admin.categories.edit');
                    Route::put('/categories/{id}', [\App\Http\Controllers\Admin\CategoryController::class, 'update'])->name('admin.categories.update');
                    Route::delete('/categories/{id}', [\App\Http\Controllers\Admin\CategoryController::class, 'destroy'])->name('admin.categories.destroy');
                    Route::post('/categories/{id}/toggle-status', [\App\Http\Controllers\Admin\CategoryController::class, 'toggleStatus'])->name('admin.categories.toggle-status');
                    Route::post('/categories/reorder', [\App\Http\Controllers\Admin\CategoryController::class, 'reorder'])->name('admin.categories.reorder');
                });

                // Gestion des attributs - Super Admin, Admin et Gestionnaire
                Route::middleware(['role:super-admin,admin,gestionnaire'])->group(function () {
                    Route::resource('attributes', \App\Http\Controllers\Admin\AttributeController::class)->names([
                        'index' => 'admin.attributes.index',
                        'create' => 'admin.attributes.create',
                        'store' => 'admin.attributes.store',
                        'show' => 'admin.attributes.show',
                        'edit' => 'admin.attributes.edit',
                        'update' => 'admin.attributes.update',
                        'destroy' => 'admin.attributes.destroy',
                    ]);
                    Route::post('/attributes/{attribute}/toggle-status', [\App\Http\Controllers\Admin\AttributeController::class, 'toggleStatus'])->name('admin.attributes.toggle-status');
                    Route::get('/attributes/{attribute}/options', [\App\Http\Controllers\Admin\AttributeController::class, 'getOptions'])->name('admin.attributes.options');
                });

                // Gestion des types de produits - Super Admin, Admin et Gestionnaire
                Route::middleware(['role:super-admin,admin,gestionnaire'])->group(function () {
                    Route::resource('product-types', \App\Http\Controllers\Admin\ProductTypeController::class)->names([
                        'index' => 'admin.product-types.index',
                        'create' => 'admin.product-types.create',
                        'store' => 'admin.product-types.store',
                        'show' => 'admin.product-types.show',
                        'edit' => 'admin.product-types.edit',
                        'update' => 'admin.product-types.update',
                        'destroy' => 'admin.product-types.destroy',
                    ]);
                    Route::post('/product-types/{productType}/toggle-status', [\App\Http\Controllers\Admin\ProductTypeController::class, 'toggleStatus'])->name('admin.product-types.toggle-status');
                });

                // Gestion des variantes de produits
                // Route déjà définie plus haut, à voir si protection nécessaire

                // Paramètres admin - Super Admin uniquement
                Route::middleware(['role:super-admin'])->group(function () {
                    Route::get('/settings', [SettingsController::class, 'index'])->name('admin.settings.index');
                    Route::get('/settings/general', [SettingsController::class, 'general'])->name('admin.settings.general');
                    Route::put('/settings/general', [SettingsController::class, 'updateGeneral'])->name('admin.settings.general.update');
                    Route::get('/settings/security', [SettingsController::class, 'security'])->name('admin.settings.security');
                    Route::get('/settings/notifications', [SettingsController::class, 'notifications'])->name('admin.settings.notifications');
                    Route::put('/settings/notifications', [SettingsController::class, 'updateNotifications'])->name('admin.settings.notifications.update');
                    Route::get('/settings/system', [SettingsController::class, 'system'])->name('admin.settings.system');
                    Route::post('/settings/clear-cache', [SettingsController::class, 'clearCache'])->name('admin.settings.clear-cache');
                });
    });
});

// Route pour la redirection mobile après activation du compte
Route::get('/mobile-app', [MobileRedirectController::class, 'index'])->name('mobile.app');

