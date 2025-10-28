# Système de Gestion des Permissions

## 🎯 Vue d'ensemble

Ce système de permissions est conçu pour gérer les accès des utilisateurs de manière flexible et sécurisée. Il utilise une architecture basée sur les rôles et les permissions (RBAC - Role-Based Access Control).

## 📋 Structure

### Tables créées

1. **roles** - Stocke les rôles (Super Admin, Admin, Gestionnaire, Vendeur, Client)
2. **permissions** - Stocke les permissions (users.view, products.create, etc.)
3. **role_permission** - Table pivot pour lier les rôles aux permissions
4. **user_role** - Table pivot pour lier les utilisateurs aux rôles

### Modèles

#### Role
- `name` : Nom du rôle
- `slug` : Identifiant unique
- `description` : Description du rôle
- `is_active` : Statut actif/inactif

#### Permission
- `name` : Nom de la permission
- `slug` : Identifiant unique
- `module` : Module concerné (users, products, orders, etc.)
- `description` : Description de la permission

## 🔐 Utilisation

### Vérifier si un utilisateur a un rôle

```php
if (auth()->user()->hasRole('admin')) {
    // L'utilisateur est admin
}

if (auth()->user()->hasAnyRole(['admin', 'manager'])) {
    // L'utilisateur est admin OU manager
}
```

### Vérifier si un utilisateur a une permission

```php
if (auth()->user()->hasPermission('products.create')) {
    // L'utilisateur peut créer des produits
}

if (auth()->user()->hasAllPermissions(['products.view', 'products.create'])) {
    // L'utilisateur a toutes ces permissions
}
```

### Attacher un rôle à un utilisateur

```php
$user = User::find(1);
$user->attachRole('admin'); // Par slug
// ou
$role = Role::find(1);
$user->attachRole($role); // Par objet
```

### Détacher un rôle

```php
$user->detachRole('admin');
```

### Attacher une permission à un rôle

```php
$role = Role::where('slug', 'admin')->first();
$role->attachPermission('products.create');
```

### Détacher une permission

```php
$role->detachPermission('products.create');
```

## 🛡️ Middleware

Le middleware `CheckPermission` permet de protéger les routes :

```php
Route::middleware(['auth', 'permission:products.view'])->group(function () {
    Route::get('/products', [ProductController::class, 'index']);
});
```

```php
// Dans web.php
Route::get('/products', [ProductController::class, 'index'])
    ->middleware(['auth', 'permission:products.view']);
```

## 📊 Rôles par défaut

### Super Administrateur
- Toutes les permissions

### Administrateur
- Voir et modifier les utilisateurs
- Gérer les produits (vue, création, modification)
- Gérer les commandes (vue, modification)
- Gérer les catégories (vue, création, modification)
- Voir le journal des activités

### Gestionnaire
- Gérer les produits (vue, création, modification)
- Gérer les commandes (vue, modification)
- Gérer les catégories (vue, création, modification)

### Vendeur
- Voir les produits
- Voir et modifier les commandes

### Client
- Aucune permission (par défaut)

## 🔧 Permissions disponibles

### Utilisateurs
- `users.view` - Voir la liste des utilisateurs
- `users.create` - Créer des utilisateurs
- `users.edit` - Modifier des utilisateurs
- `users.delete` - Supprimer des utilisateurs

### Produits
- `products.view` - Voir la liste des produits
- `products.create` - Créer des produits
- `products.edit` - Modifier des produits
- `products.delete` - Supprimer des produits

### Commandes
- `orders.view` - Voir la liste des commandes
- `orders.edit` - Modifier les commandes
- `orders.delete` - Supprimer les commandes

### Catégories
- `categories.view` - Voir la liste des catégories
- `categories.create` - Créer des catégories
- `categories.edit` - Modifier des catégories
- `categories.delete` - Supprimer des catégories

### Activités
- `activities.view` - Voir le journal des activités

### Paramètres
- `settings.manage` - Gérer les paramètres du système

## 🚀 Installation

Les migrations et seeders ont déjà été exécutés. Si vous voulez les réexécuter :

```bash
php artisan migrate:refresh --seed
```

Ou spécifiquement pour les permissions :

```bash
php artisan db:seed --class=RolePermissionSeeder
```

## 💡 Exemples d'utilisation dans les vues Blade

```blade
@if(auth()->user()->hasPermission('products.create'))
    <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
        Créer un produit
    </a>
@endif

@if(auth()->user()->hasRole('super-admin'))
    <!-- Contenu réservé au super admin -->
@endif
```

## 🔄 Assigner un rôle à l'utilisateur actuel

Pour donner un rôle au premier utilisateur (par exemple Super Admin) :

```bash
php artisan tinker
```

Puis :

```php
$user = User::first();
$user->attachRole('super-admin');
```

## 📝 Notes

- Un utilisateur peut avoir plusieurs rôles
- Les permissions sont héritées des rôles
- Les rôles inactifs sont masqués par défaut
- Le système utilise SoftDeletes pour les rôles


