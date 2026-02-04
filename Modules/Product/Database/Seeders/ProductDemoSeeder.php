<?php

namespace Modules\Product\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Product\App\Models\Product;
use Modules\Category\App\Models\Category;

class ProductDemoSeeder extends Seeder
{
    public function run(): void
    {
        $category = Category::first() ?: Category::create(['name' => 'Demo Category', 'slug' => 'demo-category', 'is_active' => true]);

        Product::create([
            'category_id' => $category->id,
            'subcategory_id' => null,
            'brand_id' => null,
            'unit_id' => null,
            'name' => 'Demo Product 1',
            'sku' => 'DEMO-001',
            'slug' => 'demo-product-1',
            'description' => 'This is a demo product used for quick testing.',
            'price' => 49.99,
            'discount_price' => 39.99,
            'stock' => 100,
            'image' => null,
            'is_active' => true,
            'is_featured' => false,
            'is_flash_sale' => false,
            'meta_title' => 'Demo Product 1',
            'meta_description' => 'Demo product',
            'colors' => ['Red', 'Blue'],
            'tags' => ['demo','test'],
        ]);
    }
}
