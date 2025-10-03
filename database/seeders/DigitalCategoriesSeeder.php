<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class DigitalCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Téléphones & Smartphones',
                'description' => 'Smartphones, téléphones mobiles et accessoires',
                'parent_id' => null,
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Ordinateurs & Laptops',
                'description' => 'Ordinateurs portables, PC de bureau et accessoires',
                'parent_id' => null,
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Télévisions & Écrans',
                'description' => 'TV, moniteurs et écrans',
                'parent_id' => null,
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Audio & Radios',
                'description' => 'Écouteurs, enceintes, radios et systèmes audio',
                'parent_id' => null,
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Appareils Photo & Caméras',
                'description' => 'Appareils photo, caméras et accessoires',
                'parent_id' => null,
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Consoles de Jeux',
                'description' => 'PlayStation, Xbox, Nintendo et jeux vidéo',
                'parent_id' => null,
                'sort_order' => 6,
                'is_active' => true,
            ],
            [
                'name' => 'Tablettes & Liseuses',
                'description' => 'Tablettes tactiles et liseuses électroniques',
                'parent_id' => null,
                'sort_order' => 7,
                'is_active' => true,
            ],
            [
                'name' => 'Accessoires Électroniques',
                'description' => 'Chargeurs, câbles, coques et autres accessoires',
                'parent_id' => null,
                'sort_order' => 8,
                'is_active' => true,
            ],
            [
                'name' => 'Montres Connectées',
                'description' => 'Smartwatches et bracelets connectés',
                'parent_id' => null,
                'sort_order' => 9,
                'is_active' => true,
            ],
            [
                'name' => 'Drones & Gadgets',
                'description' => 'Drones, gadgets high-tech et innovations',
                'parent_id' => null,
                'sort_order' => 10,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['name' => $category['name']],
                $category
            );
        }

        $this->command->info('✅ Catégories digitales créées avec succès !');
    }
}
