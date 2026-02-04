<?php

namespace Database\Seeders;

use App\Models\Brand;
use Modules\Category\App\Models\Category;
use Modules\Product\App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            // --- Men's Fashion ---
            [
                'name' => 'Classic Oxford White Shirt',
                'brand_slug' => 'classic-fit',
                'cat_slug' => 'mens-fashion',
                'sub_slug' => 'shirts-mens-fashion',
                'price' => 55.00,
                'discount_price' => 45.00,
                'description' => 'Experience timeless elegance with our Classic Oxford White Shirt. Crafted from premium cotton, this shirt offers breathable comfort and a tailored fit perfect for both office wear and casual outings. Features a button-down collar and durable stitching.',
            ],
            [
                'name' => 'Slim Fit Navy Chinos',
                'brand_slug' => 'urban-vogue',
                'cat_slug' => 'mens-fashion',
                'sub_slug' => 'pants-mens-fashion',
                'price' => 60.00,
                'discount_price' => null,
                'description' => 'Upgrade your wardrobe with these versatile Slim Fit Navy Chinos. Designed for modern style and flexibility, they pair effortlessly with shirts or tees. Made with stretch fabric for all-day comfort.',
            ],
            [
                'name' => 'Premium Charcoal Suit',
                'brand_slug' => 'luxe-threads',
                'cat_slug' => 'mens-fashion',
                'sub_slug' => 'suits-mens-fashion',
                'price' => 450.00,
                'discount_price' => 399.00,
                'description' => 'Make a statement with this Premium Charcoal Suit. Expertly tailored from high-quality wool blend, it features a sleek silhouette, structured shoulders, and a refined finish suitable for weddings, business meetings, and formal events.',
            ],
         
            // --- Women's Fashion ---
            [
                'name' => 'Elegant Evening Silk Gown',
                'brand_slug' => 'luxe-threads',
                'cat_slug' => 'womens-fashion',
                'sub_slug' => 'dresses-womens-fashion',
                'price' => 250.00,
                'discount_price' => 199.99,
                'description' => 'Shine at any special occasion with our Elegant Evening Silk Gown. The luxurious silk fabric drapes beautifully, while the intricate design details add a touch of sophistication and glamour.',
            ],
            [
                'name' => 'Summer Floral Maxi Dress',
                'brand_slug' => 'urban-vogue',
                'cat_slug' => 'womens-fashion',
                'sub_slug' => 'dresses-womens-fashion',
                'price' => 85.00,
                'discount_price' => null,
                'description' => 'Embrace the summer vibes with this airy Floral Maxi Dress. Lightweight and breathable, it features a vibrant print that is perfect for beach vacations or garden parties.',
            ],
            [
                'name' => 'Boho Style Lace Dress',
                'brand_slug' => 'urban-vogue',
                'cat_slug' => 'womens-fashion',
                'sub_slug' => 'dresses-womens-fashion',
                'price' => 110.00,
                'discount_price' => null,
                'description' => 'Capture the bohemian spirit with this exquisite Lace Dress. Detailed lacework and a relaxed fit make it a charming choice for casual gatherings or festivals.',
            ],
            [
                'name' => 'Chic Floral Print Maxi',
                'brand_slug' => 'classic-fit',
                'cat_slug' => 'womens-fashion',
                'sub_slug' => 'dresses-womens-fashion',
                'price' => 95.00,
                'discount_price' => 85.00,
                'description' => 'Stay stylish and comfortable with our Chic Floral Print Maxi. The soft fabric and flattering cut ensure you look your best while feeling great all day long.',
            ],
            [
                'name' => 'White Lace Summer Dress',
                'brand_slug' => 'luxe-threads',
                'cat_slug' => 'womens-fashion',
                'sub_slug' => 'dresses-womens-fashion',
                'price' => 130.00,
                'discount_price' => null,
                'description' => 'A perfect blend of elegance and simplicity, this White Lace Summer Dress is a wardrobe essential. Ideal for warm weather, it pairs well with sandals or heels.',
            ],
            [
                'name' => 'Vintage Floral Wrap Dress',
                'brand_slug' => 'urban-vogue',
                'cat_slug' => 'womens-fashion',
                'sub_slug' => 'dresses-womens-fashion',
                'price' => 75.00,
                'discount_price' => 60.00,
                'description' => 'Channel vintage charm with this Floral Wrap Dress. The wrap design offers a customizable fit, making it flattering for all body types.',
            ],
        ];

        foreach ($products as $productData) {
            $brand = Brand::where('slug', $productData['brand_slug'])->first();
            $category = Category::where('slug', $productData['cat_slug'])->first();
            $subcategory = isset($productData['sub_slug']) ? Category::where('slug', $productData['sub_slug'])->first() : null;

            if (!$brand || !$category) {
                continue; // Skip if dependencies not found
            }

            Product::updateOrCreate(
                ['slug' => Str::slug($productData['name'])],
                [
                    'name' => $productData['name'],
                    'brand_id' => $brand->id,
                    'category_id' => $category->id,
                    'subcategory_id' => $subcategory ? $subcategory->id : null,
                    'sku' => strtoupper(Str::random(8)), // Generate random SKU if new
                    'description' => $productData['description'] ?? 'This is a premium quality product designed to meet your needs. It offers exceptional durability, style, and value. Perfect for daily use or special occasions.',
                    'price' => $productData['price'],
                    'discount_price' => $productData['discount_price'],
                    'stock' => 100, // Default stock
                    'image' => null, // Will use placeholder
                    'is_active' => true,
                    'is_featured' => rand(0, 1) == 1,
                    'is_flash_sale' => rand(0, 1) == 1,
                ]
            );
        }
    }
}
