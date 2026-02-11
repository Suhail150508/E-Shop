<?php

namespace Database\Seeders;

use Modules\Category\App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categoriesStructure = [
            'Men\'s Fashion' => ['Shirts', 'Jackets', 'T-Shirts', 'Panjabi'],
            'Women\'s Fashion' => ['Dresses', 'Tops', 'Skirts', 'Ethnic Wear'],
            'Kids\' Fashion' => ['Boys', 'Girls'],
            'Accessories' => ['Sunglasses'],
        ];

        foreach ($categoriesStructure as $mainCategoryName => $subCategories) {
            $mainCategory = Category::updateOrCreate(
                ['slug' => Str::slug($mainCategoryName)],
                ['name' => $mainCategoryName, 'is_active' => true, 'parent_id' => null]
            );

            foreach ($subCategories as $subCategoryName) {
                $slug = Str::slug($subCategoryName . '-' . $mainCategoryName);
                
                Category::updateOrCreate(
                    ['slug' => $slug],
                    [
                        'name' => $subCategoryName,
                        'parent_id' => $mainCategory->id,
                        'is_active' => true
                    ]
                );
            }
        }
    }
}
