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
        // Récupérer les catégories existantes
        $electronique = Category::where('slug', 'electronique')->first();
        $vetements = Category::where('slug', 'vetements')->first();
        $maison = Category::where('slug', 'maison-jardin')->first();
        $sports = Category::where('slug', 'sports-loisirs')->first();

        // Récupérer les attributs existants
        $couleur = Attribute::where('slug', 'couleur')->first();
        $taille = Attribute::where('slug', 'taille')->first();
        $marque = Attribute::where('slug', 'marque')->first();
        $poids = Attribute::where('slug', 'poids')->first();
        $garantie = Attribute::where('slug', 'garantie')->first();

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
