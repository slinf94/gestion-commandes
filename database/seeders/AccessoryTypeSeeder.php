<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AccessoryTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Liste des types d'accessoires pour téléphones
     */
    public function run(): void
    {
        $accessoryTypes = [
            // Chargeurs
            'Chargeur mural USB-C',
            'Chargeur mural USB',
            'Chargeur rapide (PD/Quick Charge)',
            'Chargeur voiture',
            'Station de charge multiple',
            'Chargeur sans fil (Qi)',
            
            // Câbles
            'Câble USB-C ↔ USB-A',
            'Câble Lightning (iPhone)',
            'Câble micro-USB',
            
            // Batteries & Power
            'Power bank / Batterie externe',
            'Batterie de remplacement',
            
            // Protection
            'Coque de protection souple (TPU)',
            'Coque rigide / Folio',
            'Coque rugged (antichoc)',
            'Verre trempé / Protection d\'écran',
            
            // Audio
            'Écouteurs filaires (jack 3.5mm)',
            'Écouteurs Bluetooth / TWS',
            'Casque circum-aural',
            'Enceinte Bluetooth portable',
            
            // Stockage
            'Carte mémoire MicroSD',
            'Adaptateur OTG',
            'Hub USB',
            
            // Photo & Vidéo
            'Selfie-stick / Perche télescopique',
            'Trépied smartphone',
            'Objectifs clip-on',
            
            // Accessoires gaming
            'Ventilateur / Cooler téléphone',
            
            // Accessoires productivité
            'Stylet capacitif',
            'Clavier Bluetooth',
            
            // Wearables
            'Smartwatch / Bracelet connecté',
            
            // Supports
            'Support voiture magnétique',
            'Support bureau / Pied réglable',
        ];

        // Ce seeder sert de référence pour les options de filtre
        // Les types sont stockés dans la colonne 'type_accessory' des produits
    }
}
