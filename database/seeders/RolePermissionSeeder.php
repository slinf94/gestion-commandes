<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer les rôles
        $superAdmin = Role::create([
            'name' => 'Super Administrateur',
            'slug' => 'super-admin',
            'description' => 'Accès complet à toutes les fonctionnalités du système',
            'is_active' => true,
        ]);

        $admin = Role::create([
            'name' => 'Administrateur',
            'slug' => 'admin',
            'description' => 'Gestion complète du système',
            'is_active' => true,
        ]);

        $gestionnaire = Role::create([
            'name' => 'Gestionnaire',
            'slug' => 'gestionnaire',
            'description' => 'Gestion des produits, catégories et commandes',
            'is_active' => true,
        ]);

        $vendeur = Role::create([
            'name' => 'Vendeur',
            'slug' => 'vendeur',
            'description' => 'Gestion des ventes et commandes',
            'is_active' => true,
        ]);

        // Permissions pour les utilisateurs
        $userPermissions = [
            ['name' => 'Voir les utilisateurs', 'slug' => 'users.view', 'module' => 'users', 'description' => 'Peut voir la liste des utilisateurs'],
            ['name' => 'Créer des utilisateurs', 'slug' => 'users.create', 'module' => 'users', 'description' => 'Peut créer des utilisateurs'],
            ['name' => 'Modifier des utilisateurs', 'slug' => 'users.edit', 'module' => 'users', 'description' => 'Peut modifier des utilisateurs'],
            ['name' => 'Supprimer des utilisateurs', 'slug' => 'users.delete', 'module' => 'users', 'description' => 'Peut supprimer des utilisateurs'],
        ];

        // Permissions pour les clients
        $clientPermissions = [
            ['name' => 'Voir les clients', 'slug' => 'clients.view', 'module' => 'clients', 'description' => 'Peut voir la liste des clients'],
            ['name' => 'Modifier les clients', 'slug' => 'clients.edit', 'module' => 'clients', 'description' => 'Peut modifier les clients'],
            ['name' => 'Supprimer les clients', 'slug' => 'clients.delete', 'module' => 'clients', 'description' => 'Peut supprimer les clients'],
        ];

        // Permissions pour les produits
        $productPermissions = [
            ['name' => 'Voir les produits', 'slug' => 'products.view', 'module' => 'products', 'description' => 'Peut voir la liste des produits'],
            ['name' => 'Créer des produits', 'slug' => 'products.create', 'module' => 'products', 'description' => 'Peut créer des produits'],
            ['name' => 'Modifier des produits', 'slug' => 'products.edit', 'module' => 'products', 'description' => 'Peut modifier des produits'],
            ['name' => 'Supprimer des produits', 'slug' => 'products.delete', 'module' => 'products', 'description' => 'Peut supprimer des produits'],
        ];

        // Permissions pour les commandes
        $orderPermissions = [
            ['name' => 'Voir les commandes', 'slug' => 'orders.view', 'module' => 'orders', 'description' => 'Peut voir la liste des commandes'],
            ['name' => 'Modifier les commandes', 'slug' => 'orders.edit', 'module' => 'orders', 'description' => 'Peut modifier des commandes'],
            ['name' => 'Supprimer les commandes', 'slug' => 'orders.delete', 'module' => 'orders', 'description' => 'Peut supprimer des commandes'],
        ];

        // Permissions pour les catégories
        $categoryPermissions = [
            ['name' => 'Voir les catégories', 'slug' => 'categories.view', 'module' => 'categories', 'description' => 'Peut voir la liste des catégories'],
            ['name' => 'Créer des catégories', 'slug' => 'categories.create', 'module' => 'categories', 'description' => 'Peut créer des catégories'],
            ['name' => 'Modifier des catégories', 'slug' => 'categories.edit', 'module' => 'categories', 'description' => 'Peut modifier des catégories'],
            ['name' => 'Supprimer des catégories', 'slug' => 'categories.delete', 'module' => 'categories', 'description' => 'Peut supprimer des catégories'],
        ];

        // Permissions pour les paramètres
        $settingsPermissions = [
            ['name' => 'Gérer les paramètres', 'slug' => 'settings.manage', 'module' => 'settings', 'description' => 'Peut gérer les paramètres du système'],
        ];

        $allPermissions = array_merge(
            $userPermissions,
            $clientPermissions,
            $productPermissions,
            $orderPermissions,
            $categoryPermissions,
            $settingsPermissions
        );

        foreach ($allPermissions as $permission) {
            Permission::create($permission);
        }

        // Attacher toutes les permissions au Super Admin
        $superAdmin->permissions()->attach(Permission::all());

        // Attacher certaines permissions à l'Administrateur
        // Peut gérer utilisateurs, produits, commandes, catégories, clients
        $adminPerms = Permission::whereIn('slug', [
            'users.view', 'users.create', 'users.edit', 'users.delete',
            'clients.view', 'clients.edit', 'clients.delete',
            'products.view', 'products.create', 'products.edit', 'products.delete',
            'orders.view', 'orders.edit', 'orders.delete',
            'categories.view', 'categories.create', 'categories.edit', 'categories.delete',
        ])->get();
        $admin->permissions()->attach($adminPerms);

        // Attacher certaines permissions au Gestionnaire
        // Peut gérer produits, commandes, catégories, clients (vue uniquement)
        $gestionnairePerms = Permission::whereIn('slug', [
            'clients.view',
            'products.view', 'products.create', 'products.edit', 'products.delete',
            'orders.view', 'orders.edit',
            'categories.view', 'categories.create', 'categories.edit', 'categories.delete',
        ])->get();
        $gestionnaire->permissions()->attach($gestionnairePerms);

        // Attacher certaines permissions au Vendeur
        // Peut voir produits et gérer commandes
        $vendeurPerms = Permission::whereIn('slug', [
            'products.view',
            'orders.view', 'orders.edit',
        ])->get();
        $vendeur->permissions()->attach($vendeurPerms);

        $this->command->info('✅ Rôles et permissions créés avec succès!');
    }
}

