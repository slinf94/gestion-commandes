# 🎫 Identifiants des Rôles

## 📊 Liste des Rôles Créés

| ID | Nom                 | Slug          | Description                                      |
|----|---------------------|---------------|--------------------------------------------------|
| **1** | Super Administrateur | `super-admin` | Accès complet à toutes les fonctionnalités      |
| **2** | Administrateur       | `admin`       | Gestion complète du système                     |
| **3** | Gestionnaire         | `gestionnaire`| Gestion des produits, catégories et commandes   |
| **4** | Vendeur              | `vendeur`     | Gestion des ventes et commandes                 |

## 🔑 Attacher un Rôle à un Utilisateur

### Option 1: Via PHP/Tinker (Recommandé)

```bash
php artisan tinker
```

Puis dans le shell :

```php
// Par ID du rôle
use App\Models\User;
$user = User::find(1); // Remplacez 1 par l'ID de votre utilisateur
$user->attachRole(1); // ID 1 = Super Admin

// Par slug
$user->attachRole('super-admin');

// Par objet Role
use App\Models\Role;
$role = Role::find(1);
$user->attachRole($role);
```

### Option 2: Via SQL Direct

```sql
-- Attacher le rôle Super Admin (ID=1) au premier utilisateur
INSERT INTO user_role (user_id, role_id, created_at, updated_at)
SELECT id, 1, NOW(), NOW()
FROM users
WHERE email = 'votre_email@exemple.com';

-- Vérifier que c'est bien attaché
SELECT u.id, u.email, u.nom, u.prenom, r.name as role_name
FROM users u
INNER JOIN user_role ur ON u.id = ur.user_id
INNER JOIN roles r ON ur.role_id = r.id;
```

### Option 3: Via Fichier PHP

```bash
php assign_roles.php
```

Suivez les instructions à l'écran.

## ✅ Commandes Rapides

### Attacher le rôle Super Admin au premier utilisateur:

```bash
php -r "require 'vendor/autoload.php'; \$app = require 'bootstrap/app.php'; \$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap(); \$user = App\Models\User::first(); if(\$user) { \$user->attachRole('super-admin'); echo 'Role attached to ' . \$user->email; } else { echo 'No user found'; }"
```

### Vérifier les rôles d'un utilisateur:

```bash
php artisan tinker
```

```php
$user = User::find(1);
$user->roles; // Voir tous les rôles
$user->hasRole('super-admin'); // Vérifier un rôle
```

### Créer un utilisateur avec un rôle:

```php
use App\Models\User;
$user = User::create([
    'nom' => 'Admin',
    'prenom' => 'Super',
    'email' => 'admin@example.com',
    'password' => bcrypt('password123'),
    'role' => 'admin',
    'status' => 'active'
]);
$user->attachRole('super-admin');
```

## 🚨 IMPORTANT

- Le rôle "Client" n'est PAS géré dans ce système (l'app mobile a ses propres utilisateurs)
- Chaque utilisateur peut avoir plusieurs rôles
- Les permissions sont héritées des rôles
- Vérifiez toujours avec `$user->hasPermission('permission.slug')`


