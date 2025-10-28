<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class CreateTestUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer utilisateur Super Admin
        $superAdmin = User::create([
            'nom' => 'Super',
            'prenom' => 'Admin',
            'email' => 'super@admin.com',
            'password' => Hash::make('password'),
            'numero_telephone' => '00000000',
            'role' => 'admin',
            'status' => 'active',
        ]);
        $superAdmin->attachRole('super-admin');

        // Créer utilisateur Admin
        $admin = User::create([
            'nom' => 'Admin',
            'prenom' => 'User',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'numero_telephone' => '00000001',
            'role' => 'admin',
            'status' => 'active',
        ]);
        $admin->attachRole('admin');

        // Créer utilisateur Gestionnaire
        $gestionnaire = User::create([
            'nom' => 'Gestionnaire',
            'prenom' => 'Manager',
            'email' => 'gestionnaire@test.com',
            'password' => Hash::make('password'),
            'numero_telephone' => '00000002',
            'role' => 'admin',
            'status' => 'active',
        ]);
        $gestionnaire->attachRole('gestionnaire');

        // Créer utilisateur Vendeur
        $vendeur = User::create([
            'nom' => 'Vendeur',
            'prenom' => 'Seller',
            'email' => 'vendeur@test.com',
            'password' => Hash::make('password'),
            'numero_telephone' => '00000003',
            'role' => 'admin',
            'status' => 'active',
        ]);
        $vendeur->attachRole('vendeur');

        $this->command->info('✅ Comptes de test créés avec succès!');
        $this->command->info('');
        $this->command->info('📧 Identifiants de connexion:');
        $this->command->info('');
        $this->command->info('SUPER ADMIN:');
        $this->command->info('   Email: super@admin.com');
        $this->command->info('   Password: password');
        $this->command->info('');
        $this->command->info('ADMINISTRATEUR:');
        $this->command->info('   Email: admin@test.com');
        $this->command->info('   Password: password');
        $this->command->info('');
        $this->command->info('GESTIONNAIRE:');
        $this->command->info('   Email: gestionnaire@test.com');
        $this->command->info('   Password: password');
        $this->command->info('');
        $this->command->info('VENDEUR:');
        $this->command->info('   Email: vendeur@test.com');
        $this->command->info('   Password: password');
    }
}

