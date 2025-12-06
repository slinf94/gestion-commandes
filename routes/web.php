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
            Route::put('/users/{user}/set-status', [UserController::class, 'quickSetStatus'])->name('admin.users.set-status');
        });

        // Gestion des produits - Super Admin, Admin, Gestionnaire et Vendeur peuvent voir
        Route::middleware(['role:super-admin,admin,gestionnaire,vendeur'])->group(function () {
            Route::get('/products', [ProductController::class, 'index'])->name('admin.products.index');
        });

        // Création, modification et suppression PRODUITS - Super Admin et Admin uniquement (Gestionnaire: lecture seule)
        Route::middleware(['role:super-admin,admin'])->group(function () {
            Route::get('/products/create', [ProductController::class, 'create'])->name('admin.products.create');
            Route::post('/products', [ProductController::class, 'store'])->name('admin.products.store');

            // Import/Export des produits (réservé Admin/SuperAdmin)
            Route::get('/products/import-export', [\App\Http\Controllers\Admin\ProductImportExportController::class, 'index'])->name('admin.products.import-export');
            Route::get('/products/export/csv', [\App\Http\Controllers\Admin\ProductImportExportController::class, 'exportCsv'])->name('admin.products.export.csv');
            Route::post('/products/import/csv', [\App\Http\Controllers\Admin\ProductImportExportController::class, 'importCsv'])->name('admin.products.import.csv');
            Route::get('/products/template/csv', [\App\Http\Controllers\Admin\ProductImportExportController::class, 'downloadTemplate'])->name('admin.products.template.csv');
            Route::post('/products/bulk-update', [\App\Http\Controllers\Admin\ProductImportExportController::class, 'bulkUpdate'])->name('admin.products.bulk-update');
            Route::get('/products/statistics/export', [\App\Http\Controllers\Admin\ProductImportExportController::class, 'exportStatistics'])->name('admin.products.statistics.export');

            Route::get('/products/{slug}/edit', [ProductController::class, 'edit'])->name('admin.products.edit');
            Route::put('/products/{slug}', [ProductController::class, 'update'])->name('admin.products.update');
            Route::delete('/products/{slug}', [ProductController::class, 'destroy'])->name('admin.products.destroy');
            Route::post('/products/{slug}/restore', [ProductController::class, 'restore'])->name('admin.products.restore');
            Route::post('/products/{slug}/toggle-status', [ProductController::class, 'toggleStatus'])->name('admin.products.toggle-status');
        });

        // IMPORTANT: placer /products/{product} APRÈS /products/create pour éviter le conflit
        Route::middleware(['role:super-admin,admin,gestionnaire,vendeur'])->group(function () {
            Route::get('/products/{slug}', [ProductController::class, 'show'])->name('admin.products.show');
        });

    // Gestion des images de produits (réservé Admin/SuperAdmin)
    Route::middleware(['role:super-admin,admin'])->group(function () {
        Route::post('/products/{slug}/images', [ProductController::class, 'uploadImages'])->name('admin.products.upload-images');
        Route::post('/products/{slug}/images/{image}/set-main', [ProductController::class, 'setMainImage'])->name('admin.products.set-main-image');
        Route::delete('/products/{slug}/images/{image}', [ProductController::class, 'deleteImage'])->name('admin.products.delete-image');
    });

    // Gestion des prix par quantité (réservé Admin/SuperAdmin) - Style Alibaba
    Route::middleware(['role:super-admin,admin'])->group(function () {
        Route::get('/products/{id}/quantity-prices', [ProductController::class, 'quantityPrices'])->name('admin.products.quantity-prices');
        Route::post('/products/{id}/quantity-prices', [ProductController::class, 'storeQuantityPrice'])->name('admin.products.quantity-prices.store');
        Route::patch('/products/{productId}/quantity-prices/{priceId}/toggle', [ProductController::class, 'toggleQuantityPrice'])->name('admin.products.quantity-prices.toggle');
        Route::delete('/products/{productId}/quantity-prices/{priceId}', [ProductController::class, 'destroyQuantityPrice'])->name('admin.products.quantity-prices.destroy');
    });

    // Gestion des variantes de produits (réservé Admin/SuperAdmin)
    Route::middleware(['role:super-admin,admin'])->group(function () {
        Route::get('/products/{product}/variants', [ProductVariantController::class, 'index'])->name('admin.products.variants.index');
        Route::get('/products/{product}/variants/create', [ProductVariantController::class, 'create'])->name('admin.products.variants.create');
        Route::post('/products/{product}/variants', [ProductVariantController::class, 'store'])->name('admin.products.variants.store');
        Route::get('/products/{product}/variants/{variant}', [ProductVariantController::class, 'show'])->name('admin.products.variants.show');
        Route::get('/products/{product}/variants/{variant}/edit', [ProductVariantController::class, 'edit'])->name('admin.products.variants.edit');
        Route::put('/products/{product}/variants/{variant}', [ProductVariantController::class, 'update'])->name('admin.products.variants.update');
        Route::delete('/products/{product}/variants/{variant}', [ProductVariantController::class, 'destroy'])->name('admin.products.variants.destroy');
        Route::post('/products/{product}/variants/{variant}/toggle-status', [ProductVariantController::class, 'toggleStatus'])->name('admin.products.variants.toggle-status');
        Route::delete('/products/{product}/variants/{variant}/images/{imageIndex}', [ProductVariantController::class, 'deleteImage'])->name('admin.products.variants.delete-image');
    });

        // Gestion des commandes - Tous les rôles peuvent voir
        Route::get('/orders', [OrderController::class, 'index'])->name('admin.orders.index');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('admin.orders.show');
        Route::post('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('admin.orders.status');

        // Suppression/Annulation de commandes - Admin, Super Admin et Gestionnaire
        Route::middleware(['role:super-admin,admin,gestionnaire'])->group(function () {
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

        // Gestion des profils et droits d'accès - Super Admin uniquement
        Route::middleware(['role:super-admin'])->group(function () {
            Route::get('/role-permissions', [\App\Http\Controllers\Admin\RolePermissionController::class, 'index'])->name('admin.role-permissions.index');
            Route::get('/role-permissions/{user}', [\App\Http\Controllers\Admin\RolePermissionController::class, 'show'])->name('admin.role-permissions.show');
            Route::post('/role-permissions/{user}/assign-role', [\App\Http\Controllers\Admin\RolePermissionController::class, 'assignRole'])->name('admin.role-permissions.assign-role');
            Route::post('/role-permissions/{user}/remove-role', [\App\Http\Controllers\Admin\RolePermissionController::class, 'removeRole'])->name('admin.role-permissions.remove-role');
            Route::post('/role-permissions/{user}/assign-permission', [\App\Http\Controllers\Admin\RolePermissionController::class, 'assignPermission'])->name('admin.role-permissions.assign-permission');
            Route::post('/role-permissions/{user}/remove-permission', [\App\Http\Controllers\Admin\RolePermissionController::class, 'removePermission'])->name('admin.role-permissions.remove-permission');
            Route::put('/role-permissions/{user}/update-legacy-role', [\App\Http\Controllers\Admin\RolePermissionController::class, 'updateLegacyRole'])->name('admin.role-permissions.update-legacy-role');
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

                // Catégories
                // Lecture: Admin, SuperAdmin, Gestionnaire
                Route::middleware(['role:super-admin,admin,gestionnaire'])->group(function () {
                    Route::get('/categories', [\App\Http\Controllers\Admin\CategoryController::class, 'index'])->name('admin.categories.index');
                });
                // Écriture: Admin, SuperAdmin
                Route::middleware(['role:super-admin,admin'])->group(function () {
                    // Routes spécifiques AVANT les routes avec paramètres
                    Route::get('/categories/create', [\App\Http\Controllers\Admin\CategoryController::class, 'create'])->name('admin.categories.create');
                    Route::post('/categories', [\App\Http\Controllers\Admin\CategoryController::class, 'store'])->name('admin.categories.store');
                    Route::post('/categories/reorder', [\App\Http\Controllers\Admin\CategoryController::class, 'reorder'])->name('admin.categories.reorder');
                    // Routes avec paramètres APRÈS les routes spécifiques
                    Route::get('/categories/{id}', [\App\Http\Controllers\Admin\CategoryController::class, 'show'])->name('admin.categories.show');
                    Route::get('/categories/{id}/edit', [\App\Http\Controllers\Admin\CategoryController::class, 'edit'])->name('admin.categories.edit');
                    Route::put('/categories/{id}', [\App\Http\Controllers\Admin\CategoryController::class, 'update'])->name('admin.categories.update');
                    Route::delete('/categories/{id}', [\App\Http\Controllers\Admin\CategoryController::class, 'destroy'])->name('admin.categories.destroy');
                    Route::post('/categories/{id}/toggle-status', [\App\Http\Controllers\Admin\CategoryController::class, 'toggleStatus'])->name('admin.categories.toggle-status');
                });

                // Attributs
                // Lecture: Admin, SuperAdmin, Gestionnaire
                Route::middleware(['role:super-admin,admin,gestionnaire'])->group(function () {
                    Route::get('attributes', [\App\Http\Controllers\Admin\AttributeController::class, 'index'])->name('admin.attributes.index');
                });
                // Écriture: Admin, SuperAdmin
                Route::middleware(['role:super-admin,admin'])->group(function () {
                    // Routes spécifiques AVANT les routes avec paramètres
                    Route::get('attributes/create', [\App\Http\Controllers\Admin\AttributeController::class, 'create'])->name('admin.attributes.create');
                    Route::post('attributes', [\App\Http\Controllers\Admin\AttributeController::class, 'store'])->name('admin.attributes.store');
                    Route::post('/attributes/reorder', [\App\Http\Controllers\Admin\AttributeController::class, 'reorder'])->name('admin.attributes.reorder');
                    // Routes avec paramètres APRÈS les routes spécifiques
                    Route::get('attributes/{attribute}/edit', [\App\Http\Controllers\Admin\AttributeController::class, 'edit'])->name('admin.attributes.edit');
                    Route::put('attributes/{attribute}', [\App\Http\Controllers\Admin\AttributeController::class, 'update'])->name('admin.attributes.update');
                    Route::delete('attributes/{attribute}', [\App\Http\Controllers\Admin\AttributeController::class, 'destroy'])->name('admin.attributes.destroy');
                    Route::post('/attributes/{attribute}/toggle-status', [\App\Http\Controllers\Admin\AttributeController::class, 'toggleStatus'])->name('admin.attributes.toggle-status');
                    Route::get('/attributes/{attribute}/options', [\App\Http\Controllers\Admin\AttributeController::class, 'getOptions'])->name('admin.attributes.options');
                });
                Route::middleware(['role:super-admin,admin,gestionnaire'])->group(function () {
                    Route::get('attributes/{attribute}', [\App\Http\Controllers\Admin\AttributeController::class, 'show'])->name('admin.attributes.show');
                });

                // Types de produits
                // Lecture: Admin, SuperAdmin, Gestionnaire
                Route::middleware(['role:super-admin,admin,gestionnaire'])->group(function () {
                    Route::get('product-types', [\App\Http\Controllers\Admin\ProductTypeController::class, 'index'])->name('admin.product-types.index');
                });
                // Écriture: Admin, SuperAdmin
                Route::middleware(['role:super-admin,admin'])->group(function () {
                    // Routes spécifiques AVANT les routes avec paramètres
                    Route::get('product-types/create', [\App\Http\Controllers\Admin\ProductTypeController::class, 'create'])->name('admin.product-types.create');
                    Route::post('product-types', [\App\Http\Controllers\Admin\ProductTypeController::class, 'store'])->name('admin.product-types.store');
                    Route::post('product-types/reorder', [\App\Http\Controllers\Admin\ProductTypeController::class, 'reorder'])->name('admin.product-types.reorder');
                    // Routes avec paramètres APRÈS les routes spécifiques
                    Route::get('product-types/{productType}/edit', [\App\Http\Controllers\Admin\ProductTypeController::class, 'edit'])->name('admin.product-types.edit');
                    Route::put('product-types/{productType}', [\App\Http\Controllers\Admin\ProductTypeController::class, 'update'])->name('admin.product-types.update');
                    Route::delete('product-types/{productType}', [\App\Http\Controllers\Admin\ProductTypeController::class, 'destroy'])->name('admin.product-types.destroy');
                    Route::post('/product-types/{productType}/toggle-status', [\App\Http\Controllers\Admin\ProductTypeController::class, 'toggleStatus'])->name('admin.product-types.toggle-status');
                });
                // Visualisation détaillée (après déclaration des routes spécifiques)
                Route::middleware(['role:super-admin,admin,gestionnaire'])->group(function () {
                    Route::get('product-types/{productType}', [\App\Http\Controllers\Admin\ProductTypeController::class, 'show'])->name('admin.product-types.show');
                });

                // Gestion des variantes de produits
                // Route déjà définie plus haut, à voir si protection nécessaire

                // Recherche autocomplete - Tous les admins
                Route::middleware(['role:super-admin,admin,gestionnaire'])->group(function () {
                    Route::get('/search/products', [\App\Http\Controllers\Admin\SearchController::class, 'products'])->name('admin.search.products');
                    Route::get('/search/users', [\App\Http\Controllers\Admin\SearchController::class, 'users'])->name('admin.search.users');
                    Route::get('/search/orders', [\App\Http\Controllers\Admin\SearchController::class, 'orders'])->name('admin.search.orders');
                    Route::get('/search/clients', [\App\Http\Controllers\Admin\SearchController::class, 'clients'])->name('admin.search.clients');
                    Route::get('/search/categories', [\App\Http\Controllers\Admin\SearchController::class, 'categories'])->name('admin.search.categories');
                    Route::get('/search/attributes', [\App\Http\Controllers\Admin\SearchController::class, 'attributes'])->name('admin.search.attributes');
                    Route::get('/search/product-types', [\App\Http\Controllers\Admin\SearchController::class, 'productTypes'])->name('admin.search.product-types');
                });

                // Notifications admin - Tous les admins
                Route::middleware(['role:super-admin,admin,gestionnaire'])->group(function () {
                    Route::get('/notifications', [\App\Http\Controllers\Admin\NotificationController::class, 'indexPage'])->name('admin.notifications.index');
                    Route::get('/notifications/api', [\App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('admin.notifications.api');
                    Route::get('/notifications/unread-count', [\App\Http\Controllers\Admin\NotificationController::class, 'unreadCount'])->name('admin.notifications.unread-count');
                    Route::post('/notifications/{notification}/read', [\App\Http\Controllers\Admin\NotificationController::class, 'markAsRead'])->name('admin.notifications.mark-read');
                    Route::post('/notifications/mark-all-read', [\App\Http\Controllers\Admin\NotificationController::class, 'markAllAsRead'])->name('admin.notifications.mark-all-read');
                    Route::delete('/notifications/{notification}', [\App\Http\Controllers\Admin\NotificationController::class, 'destroy'])->name('admin.notifications.destroy');
                });

                // Paramètres admin - Super Admin uniquement
                Route::middleware(['role:super-admin'])->group(function () {
                    Route::get('/settings', [SettingsController::class, 'index'])->name('admin.settings.index');
                    Route::get('/settings/general', [SettingsController::class, 'general'])->name('admin.settings.general');
                    Route::put('/settings/general', [SettingsController::class, 'updateGeneral'])->name('admin.settings.general.update');
                    Route::get('/settings/security', [SettingsController::class, 'security'])->name('admin.settings.security');
                    Route::get('/settings/notifications', [SettingsController::class, 'notifications'])->name('admin.settings.notifications');
                    Route::put('/settings/notifications', [SettingsController::class, 'updateNotifications'])->name('admin.settings.notifications.update');
                    Route::get('/settings/system', [SettingsController::class, 'system'])->name('admin.settings.system');
                    Route::get('/settings/logs', [SettingsController::class, 'logs'])->name('admin.settings.logs');
                    Route::get('/settings/maintenance', [SettingsController::class, 'maintenance'])->name('admin.settings.maintenance');
                    Route::post('/settings/clear-cache', [SettingsController::class, 'clearCache'])->name('admin.settings.clear-cache');
                    Route::post('/settings/optimize-db', [SettingsController::class, 'optimizeDatabase'])->name('admin.settings.optimize-db');
                    Route::post('/settings/clear-logs', [SettingsController::class, 'clearLogs'])->name('admin.settings.clear-logs');
                });
    });
});

// Route pour la redirection mobile après activation du compte
Route::get('/mobile-app', [MobileRedirectController::class, 'index'])->name('mobile.app');

