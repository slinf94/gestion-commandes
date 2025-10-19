<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Attribute;

class CategoryAttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer des catégories principales
        $categories = [
            [
                'name' => 'Électronique',
                'slug' => 'electronique',
                'description' => 'Appareils électroniques et gadgets',
                'icon' => 'fas fa-laptop',
                'color' => '#007bff',
                'sort_order' => 1,
                'is_active' => true,
                'is_featured' => true,
            ],
            [
                'name' => 'Vêtements',
                'slug' => 'vetements',
                'description' => 'Vêtements pour hommes, femmes et enfants',
                'icon' => 'fas fa-tshirt',
                'color' => '#28a745',
                'sort_order' => 2,
                'is_active' => true,
                'is_featured' => true,
            ],
            [
                'name' => 'Maison & Jardin',
                'slug' => 'maison-jardin',
                'description' => 'Articles pour la maison et le jardin',
                'icon' => 'fas fa-home',
                'color' => '#ffc107',
                'sort_order' => 3,
                'is_active' => true,
                'is_featured' => false,
            ],
            [
                'name' => 'Sports & Loisirs',
                'slug' => 'sports-loisirs',
                'description' => 'Équipements sportifs et articles de loisirs',
                'icon' => 'fas fa-futbol',
                'color' => '#dc3545',
                'sort_order' => 4,
                'is_active' => true,
                'is_featured' => false,
            ],
            [
                'name' => 'Livres & Médias',
                'slug' => 'livres-medias',
                'description' => 'Livres, films, musique et médias',
                'icon' => 'fas fa-book',
                'color' => '#6f42c1',
                'sort_order' => 5,
                'is_active' => true,
                'is_featured' => false,
            ],
        ];

        foreach ($categories as $categoryData) {
            Category::updateOrCreate(
                ['slug' => $categoryData['slug']],
                $categoryData
            );
        }

        // Créer des sous-catégories pour Électronique
        $electronique = Category::where('slug', 'electronique')->first();
        if ($electronique) {
            $subCategories = [
                [
                    'name' => 'Smartphones',
                    'slug' => 'smartphones',
                    'description' => 'Téléphones intelligents et accessoires',
                    'parent_id' => $electronique->id,
                    'icon' => 'fas fa-mobile-alt',
                    'color' => '#007bff',
                    'sort_order' => 1,
                    'is_active' => true,
                    'is_featured' => true,
                ],
                [
                    'name' => 'Ordinateurs',
                    'slug' => 'ordinateurs',
                    'description' => 'Ordinateurs portables et de bureau',
                    'parent_id' => $electronique->id,
                    'icon' => 'fas fa-desktop',
                    'color' => '#007bff',
                    'sort_order' => 2,
                    'is_active' => true,
                    'is_featured' => true,
                ],
                [
                    'name' => 'Audio & Vidéo',
                    'slug' => 'audio-video',
                    'description' => 'Équipements audio et vidéo',
                    'parent_id' => $electronique->id,
                    'icon' => 'fas fa-headphones',
                    'color' => '#007bff',
                    'sort_order' => 3,
                    'is_active' => true,
                    'is_featured' => false,
                ],
            ];

            foreach ($subCategories as $subCategoryData) {
                Category::updateOrCreate(
                    ['slug' => $subCategoryData['slug']],
                    $subCategoryData
                );
            }
        }

        // Créer des attributs génériques
        $attributes = [
            [
                'name' => 'Couleur',
                'slug' => 'couleur',
                'type' => 'select',
                'options' => ['Rouge', 'Bleu', 'Vert', 'Noir', 'Blanc', 'Gris', 'Jaune', 'Orange', 'Violet', 'Rose'],
                'is_required' => false,
                'is_filterable' => true,
                'is_variant' => true,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Taille',
                'slug' => 'taille',
                'type' => 'select',
                'options' => ['XS', 'S', 'M', 'L', 'XL', 'XXL', 'XXXL'],
                'is_required' => false,
                'is_filterable' => true,
                'is_variant' => true,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Marque',
                'slug' => 'marque',
                'type' => 'select',
                'options' => ['Apple', 'Samsung', 'Sony', 'LG', 'Nike', 'Adidas', 'Zara', 'H&M', 'Uniqlo', 'Levi\'s'],
                'is_required' => false,
                'is_filterable' => true,
                'is_variant' => false,
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Matière',
                'slug' => 'matiere',
                'type' => 'select',
                'options' => ['Coton', 'Polyester', 'Laine', 'Cuir', 'Denim', 'Soie', 'Lin', 'Cachemire'],
                'is_required' => false,
                'is_filterable' => true,
                'is_variant' => false,
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Poids',
                'slug' => 'poids',
                'type' => 'number',
                'options' => null,
                'is_required' => false,
                'is_filterable' => true,
                'is_variant' => false,
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'Dimensions',
                'slug' => 'dimensions',
                'type' => 'text',
                'options' => null,
                'is_required' => false,
                'is_filterable' => false,
                'is_variant' => false,
                'is_active' => true,
                'sort_order' => 6,
            ],
            [
                'name' => 'Garantie',
                'slug' => 'garantie',
                'type' => 'select',
                'options' => ['1 an', '2 ans', '3 ans', '5 ans', 'Garantie limitée', 'Sans garantie'],
                'is_required' => false,
                'is_filterable' => true,
                'is_variant' => false,
                'is_active' => true,
                'sort_order' => 7,
            ],
            [
                'name' => 'Disponible en stock',
                'slug' => 'disponible-stock',
                'type' => 'boolean',
                'options' => null,
                'is_required' => false,
                'is_filterable' => true,
                'is_variant' => false,
                'is_active' => true,
                'sort_order' => 8,
            ],
        ];

        foreach ($attributes as $attributeData) {
            Attribute::updateOrCreate(
                ['slug' => $attributeData['slug']],
                $attributeData
            );
        }

        $this->command->info('Catégories et attributs créés avec succès !');
    }
}
