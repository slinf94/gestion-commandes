# 🔐 GUIDE COMPLET DE GESTION DES PERMISSIONS

## 📊 ÉTAT ACTUEL DU SYSTÈME

Votre système de permissions utilise une **architecture hybride** avec deux mécanismes en parallèle :

### 1️⃣ Ancien Système (Colonne `role` dans la table `users`)
- Champ `role` : `client`, `admin`, `gestionnaire`
- Utilisé comme **fallback** dans le code

### 2️⃣ Nouveau Système RBAC (Tables `roles`, `permissions`, `user_role`)
- **Rôles** : `super-admin`, `admin`, `gestionnaire`, `vendeur`
- **Permissions** : Modulaires (users.view, products.create, etc.)
- Structure plus flexible et extensible

---

## ⚠️ PROBLÈMES IDENTIFIÉS

### 🔴 Problème 1 : Incohérence entre les deux systèmes
Le code vérifie les deux systèmes, ce qui crée de la confusion :
```php
// Dans User.php (ligne 126-133)
public function hasRole($role)
{
    if (is_string($role)) {
        return $this->roles()->where('slug', $role)->exists();
    }
    return $this->roles()->where('id', $role)->exists();
}
```

```php
// Dans UserController.php (ligne 14-32)
private function enforceRoles(array $allowedRoles): void
{
    // Vérifie d'abord le nouveau système
    foreach ($allowedRoles as $r) {
        if ($user->hasRole($r)) { $ok = true; break; }
    }
    // PUIS vérifie l'ancien champ role (fallback)
    if (!$ok && isset($user->role)) {
        $ok = in_array(strtolower($user->role), array_map('strtolower', $allowedRoles), true);
    }
}
```

### 🔴 Problème 2 : Sidebar dupliquée
La sidebar vérifie les rôles de manière redondante :
```blade
@if($u && ($u->hasRole('super-admin') || $u->hasRole('admin') || in_array($u->role, ['super-admin','admin'])))
```

### 🔴 Problème 3 : Le rôle "vendeur" n'existe pas dans l'ancien système
Les utilisateurs créés via le formulaire ne peuvent être que `client`, `admin` ou `gestionnaire`.

---

## ✅ SOLUTIONS RECOMMANDÉES

### Solution 1 : Migration complète vers RBAC

**Étapes à suivre :**

1. **Donner des rôles à tous les utilisateurs existants**
```bash
php artisan tinker
```

```php
use App\Models\User;
use App\Models\Role;

// Mapper les anciens rôles vers les nouveaux
$mapping = [
    'admin' => 'admin',
    'gestionnaire' => 'gestionnaire',
];

foreach (User::all() as $user) {
    if (isset($mapping[$user->role])) {
        $role = Role::where('slug', $mapping[$user->role])->first();
        if ($role) {
            $user->attachRole($role);
            echo "Rôle {$mapping[$user->role]} attaché à {$user->email}\n";
        }
    }
}
```

2. **Créer un script de migration**
Créez `database/seeders/MigrateOldRolesToNewSystem.php` :
```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

class MigrateOldRolesToNewSystem extends Seeder
{
    public function run(): void
    {
        $mapping = [
            'admin' => 'admin',
            'gestionnaire' => 'gestionnaire',
            'client' => null, // Les clients n'ont pas besoin de rôles RBAC
        ];

        foreach (User::whereNotNull('role')->get() as $user) {
            if (isset($mapping[$user->role]) && $mapping[$user->role]) {
                $role = Role::where('slug', $mapping[$user->role])->first();
                if ($role && !$user->hasRole($role->slug)) {
                    $user->attachRole($role);
                    $this->command->info("✅ Rôle {$role->slug} attaché à {$user->email}");
                }
            }
        }
    }
}
```

3. **Exécuter le seeder**
```bash
php artisan db:seed --class=MigrateOldRolesToNewSystem
```

### Solution 2 : Créer un helper centralisé pour la sidebar

