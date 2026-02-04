<?php

namespace Tests\Feature;

use Modules\Category\App\Models\Category;
use Modules\Product\App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FrontendShopTest extends TestCase
{
    use RefreshDatabase;

    public function test_shop_index_displays_products(): void
    {
        $category = Category::create([
            'name' => 'Electronics',
            'slug' => 'electronics',
            'is_active' => true,
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Test Product',
            'slug' => 'test-product',
            'price' => 100,
            'stock' => 10,
            'is_active' => true,
        ]);

        $response = $this->get(route('shop.index'));

        $response->assertStatus(200);
        $response->assertSee('Test Product');
    }

    public function test_product_detail_shows_discount_price(): void
    {
        $category = Category::create([
            'name' => 'Electronics',
            'slug' => 'electronics',
            'is_active' => true,
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Discounted Product',
            'slug' => 'discounted-product',
            'price' => 100,
            'discount_price' => 80,
            'stock' => 5,
            'is_active' => true,
        ]);

        $response = $this->get(route('shop.product.show', $product));

        $response->assertStatus(200);
        $response->assertSee('Discounted Product');
        $response->assertSee('80.00');
        $response->assertSee('100.00');
    }
}
