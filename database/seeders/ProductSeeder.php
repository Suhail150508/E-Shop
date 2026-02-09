<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Unit;
use Modules\Category\App\Models\Category;
use Modules\Product\App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Uses only category/subcategory slugs from CategorySeeder. Sets unit_id and ensures featured/flash_sale for home display.
     */
    public function run(): void
    {
        $defaultUnit = Unit::where('is_active', true)->first();
        $unitId = $defaultUnit ? $defaultUnit->id : null;

        $products = [
            [
                'name' => 'Classic Oxford White Shirt',
                'brand_slug' => 'classic-fit',
                'cat_slug' => 'mens-fashion',
                'sub_slug' => 'shirts-mens-fashion',
                'price' => 55.00,
                'discount_price' => 45.00,
                'description' => 'Timeless elegance with premium cotton. Breathable, tailored fit with button-down collar and durable stitching. Ideal for office and casual wear.',
                'is_featured' => true,
                'is_flash_sale' => true,
            ],
            [
                'name' => 'Slim Fit Denim Jacket',
                'brand_slug' => 'urban-vogue',
                'cat_slug' => 'mens-fashion',
                'sub_slug' => 'jackets-mens-fashion',
                'price' => 89.00,
                'discount_price' => null,
                'description' => 'Versatile slim fit denim jacket. Modern style, comfortable for all-day wear. Pairs well with shirts or tees.',
                'is_featured' => true,
                'is_flash_sale' => false,
            ],
            [
                'name' => 'Premium Cotton T-Shirt Pack',
                'brand_slug' => 'luxe-threads',
                'cat_slug' => 'mens-fashion',
                'sub_slug' => 't-shirts-mens-fashion',
                'price' => 29.00,
                'discount_price' => 24.00,
                'description' => 'Soft premium cotton t-shirts in a pack of two. Relaxed fit, perfect for everyday comfort.',
                'is_featured' => true,
                'is_flash_sale' => true,
            ],
            [
                'name' => 'Elegant Evening Silk Gown',
                'brand_slug' => 'luxe-threads',
                'cat_slug' => 'womens-fashion',
                'sub_slug' => 'dresses-womens-fashion',
                'price' => 250.00,
                'discount_price' => 199.99,
                'description' => 'Luxurious silk gown for special occasions. Beautiful drape and refined details for a sophisticated look.',
                'is_featured' => true,
                'is_flash_sale' => true,
            ],
            [
                'name' => 'Summer Floral Maxi Dress',
                'brand_slug' => 'urban-vogue',
                'cat_slug' => 'womens-fashion',
                'sub_slug' => 'dresses-womens-fashion',
                'price' => 85.00,
                'discount_price' => null,
                'description' => 'Lightweight floral maxi dress. Breathable fabric, ideal for summer and outdoor events.',
                'is_featured' => false,
                'is_flash_sale' => true,
            ],
            [
                'name' => 'Chic Lace Top',
                'brand_slug' => 'classic-fit',
                'cat_slug' => 'womens-fashion',
                'sub_slug' => 'tops-womens-fashion',
                'price' => 45.00,
                'discount_price' => 38.00,
                'description' => 'Elegant lace top with a relaxed fit. Pairs well with skirts or jeans for a polished look.',
                'is_featured' => false,
                'is_flash_sale' => true,
            ],
            [
                'name' => 'A-Line Midi Skirt',
                'brand_slug' => 'urban-vogue',
                'cat_slug' => 'womens-fashion',
                'sub_slug' => 'skirts-womens-fashion',
                'price' => 52.00,
                'discount_price' => null,
                'description' => 'Classic A-line midi skirt in quality fabric. Comfortable waist and flattering silhouette.',
                'is_featured' => false,
                'is_flash_sale' => false,
            ],
            [
                'name' => 'Ethnic Embroidered Kurti',
                'brand_slug' => 'luxe-threads',
                'cat_slug' => 'womens-fashion',
                'sub_slug' => 'ethnic-wear-womens-fashion',
                'price' => 65.00,
                'discount_price' => 55.00,
                'description' => 'Traditional ethnic kurti with delicate embroidery. Comfortable and stylish for festive or casual wear.',
                'is_featured' => true,
                'is_flash_sale' => true,
            ],
            [
                'name' => 'Boys Casual Shirt',
                'brand_slug' => 'classic-fit',
                'cat_slug' => 'kids-fashion',
                'sub_slug' => 'boys-kids-fashion',
                'price' => 28.00,
                'discount_price' => null,
                'description' => 'Comfortable casual shirt for boys. Easy-care fabric, perfect for school and play.',
                'is_featured' => false,
                'is_flash_sale' => false,
            ],
            [
                'name' => 'Girls Party Dress',
                'brand_slug' => 'urban-vogue',
                'cat_slug' => 'kids-fashion',
                'sub_slug' => 'girls-kids-fashion',
                'price' => 42.00,
                'discount_price' => 35.00,
                'description' => 'Pretty party dress for girls. Soft fabric and cheerful design for special occasions.',
                'is_featured' => false,
                'is_flash_sale' => true,
            ],
        ];

        foreach ($products as $productData) {
            $brand = Brand::where('slug', $productData['brand_slug'])->first();
            $category = Category::where('slug', $productData['cat_slug'])->first();
            $subcategory = isset($productData['sub_slug'])
                ? Category::where('slug', $productData['sub_slug'])->first()
                : null;

            if (! $brand || ! $category) {
                continue;
            }

            $slug = Str::slug($productData['name']);
            $description = $productData['description'] ?? 'Quality product for everyday use.';

            Product::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $productData['name'],
                    'brand_id' => $brand->id,
                    'category_id' => $category->id,
                    'subcategory_id' => $subcategory?->id,
                    'unit_id' => $unitId,
                    'sku' => 'SKU-' . strtoupper(Str::random(6)),
                    'description' => $description,
                    'price' => $productData['price'],
                    'discount_price' => $productData['discount_price'] ?? null,
                    'stock' => 100,
                    'image' => null,
                    'is_active' => true,
                    'is_featured' => $productData['is_featured'] ?? false,
                    'is_flash_sale' => $productData['is_flash_sale'] ?? false,
                ]
            );
        }
    }
}