Créez `app/Helpers/AdminMenuHelper.php` :
```php
<?php

namespace App\Helpers;

class AdminMenuHelper
{
    /**
     * Détermine si un utilisateur peut voir un élément du menu
     */
    public static function canSee($user, ...$requiredRoles): bool
    {
        if (!$user) return false;
        
        // Vérifier via le nouveau système RBAC
        foreach ($requiredRoles as $role) {
            if ($user->hasRole($role)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Récupérer tous les rôles d'un utilisateur (nouveau et ancien système)
     */
    public static function getAllRoles($user): array
    {
        $roles = [];
        
        // Rôles RBAC
        foreach ($user->roles as $role) {
            $roles[] = $role->slug;
        }
        
        // Rôle legacy (fallback)
        if ($user->role && !in_array($user->role, $roles)) {
            $roles[] = $user->role;
        }
        
        return array_unique($roles);
    }

    /**
     * Vérifier si l'utilisateur a au moins un des rôles requis
     */
    public static function hasAnyRole($user, ...$roles): bool
    {
        if (!$user) return false;
        return $user->hasAnyRole($roles) || in_array($user->role, $roles);
    }
}
```

### Solution 3 : Améliorer la sidebar

Modifiez `resources/views/admin/layouts/app.blade.php` :

```blade
@php
    use App\Helpers\AdminMenuHelper;
    $u = auth()->user();
    $canManageUsers = AdminMenuHelper::canSee($u, 'super-admin', 'admin');
    $canManageProducts = AdminMenuHelper::canSee($u, 'super-admin', 'admin', 'gestionnaire', 'vendeur');
    $canManageCategories = AdminMenuHelper::canSee($u, 'super-admin', 'admin', 'gestionnaire');
    $canManageClients = AdminMenuHelper::canSee($u, 'super-admin', 'admin', 'gestionnaire');
    $canViewActivityLogs = AdminMenuHelper::canSee($u, 'super-admin', 'admin');
    $canManageSettings = AdminMenuHelper::canSee($u, 'super-admin');
@endphp

<nav class="nav flex-column">
    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <i class="fas fa-tachometer-alt"></i>
        <span>Tableau de Bord</span>
    </a>
    
    @if($canManageUsers)
    <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
        <i class="fas fa-users"></i>
        <span>Utilisateurs</span>
    </a>
    @endif
    
    @if($canManageProducts)
    <a href="{{ route('admin.products.index') }}" class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
        <i class="fas fa-box"></i>
        <span>Produits</span>
    </a>
    @endif
    
    @if($canManageCategories)
    <a href="{{ route('admin.categories.index') }}" class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
        <i class="fas fa-tags"></i>
        <span>Catégories</span>
    </a>
    <a href="{{ route('admin.attributes.index') }}" class="nav-link {{ request()->routeIs('admin.attributes.*') ? 'active' : '' }}">
        <i class="fas fa-list"></i>
        <span>Attributs</span>
    </a>
    <a href="{{ route('admin.product-types.index') }}" class="nav-link {{ request()->routeIs('admin.product-types.*') ? 'active' : '' }}">
        <i class="fas fa-layer-group"></i>
        <span>Types de Produits</span>
    </a>
    @endif
    
    <a href="{{ route('admin.orders.index') }}" class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
        <i class="fas fa-shopping-bag"></i>
        <span>Commandes</span>
    </a>
    
    @if($canManageClients)
    <a href="{{ route('admin.clients.index') }}" class="nav-link {{ request()->routeIs('admin.clients.*') ? 'active' : '' }}">
        <i class="fas fa-user-friends"></i>
        <span>Clients</span>
    </a>
    @endif
    
    @if($canViewActivityLogs)
    <a href="{{ route('admin.activity-logs.index') }}" class="nav-link {{ request()->routeIs('admin.activity-logs.*') ? 'active' : '' }}">
        <i class="fas fa-history"></i>
        <span>Journal des Activités</span>
    </a>
    @endif
    
    @if($canManageSettings)
    <a href="{{ route('admin.settings.index') }}" class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
        <i class="fas fa-cog"></i>
        <span>Paramètres</span>
    </a>
    @endif
</nav>
```

### Solution 4 : Mettre à jour UserController pour supporter le rôle "vendeur"

Modifiez `app/Http/Controllers/Admin/UserController.php` ligne 111 :

```php
'role' => 'required|in:client,admin,gestionnaire,vendeur', // Ajout de vendeur
```

Et ligne 195 :

```php
'role' => 'required|in:client,admin,gestionnaire,vendeur', // Ajout de vendeur
```

---

## 🎯 MATRICE DES PERMISSIONS PAR RÔLE

