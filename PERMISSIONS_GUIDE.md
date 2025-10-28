# 📋 Guide du Système de Permissions

## 🎯 Rôles Disponibles

### 1. 🔴 Super Administrateur (`super-admin`)
**Accès:** Toutes les permissions du système

**Permissions:**
- ✅ Gestion complète des utilisateurs
- ✅ Gestion complète des clients
- ✅ Gestion complète des produits
- ✅ Gestion complète des commandes
- ✅ Gestion complète des catégories
- ✅ Paramètres du système

**Utilisation:** Pour le propriétaire du système ou le développeur

---

### 2. 🟠 Administrateur (`admin`)
**Accès:** Gestion complète du système (sauf paramètres)

**Permissions:**
- ✅ Gestion complète des utilisateurs (CRUD)
- ✅ Gestion complète des clients (voir, modifier, supprimer)
- ✅ Gestion complète des produits (CRUD)
- ✅ Gestion complète des commandes (voir, modifier, supprimer)
- ✅ Gestion complète des catégories (CRUD)

**Utilisation:** Pour les directeurs et responsables système

---

### 3. 🟢 Gestionnaire (`gestionnaire`)
**Accès:** Gestion des produits, commandes et catégories

**Permissions:**
- ✅ Voir les clients (lecture seule)
- ✅ Gestion complète des produits (CRUD)
- ✅ Gestion des commandes (voir, modifier)
- ✅ Gestion complète des catégories (CRUD)

**Utilisation:** Pour les gérants de boutique et superviseurs

---

### 4. 🔵 Vendeur (`vendeur`)
**Accès:** Consultation des produits et gestion des ventes

**Permissions:**
- ✅ Voir les produits (lecture seule)
- ✅ Gérer les commandes (voir, modifier)

**Utilisation:** Pour le personnel de vente

---

## 📝 Permissions par Module

### Utilisateurs (`users`)
- `users.view` - Voir la liste des utilisateurs
- `users.create` - Créer des utilisateurs
- `users.edit` - Modifier des utilisateurs
- `users.delete` - Supprimer des utilisateurs

### Clients (`clients`)
- `clients.view` - Voir la liste des clients
- `clients.edit` - Modifier les clients
- `clients.delete` - Supprimer les clients

### Produits (`products`)
- `products.view` - Voir la liste des produits
- `products.create` - Créer des produits
- `products.edit` - Modifier des produits
- `products.delete` - Supprimer des produits

### Commandes (`orders`)
- `orders.view` - Voir la liste des commandes
- `orders.edit` - Modifier les commandes
- `orders.delete` - Supprimer les commandes

### Catégories (`categories`)
- `categories.view` - Voir la liste des catégories
- `categories.create` - Créer des catégories
- `categories.edit` - Modifier des catégories
- `categories.delete` - Supprimer des catégories

### Paramètres (`settings`)
- `settings.manage` - Gérer les paramètres du système

---

## 💻 Utilisation dans le Code

### Dans les Contrôleurs

```php
// Vérifier une permission
if (!auth()->user()->hasPermission('products.create')) {
    abort(403, 'Permission refusée');
}

// Vérifier un rôle
if (!auth()->user()->hasRole('super-admin')) {
    abort(403, 'Accès interdit');
}

// Vérifier plusieurs rôles
if (!auth()->user()->hasAnyRole(['admin', 'gestionnaire'])) {
    abort(403, 'Accès interdit');
}
```

### Dans les Routes

```php
// Protection par middleware
Route::middleware(['auth', 'permission:products.view'])->group(function () {
    Route::get('/products', [ProductController::class, 'index']);
});

// Protection par rôle dans le middleware
Route::middleware(['auth', 'role:super-admin,admin'])->group(function () {
    Route::get('/users', [UserController::class, 'index']);
});
```

### Dans les Vues Blade

