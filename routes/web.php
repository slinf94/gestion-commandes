<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\OrderController;

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

Route::get('/', function () {
    return view('api-test');
});

Route::get('/admin-test', function () {
    return view('admin-test');
});

// Routes Admin
Route::prefix('admin')->group(function () {
    // Page de connexion
    Route::get('/login', [DashboardController::class, 'login'])->name('admin.login');
    Route::post('/login', [DashboardController::class, 'authenticate'])->name('admin.authenticate');

    // Routes protégées par authentification
    Route::middleware(['auth'])->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
        Route::post('/logout', [DashboardController::class, 'logout'])->name('admin.logout');

        // Gestion des utilisateurs
        Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('admin.users.create');
        Route::post('/users', [UserController::class, 'store'])->name('admin.users.store');
        Route::get('/users/{user}', [UserController::class, 'show'])->name('admin.users.show');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('admin.users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');
        Route::post('/users/{id}/restore', [UserController::class, 'restore'])->name('admin.users.restore');

        // Gestion des produits
        Route::get('/products', [ProductController::class, 'index'])->name('admin.products.index');
        Route::get('/products/create', [ProductController::class, 'create'])->name('admin.products.create');
        Route::post('/products', [ProductController::class, 'store'])->name('admin.products.store');
        Route::get('/products/{product}', [ProductController::class, 'show'])->name('admin.products.show');
        Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('admin.products.edit');
        Route::put('/products/{product}', [ProductController::class, 'update'])->name('admin.products.update');
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('admin.products.destroy');
        Route::post('/products/{id}/restore', [ProductController::class, 'restore'])->name('admin.products.restore');

        // Gestion des commandes
        Route::get('/orders', [OrderController::class, 'index'])->name('admin.orders.index');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('admin.orders.show');
        Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('admin.orders.destroy');
        Route::post('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('admin.orders.status');
    });
});