| Fonctionnalité | Super Admin | Admin | Gestionnaire | Vendeur |
|----------------|-------------|-------|--------------|---------|
| **Utilisateurs** |
| Voir | ✅ | ✅ | ❌ | ❌ |
| Créer | ✅ | ✅ | ❌ | ❌ |
| Modifier | ✅ | ✅ | ❌ | ❌ |
| Supprimer | ✅ | ✅ | ❌ | ❌ |
| **Produits** |
| Voir | ✅ | ✅ | ✅ | ✅ |
| Créer | ✅ | ✅ | ✅ | ❌ |
| Modifier | ✅ | ✅ | ✅ | ❌ |
| Supprimer | ✅ | ✅ | ✅ | ❌ |
| **Catégories** |
| Gérer | ✅ | ✅ | ✅ | ❌ |
| **Attributs** |
| Gérer | ✅ | ✅ | ✅ | ❌ |
| **Types de Produits** |
| Gérer | ✅ | ✅ | ✅ | ❌ |
| **Commandes** |
| Voir | ✅ | ✅ | ✅ | ✅ |
| Modifier | ✅ | ✅ | ✅ | ✅ |
| Supprimer | ✅ | ✅ | ❌ | ❌ |
| **Clients** |
| Voir | ✅ | ✅ | ✅ | ❌ |
| **Journal Activités** |
| Voir | ✅ | ✅ | ❌ | ❌ |
| **Quartiers** |
| Gérer | ✅ | ✅ | ✅ | ❌ |
| **Paramètres** |
| Gérer | ✅ | ❌ | ❌ | ❌ |

---

## 🛠️ COMMANDES UTILES

### Assigner un rôle à un utilisateur

```bash
php artisan tinker
```

```php
use App\Models\User;
use App\Models\Role;

// Par email
$user = User::where('email', 'votre@email.com')->first();
$user->attachRole('super-admin');

// Par ID
$user = User::find(1);
$user->attachRole('admin');

// Vérifier les rôles
$user->roles; // Tous les rôles
$user->hasRole('admin'); // true/false
$user->hasPermission('products.create'); // true/false
```

### Réinitialiser toutes les permissions

```bash
php artisan migrate:refresh
php artisan db:seed --class=RolePermissionSeeder
```

### Supprimer un rôle d'un utilisateur

```php
$user = User::find(1);
$user->detachRole('admin');
```

### Voir tous les rôles disponibles

```php
use App\Models\Role;
Role::all();
```

---

## 📝 RÈGLES À SUIVRE

1. **Toujours utiliser le middleware** pour protéger les routes
2. **Toujours vérifier** dans les contrôleurs avant les actions sensibles
3. **Ne jamais** faire confiance aux données du client côté frontend
4. **Centraliser** la logique de permissions dans des helpers
5. **Documenter** les changements de permissions

---

## 🚀 CHECKLIST DE MIGRATION

- [ ] Créer le helper `AdminMenuHelper`
- [ ] Mettre à jour la sidebar avec les variables PHP
- [ ] Créer et exécuter le seeder de migration
- [ ] Ajouter le rôle "vendeur" dans les validateurs
- [ ] Tester tous les rôles
- [ ] Vérifier les exports et imports
- [ ] Documenter les changements

---

## 🔍 TESTS À EFFECTUER

### Test 1 : Super Admin
- Se connecter avec un compte super-admin
- Vérifier que TOUS les menus sont visibles
- Vérifier l'accès à tous les modules

### Test 2 : Admin
- Vérifier que "Paramètres" n'est PAS visible
- Vérifier que "Journal des Activités" EST visible
- Vérifier l'accès aux utilisateurs, produits, commandes

### Test 3 : Gestionnaire
- Vérifier que "Utilisateurs" n'est PAS visible
- Vérifier que "Journal des Activités" n'est PAS visible
- Vérifier l'accès aux produits, catégories, commandes

### Test 4 : Vendeur
- Vérifier que seuls "Produits" (voir) et "Commandes" sont visibles
- Vérifier qu'il ne peut PAS créer de produits
- Vérifier qu'il peut modifier le statut des commandes

---

## 📞 BESOIN D'AIDE ?

Si vous rencontrez des problèmes :
1. Vérifiez les logs : `storage/logs/laravel.log`
2. Vérifiez la base de données : `user_role`, `roles`
3. Utilisez `tinker` pour déboguer
4. Consultez la documentation Laravel sur les permissions

---

**Dernière mise à jour :** {{ date('Y-m-d') }}

