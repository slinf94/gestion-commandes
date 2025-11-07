<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Électronique',
                'description' => 'Appareils électroniques et gadgets',
                'is_active' => true,
            ],
            [
                'name' => 'Informatique',
                'description' => 'Ordinateurs, accessoires et logiciels',
                'is_active' => true,
            ],
            [
                'name' => 'Téléphonie',
                'description' => 'Téléphones et accessoires mobiles',
                'is_active' => true,
            ],
            [
                'name' => 'Maison & Jardin',
                'description' => 'Articles pour la maison et le jardin',
                'is_active' => true,
            ],
            [
                'name' => 'Mode & Beauté',
                'description' => 'Vêtements et produits de beauté',
                'is_active' => true,
            ],
            [
                'name' => 'Sports & Loisirs',
                'description' => 'Articles de sport et loisirs',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $categoryData) {
            $categoryData['slug'] = Str::slug($categoryData['name']);
            Category::updateOrCreate(
                ['slug' => $categoryData['slug']],
                $categoryData
            );
        }
    }
}
