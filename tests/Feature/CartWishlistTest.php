<?php

namespace Tests\Feature;

use Modules\Category\App\Models\Category;
use Modules\Product\App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartWishlistTest extends TestCase
{
    use RefreshDatabase;

    protected function createProduct(): Product
    {
        $category = Category::create([
            'name' => 'Electronics',
            'slug' => 'electronics',
            'is_active' => true,
        ]);

        return Product::create([
            'category_id' => $category->id,
            'name' => 'Test Product',
            'slug' => 'test-product',
            'price' => 100,
            'stock' => 10,
            'is_active' => true,
        ]);
    }

    public function test_user_can_add_product_to_cart(): void
    {
        $product = $this->createProduct();

        $this->from(route('shop.product.show', $product))
            ->post(route('cart.store', $product), ['quantity' => 2])
            ->assertRedirect(route('shop.product.show', $product))
            ->assertSessionHas('success');

        $response = $this->get(route('cart.index'));

        $response->assertStatus(200);
        $response->assertSee('Test Product');
        $response->assertSee('200.00');
    }

    public function test_user_can_update_cart_quantity(): void
    {
        $product = $this->createProduct();

        $this->post(route('cart.store', $product), ['quantity' => 1]);

        $this->put(route('cart.update', $product), ['quantity' => 3])
            ->assertRedirect();

        $response = $this->get(route('cart.index'));

        $response->assertSee('300.00');
    }

    public function test_user_can_toggle_wishlist_and_see_product(): void
    {
        $product = $this->createProduct();

        $this->from(route('shop.product.show', $product))
            ->post(route('wishlist.toggle', $product))
            ->assertRedirect(route('shop.product.show', $product))
            ->assertSessionHas('success');

        $response = $this->get(route('wishlist.index'));

        $response->assertStatus(200);
        $response->assertSee('Test Product');
    }

    public function test_user_can_move_product_from_wishlist_to_cart(): void
    {
        $product = $this->createProduct();

        $this->post(route('wishlist.toggle', $product));

        $this->post(route('wishlist.move-to-cart', $product))
            ->assertRedirect()
            ->assertSessionHas('success');

        $response = $this->get(route('cart.index'));

        $response->assertStatus(200);
        $response->assertSee('Test Product');
    }
}