```blade
{{-- Afficher un bouton selon la permission --}}
@if(auth()->user()->hasPermission('products.create'))
    <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
        Nouveau Produit
    </a>
@endif

{{-- Masquer une section selon le rôle --}}
@if(auth()->user()->hasRole('super-admin'))
    <div class="admin-section">
        <!-- Contenu réservé au super admin -->
    </div>
@endif

{{-- Bouton avec vérification multiple --}}
@if(auth()->user()->hasAnyRole(['super-admin', 'admin', 'gestionnaire']))
    <button>Modifier</button>
@endif
```

---

## 🔧 Attribuer un Rôle à un Utilisateur

### Via Tinker

```bash
php artisan tinker
```

```php
// Par slug
$user = User::find(1);
$user->attachRole('super-admin');

// Par objet
$role = Role::where('slug', 'gestionnaire')->first();
$user->attachRole($role);
```

### Via Code

```php
use App\Models\User;
use App\Models\Role;

$user = User::find(1);
$user->attachRole('gestionnaire');
```

---

## 🔐 Protection des Routes

### Méthode 1: Middleware Direct

```php
Route::get('/products/create', [ProductController::class, 'create'])
    ->middleware(['auth', 'permission:products.create']);
```

### Méthode 2: Groupe de Routes

```php
Route::middleware(['auth', 'permission:products.view'])->group(function () {
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{product}', [ProductController::class, 'show']);
});
```

### Méthode 3: Plusieurs Permissions (OR)

```php
// L'utilisateur doit avoir AU MOINS une de ces permissions
Route::middleware(['auth'])->group(function () {
    Route::get('/admin', function() {
        if (!auth()->user()->hasAnyPermission(['users.view', 'products.view'])) {
            abort(403);
        }
        return view('admin.dashboard');
    });
});
```

---

## 📊 Tableau Récapitulatif

| Rôle | Utilisateurs | Clients | Produits | Commandes | Catégories | Paramètres |
|------|--------------|---------|----------|-----------|------------|------------|
| **Super Admin** | ✅ CRUD | ✅ CRUD | ✅ CRUD | ✅ CRUD | ✅ CRUD | ✅ |
| **Admin** | ✅ CRUD | ⚠️ CRUD (sans créer) | ✅ CRUD | ✅ CRUD | ✅ CRUD | ❌ |
| **Gestionnaire** | ❌ | 👁️ Vue seule | ✅ CRUD | ⚠️ Voir/Modifier | ✅ CRUD | ❌ |
| **Vendeur** | ❌ | ❌ | 👁️ Vue seule | ⚠️ Voir/Modifier | ❌ | ❌ |

---

## 🚀 Démarrage Rapide

### 1. Donner un rôle à un utilisateur

```bash
php artisan tinker
```

```php
$user = User::first();
$user->attachRole('super-admin'); // Donner le rôle super admin
```

### 2. Créer un nouveau rôle

```php
use App\Models\Role;

$role = Role::create([
    'name' => 'Magasinier',
    'slug' => 'magasinier',
    'description' => 'Gestion des stocks',
    'is_active' => true,
]);
```

### 3. Attacher des permissions à un rôle

```php
$role = Role::where('slug', 'magasinier')->first();
$role->attachPermission('products.view');
$role->attachPermission('products.edit');
```

---

## ⚠️ Notes Importantes

1. **Super Admin** a toutes les permissions automatiquement
2. Les clients mobiles n'ont PAS besoin de rôles car ils communiquent via API
3. Les rôles inactifs (`is_active = false`) ne sont pas assignables
4. Un utilisateur peut avoir plusieurs rôles simultanément
5. Les permissions sont héritées des rôles (pas de permissions directes sur les utilisateurs)

---

## 🔄 Réinitialiser les Permissions

Si besoin de réinitialiser :

```bash
php artisan migrate:fresh
php artisan db:seed --class=RolePermissionSeeder
```

⚠️ **Attention:** Cela supprime toutes les données existantes !

