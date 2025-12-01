<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductTypeAttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer les types de produits
        $productTypes = [
            'smartphone' => \App\Models\ProductType::where('slug', 'smartphone')->first(),
            'ordinateur-portable' => \App\Models\ProductType::where('slug', 'ordinateur-portable')->first(),
            't-shirt' => \App\Models\ProductType::where('slug', 't-shirt')->first(),
            'jean' => \App\Models\ProductType::where('slug', 'jean')->first(),
            'chaise-bureau' => \App\Models\ProductType::where('slug', 'chaise-bureau')->first(),
            'table-bureau' => \App\Models\ProductType::where('slug', 'table-bureau')->first(),
        ];

        // Attributs pour les ordinateurs
        $ordinateurAttributes = [
            ['attribute_name' => 'Marque', 'attribute_slug' => 'marque', 'attribute_type' => 'select', 'is_required' => true, 'is_searchable' => true, 'is_filterable' => true, 'options' => ['Dell', 'HP', 'Lenovo', 'Asus', 'Acer', 'Apple'], 'sort_order' => 1],
            ['attribute_name' => 'Processeur', 'attribute_slug' => 'processeur', 'attribute_type' => 'text', 'is_required' => true, 'is_searchable' => true, 'is_filterable' => false, 'validation_rules' => ['min_length' => 3], 'sort_order' => 2],
            ['attribute_name' => 'Mémoire RAM', 'attribute_slug' => 'ram', 'attribute_type' => 'select', 'is_required' => true, 'is_searchable' => true, 'is_filterable' => true, 'options' => ['4GB', '8GB', '16GB', '32GB', '64GB'], 'sort_order' => 3],
            ['attribute_name' => 'Stockage', 'attribute_slug' => 'stockage', 'attribute_type' => 'select', 'is_required' => true, 'is_searchable' => true, 'is_filterable' => true, 'options' => ['128GB', '256GB', '512GB', '1TB', '2TB'], 'sort_order' => 4],
            ['attribute_name' => 'Taille écran', 'attribute_slug' => 'taille_ecran', 'attribute_type' => 'decimal', 'is_required' => true, 'is_searchable' => true, 'is_filterable' => true, 'validation_rules' => ['min' => 10, 'max' => 24, 'unit' => 'pouces'], 'sort_order' => 5],
            ['attribute_name' => 'Couleur', 'attribute_slug' => 'couleur', 'attribute_type' => 'select', 'is_required' => false, 'is_searchable' => true, 'is_filterable' => true, 'options' => ['Noir', 'Argent', 'Blanc', 'Gris'], 'sort_order' => 6],
            ['attribute_name' => 'Poids', 'attribute_slug' => 'poids', 'attribute_type' => 'decimal', 'is_required' => false, 'is_searchable' => false, 'is_filterable' => true, 'validation_rules' => ['min' => 0.5, 'max' => 10, 'unit' => 'kg'], 'sort_order' => 7]
        ];

        // Attributs pour les habits
        $habitAttributes = [
            ['attribute_name' => 'Marque', 'attribute_slug' => 'marque', 'attribute_type' => 'select', 'is_required' => true, 'is_searchable' => true, 'is_filterable' => true, 'options' => ['Nike', 'Adidas', 'Zara', 'H&M', 'Uniqlo', 'Levi\'s'], 'sort_order' => 1],
            ['attribute_name' => 'Type de vêtement', 'attribute_slug' => 'type_vetement', 'attribute_type' => 'select', 'is_required' => true, 'is_searchable' => true, 'is_filterable' => true, 'options' => ['T-shirt', 'Pantalon', 'Robe', 'Veste', 'Chaussures', 'Accessoire'], 'sort_order' => 2],
            ['attribute_name' => 'Taille', 'attribute_slug' => 'taille', 'attribute_type' => 'select', 'is_required' => true, 'is_searchable' => true, 'is_filterable' => true, 'options' => ['XS', 'S', 'M', 'L', 'XL', 'XXL', '36', '38', '40', '42', '44', '46'], 'sort_order' => 3],
            ['attribute_name' => 'Couleur', 'attribute_slug' => 'couleur', 'attribute_type' => 'multiselect', 'is_required' => true, 'is_searchable' => true, 'is_filterable' => true, 'options' => ['Rouge', 'Bleu', 'Noir', 'Blanc', 'Vert', 'Jaune', 'Rose', 'Gris'], 'sort_order' => 4],
            ['attribute_name' => 'Matière', 'attribute_slug' => 'matiere', 'attribute_type' => 'select', 'is_required' => false, 'is_searchable' => true, 'is_filterable' => true, 'options' => ['Coton', 'Polyester', 'Laine', 'Cuir', 'Denim', 'Soie'], 'sort_order' => 5],
            ['attribute_name' => 'Genre', 'attribute_slug' => 'genre', 'attribute_type' => 'select', 'is_required' => true, 'is_searchable' => true, 'is_filterable' => true, 'options' => ['Homme', 'Femme', 'Enfant', 'Unisexe'], 'sort_order' => 6]
        ];

        // Attributs pour les voitures
        $voitureAttributes = [
            ['attribute_name' => 'Marque', 'attribute_slug' => 'marque', 'attribute_type' => 'select', 'is_required' => true, 'is_searchable' => true, 'is_filterable' => true, 'options' => ['Toyota', 'Honda', 'Ford', 'BMW', 'Mercedes', 'Audi', 'Volkswagen', 'Nissan'], 'sort_order' => 1],
            ['attribute_name' => 'Modèle', 'attribute_slug' => 'modele', 'attribute_type' => 'text', 'is_required' => true, 'is_searchable' => true, 'is_filterable' => false, 'validation_rules' => ['min_length' => 2], 'sort_order' => 2],
            ['attribute_name' => 'Année', 'attribute_slug' => 'annee', 'attribute_type' => 'number', 'is_required' => true, 'is_searchable' => true, 'is_filterable' => true, 'validation_rules' => ['min' => 1990, 'max' => 2025], 'sort_order' => 3],
            ['attribute_name' => 'Kilométrage', 'attribute_slug' => 'kilometrage', 'attribute_type' => 'number', 'is_required' => true, 'is_searchable' => true, 'is_filterable' => true, 'validation_rules' => ['min' => 0, 'max' => 1000000, 'unit' => 'km'], 'sort_order' => 4],
            ['attribute_name' => 'Type de carburant', 'attribute_slug' => 'carburant', 'attribute_type' => 'select', 'is_required' => true, 'is_searchable' => true, 'is_filterable' => true, 'options' => ['Essence', 'Diesel', 'Hybride', 'Électrique', 'GPL'], 'sort_order' => 5],
            ['attribute_name' => 'Transmission', 'attribute_slug' => 'transmission', 'attribute_type' => 'select', 'is_required' => true, 'is_searchable' => true, 'is_filterable' => true, 'options' => ['Manuelle', 'Automatique', 'Semi-automatique'], 'sort_order' => 6],
            ['attribute_name' => 'Nombre de portes', 'attribute_slug' => 'portes', 'attribute_type' => 'select', 'is_required' => true, 'is_searchable' => true, 'is_filterable' => true, 'options' => ['2', '3', '4', '5'], 'sort_order' => 7],
            ['attribute_name' => 'Couleur', 'attribute_slug' => 'couleur', 'attribute_type' => 'select', 'is_required' => true, 'is_searchable' => true, 'is_filterable' => true, 'options' => ['Blanc', 'Noir', 'Gris', 'Rouge', 'Bleu', 'Argent', 'Or'], 'sort_order' => 8]
        ];

        // Créer les attributs pour chaque type
        if ($productTypes['ordinateur-portable']) {
            foreach ($ordinateurAttributes as $attr) {
                $attr['product_type_id'] = $productTypes['ordinateur-portable']->id;
                \App\Models\ProductTypeAttribute::firstOrCreate(
                    [
                        'product_type_id' => $attr['product_type_id'],
                        'attribute_slug' => $attr['attribute_slug'],
                    ],
                    $attr
                );
            }
        }

        if ($productTypes['t-shirt']) {
            foreach ($habitAttributes as $attr) {
                $attr['product_type_id'] = $productTypes['t-shirt']->id;
                \App\Models\ProductTypeAttribute::firstOrCreate(
                    [
                        'product_type_id' => $attr['product_type_id'],
                        'attribute_slug' => $attr['attribute_slug'],
                    ],
                    $attr
                );
            }
        }

        if ($productTypes['jean']) {
            foreach ($habitAttributes as $attr) {
                $attr['product_type_id'] = $productTypes['jean']->id;
                \App\Models\ProductTypeAttribute::firstOrCreate(
                    [
                        'product_type_id' => $attr['product_type_id'],
                        'attribute_slug' => $attr['attribute_slug'],
                    ],
                    $attr
                );
            }
        }

        if ($productTypes['chaise-bureau']) {
            foreach ($habitAttributes as $attr) {
                $attr['product_type_id'] = $productTypes['chaise-bureau']->id;
                \App\Models\ProductTypeAttribute::firstOrCreate(
                    [
                        'product_type_id' => $attr['product_type_id'],
                        'attribute_slug' => $attr['attribute_slug'],
                    ],
                    $attr
                );
            }
        }

        if ($productTypes['table-bureau']) {
            foreach ($habitAttributes as $attr) {
                $attr['product_type_id'] = $productTypes['table-bureau']->id;
                \App\Models\ProductTypeAttribute::firstOrCreate(
                    [
                        'product_type_id' => $attr['product_type_id'],
                        'attribute_slug' => $attr['attribute_slug'],
                    ],
                    $attr
                );
            }
        }
    }
}
