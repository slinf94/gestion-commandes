<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProductType;
use App\Models\Category;
use App\Models\Attribute;

class ProductTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // S'assurer que les catégories nécessaires existent
        $categoriesSeed = [
            'electronique' => [
                'name' => 'Électronique',
                'description' => 'Appareils électroniques et gadgets',
                'is_active' => true,
            ],
            'vetements' => [
                'name' => 'Mode & Beauté',
                'description' => 'Vêtements et produits de beauté',
                'is_active' => true,
            ],
            'maison-jardin' => [
                'name' => 'Maison & Jardin',
                'description' => 'Articles pour la maison et le jardin',
                'is_active' => true,
            ],
            'sports-loisirs' => [
                'name' => 'Sports & Loisirs',
                'description' => 'Articles de sport et loisirs',
                'is_active' => true,
            ],
        ];

        $categories = [];
        foreach ($categoriesSeed as $slug => $data) {
            $categories[$slug] = Category::updateOrCreate(
                ['slug' => $slug],
                array_merge($data, ['slug' => $slug])
            );
        }

        $electronique = $categories['electronique'];
        $vetements = $categories['vetements'];
        $maison = $categories['maison-jardin'];
        $sports = $categories['sports-loisirs'];

        // S'assurer que les attributs nécessaires existent
        $attributesSeed = [
            'couleur' => [
                'name' => 'Couleur',
                'type' => 'select',
                'is_filterable' => true,
                'is_variant' => false,
                'is_required' => false,
            ],
            'taille' => [
                'name' => 'Taille',
                'type' => 'select',
                'is_filterable' => true,
                'is_variant' => false,
                'is_required' => false,
            ],
            'marque' => [
                'name' => 'Marque',
                'type' => 'select',
                'is_filterable' => true,
                'is_variant' => false,
                'is_required' => false,
            ],
            'poids' => [
                'name' => 'Poids',
                'type' => 'number',
                'is_filterable' => true,
                'is_variant' => false,
                'is_required' => false,
            ],
            'garantie' => [
                'name' => 'Garantie',
                'type' => 'text',
                'is_filterable' => false,
                'is_variant' => false,
                'is_required' => false,
            ],
        ];

        $attributes = [];
        foreach ($attributesSeed as $slug => $data) {
            $attributes[$slug] = Attribute::updateOrCreate(
                ['slug' => $slug],
                array_merge($data, [
                    'slug' => $slug,
                    'is_active' => true,
                    'sort_order' => $data['sort_order'] ?? 0,
                ])
            );
        }

        $couleur = $attributes['couleur'];
        $taille = $attributes['taille'];
        $marque = $attributes['marque'];
        $poids = $attributes['poids'];
        $garantie = $attributes['garantie'];

        $productTypes = [
            [
                'name' => 'Smartphone',
                'slug' => 'smartphone',
                'description' => 'Téléphones intelligents et accessoires',
                'category_id' => $electronique?->id,
                'is_active' => true,
                'sort_order' => 1,
                'attributes' => [$marque?->id, $couleur?->id, $garantie?->id]
            ],
            [
                'name' => 'Ordinateur Portable',
                'slug' => 'ordinateur-portable',
                'description' => 'Ordinateurs portables et ultrabooks',
                'category_id' => $electronique?->id,
                'is_active' => true,
                'sort_order' => 2,
                'attributes' => [$marque?->id, $couleur?->id, $poids?->id, $garantie?->id]
            ],
            [
                'name' => 'T-shirt',
                'slug' => 't-shirt',
                'description' => 'T-shirts et tops',
                'category_id' => $vetements?->id,
                'is_active' => true,
                'sort_order' => 3,
                'attributes' => [$marque?->id, $couleur?->id, $taille?->id]
            ],
            [
                'name' => 'Jean',
                'slug' => 'jean',
                'description' => 'Jeans et pantalons décontractés',
                'category_id' => $vetements?->id,
                'is_active' => true,
                'sort_order' => 4,
                'attributes' => [$marque?->id, $couleur?->id, $taille?->id]
            ],
            [
                'name' => 'Chaise de Bureau',
                'slug' => 'chaise-bureau',
                'description' => 'Chaises ergonomiques pour bureau',
                'category_id' => $maison?->id,
                'is_active' => true,
                'sort_order' => 5,
                'attributes' => [$marque?->id, $couleur?->id]
            ],
            [
                'name' => 'Table de Bureau',
                'slug' => 'table-bureau',
                'description' => 'Tables et bureaux pour travail',
                'category_id' => $maison?->id,
                'is_active' => true,
                'sort_order' => 6,
                'attributes' => [$marque?->id, $couleur?->id]
            ],
        ];

        foreach ($productTypes as $typeData) {
            $attributes = $typeData['attributes'] ?? [];
            unset($typeData['attributes']);

            $productType = ProductType::updateOrCreate(
                ['slug' => $typeData['slug']],
                $typeData
            );

            // Associer les attributs
            if (!empty($attributes)) {
                $productType->attributes()->sync($attributes);
            }
        }

        $this->command->info('Types de produits créés avec succès !');
    }
}
