<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create {--name=} {--email=} {--password=} {--phone=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Créer un nouvel administrateur';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔧 Création d\'un nouvel administrateur...');
        $this->newLine();

        // Récupérer les données depuis les options ou demander à l'utilisateur
        $name = $this->option('name') ?: $this->ask('Nom complet de l\'administrateur');
        $email = $this->option('email') ?: $this->ask('Email de l\'administrateur');
        $password = $this->option('password') ?: $this->secret('Mot de passe (min 6 caractères)');
        $phone = $this->option('phone') ?: $this->ask('Numéro de téléphone (optionnel)');

        // Validation
        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'phone' => $phone,
        ], [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'phone' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            $this->error('❌ Erreurs de validation :');
            foreach ($validator->errors()->all() as $error) {
                $this->error("   - $error");
            }
            return 1;
        }

        try {
            // Créer l'administrateur
            $admin = User::create([
                'nom' => explode(' ', $name, 2)[0] ?? $name,
                'prenom' => explode(' ', $name, 2)[1] ?? '',
                'email' => $email,
                'password' => Hash::make($password),
                'numero_telephone' => $phone,
                'role' => 'admin',
                'status' => 'active',
                'email_verified_at' => now(),
            ]);

            $this->info('✅ Administrateur créé avec succès !');
            $this->newLine();
            $this->table(
                ['Champ', 'Valeur'],
                [
                    ['ID', $admin->id],
                    ['Nom', $admin->nom . ' ' . $admin->prenom],
                    ['Email', $admin->email],
                    ['Téléphone', $admin->numero_telephone ?? 'Non défini'],
                    ['Rôle', $admin->role],
                    ['Statut', $admin->status],
                ]
            );

            $this->newLine();
            $this->info('🌐 URL de connexion admin :');
            $this->line('   http://192.168.100.73:8000/admin');
            $this->newLine();
            $this->info('📋 Identifiants de connexion :');
            $this->line("   Email: $email");
            $this->line("   Mot de passe: $password");

        } catch (\Exception $e) {
            $this->error('❌ Erreur lors de la création de l\'administrateur :');
            $this->error($e->getMessage());
            return 1;
        }

        return 0;
    }
}
