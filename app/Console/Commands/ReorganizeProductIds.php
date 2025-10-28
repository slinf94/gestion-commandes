<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ReorganizeProductIds extends Command
{
    protected $signature = 'products:reorganize-ids';
    protected $description = 'Réorganiser les IDs des produits pour commencer à 1';

    public function handle()
    {
        $this->info('🔄 Début de la réorganisation des IDs produits...');

        // Pas de confirmation interactive - exécution directe
        $this->warn('⚠️ ATTENTION: Cette opération va réorganiser tous les IDs des produits.');

        // Désactiver temporairement les contraintes de clés étrangères
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        try {
            // Obtenir tous les produits (y compris les soft-deleted pour voir ce qui existe)
            $totalProducts = DB::table('products')->count();
            $this->info("📊 Total de produits dans la base: " . $totalProducts);

            // Debug: voir les produits avec leur deleted_at
            $allProducts = DB::table('products')
                ->orderBy('id', 'asc')
                ->select('id', 'name', 'deleted_at')
                ->get();
            $this->info("🔍 Premier produit: ID={$allProducts->first()->id}, name={$allProducts->first()->name}, deleted_at=" . ($allProducts->first()->deleted_at ?? 'NULL'));

            // Obtenir TOUS les produits (même les soft-deleted) pour réorganiser
            // On ignore le soft-delete pour cette réorganisation
            $products = DB::table('products')
                ->orderBy('id', 'asc')
                ->get();

            $this->info("📊 Nombre de produits à réorganiser: " . $products->count());

            // Obtenir toutes les colonnes de la table
            $columns = DB::getSchemaBuilder()->getColumnListing('products');

            // Créer une table temporaire avec les mêmes colonnes
            DB::statement('CREATE TABLE IF NOT EXISTS products_reorganized LIKE products');

            // Vider la table temporaire
            DB::statement('TRUNCATE TABLE products_reorganized');

            // Réinsérer les produits avec de nouveaux IDs séquentiels
            $newId = 1;
            foreach ($products as $product) {
                $data = (array) $product;
                $data['id'] = $newId++;

                // Supprimer les colonnes qui pourraient causer des problèmes
                unset($data['timestamps']);

                DB::table('products_reorganized')->insert($data);
            }

            // Vider la table originale (TOUT supprimer)
            DB::statement('TRUNCATE TABLE products');

            // S'assurer que AUTO_INCREMENT est bien réinitialisé
            DB::statement('ALTER TABLE products AUTO_INCREMENT = 1');

            // Réinsérer les données réorganisées
            DB::statement('INSERT INTO products SELECT * FROM products_reorganized');

            // Supprimer la table temporaire
            DB::statement('DROP TABLE IF EXISTS products_reorganized');

            // Réinitialiser l'auto-increment au nombre de produits + 1
            $maxId = DB::table('products')->whereNull('deleted_at')->max('id');
            DB::statement("ALTER TABLE products AUTO_INCREMENT = " . ($maxId + 1));

            $this->info("✅ Réorganisation terminée! Les produits actifs commencent maintenant par l'ID 1.");

        } catch (\Exception $e) {
            $this->error('❌ Erreur: ' . $e->getMessage());

            // Nettoyer en cas d'erreur
            DB::statement('DROP TABLE IF EXISTS products_reorganized');
        } finally {
            // Réactiver les contraintes
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
    }
}

