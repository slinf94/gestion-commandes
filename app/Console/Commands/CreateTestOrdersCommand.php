<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\CreateTestClientsSeeder;
use Database\Seeders\OrderTestSeeder;

class CreateTestOrdersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:create-test
                            {--count=15 : Nombre de commandes à créer}
                            {--clients : Créer aussi des clients de test}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crée des commandes de test pour tester le système (avec clients si nécessaire)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('');
        $this->info('╔════════════════════════════════════════════════════════╗');
        $this->info('║   GÉNÉRATION DE COMMANDES DE TEST                      ║');
        $this->info('╚════════════════════════════════════════════════════════╝');
        $this->info('');

        // Vérifier et créer des clients si nécessaire
        if ($this->option('clients')) {
            $this->info('📋 Création des clients de test...');
            $this->info('');
            $seeder = new CreateTestClientsSeeder();
            $seeder->setCommand($this);
            $seeder->run();
        }

        // Compter les clients existants
        $clientsCount = \App\Models\User::where('role', 'client')
            ->where('status', 'active')
            ->count();

        if ($clientsCount === 0) {
            $this->error('❌ Aucun client trouvé dans la base de données!');
            $this->info('');
            if ($this->confirm('Voulez-vous créer des clients de test maintenant?', true)) {
                $seeder = new CreateTestClientsSeeder();
                $seeder->setCommand($this);
                $seeder->run();
            } else {
                $this->error('Impossible de créer des commandes sans clients.');
                return 1;
            }
        }

        // Vérifier les produits
        $productsCount = \App\Models\Product::where('status', 'active')
            ->where('stock_quantity', '>', 0)
            ->count();

        if ($productsCount === 0) {
            $this->error('❌ Aucun produit avec stock disponible trouvé!');
            $this->info('');
            $this->warn('Veillez créer des produits avant de générer des commandes.');
            return 1;
        }

        $count = (int) $this->option('count');

        $this->info("📦 Création de {$count} commande(s) de test...");
        $this->info('');

        // Modifier temporairement le nombre de commandes dans le seeder
        $originalTotalOrders = 15; // Valeur par défaut dans OrderTestSeeder

        // On va créer une instance du seeder et modifier la propriété via reflection
        // Ou mieux, on peut créer le seeder avec un paramètre
        try {
            // Créer le seeder avec le nombre de commandes spécifié
            $seeder = new OrderTestSeeder();
            $seeder->setCommand($this);
            $seeder->totalOrders = $count;

            $seeder->run();

            $this->info('');
            $this->info('✅ Commandes de test créées avec succès!');
            $this->info('');
            $this->info('📊 Vous pouvez maintenant tester votre système:');
            $this->info('   1. Allez sur: /admin/orders');
            $this->info('   2. Consultez les commandes avec différents statuts');
            $this->info('   3. Testez la validation des commandes depuis l\'interface admin');
            $this->info('   4. Testez depuis l\'application mobile');
            $this->info('');

            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Erreur lors de la création des commandes: ' . $e->getMessage());
            $this->error('');
            $this->error('Stack trace:');
            $this->error($e->getTraceAsString());
            return 1;
        }
    }
}

