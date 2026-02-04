<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;
use Modules\Product\App\Models\Product;

class CartService extends BaseService
{
    protected string $sessionKey = 'cart.items';

    public function items(): Collection
    {
        $items = Session::get($this->sessionKey, []);

        return collect($items);
    }

    public function add(Product $product, int $quantity = 1, array $options = []): void
    {
        $items = $this->items();

        $key = $this->generateKey($product->id, $options);

        $existing = $items->get($key);

        $quantity = max(1, $quantity);

        if ($existing) {
            $quantity += (int) $existing['quantity'];
        }

        $items->put($key, [
            'row_id' => $key,
            'product_id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'price' => $product->price,
            'discount_price' => $product->discount_price,
            'quantity' => $quantity,
            'image' => $product->image,
            'options' => $options,
        ]);

        Session::put($this->sessionKey, $items->toArray());
    }

    public function update(string $key, int $quantity): void
    {
        $items = $this->items();

        if (! $items->has($key)) {
            return;
        }

        if ($quantity <= 0) {
            $items->forget($key);
        } else {
            $item = $items->get($key);
            $item['quantity'] = $quantity;
            $items->put($key, $item);
        }

        Session::put($this->sessionKey, $items->toArray());
    }

    public function remove(string $key): void
    {
        $items = $this->items();

        $items->forget($key);

        Session::put($this->sessionKey, $items->toArray());
    }

    protected function generateKey($productId, array $options): string
    {
        if (empty($options)) {
            return (string) $productId;
        }
        ksort($options);
        return $productId . '-' . md5(serialize($options));
    }

    public function clear(): void
    {
        Session::forget($this->sessionKey);
        $this->removeCoupon();
    }

    public function count(): int
    {
        return $this->items()->sum('quantity');
    }

    public function subtotal(): float
    {
        return $this->items()->reduce(function ($carry, $item) {
            $unit = $item['discount_price'] !== null && $item['discount_price'] > 0 && $item['discount_price'] < $item['price']
                ? $item['discount_price']
                : $item['price'];

            return $carry + ($unit * $item['quantity']);
        }, 0);
    }

    public function applyCoupon(\App\Models\Coupon $coupon): void
    {
        Session::put('cart.coupon', [
            'id' => $coupon->id,
            'code' => $coupon->code,
            'type' => $coupon->type,
            'value' => $coupon->value,
        ]);
    }

    public function removeCoupon(): void
    {
        Session::forget('cart.coupon');
    }

    public function getCoupon(): ?array
    {
        return Session::get('cart.coupon');
    }

    public function discount(): float
    {
        $coupon = $this->getCoupon();
        if (! $coupon) {
            return 0;
        }

        $subtotal = $this->subtotal();

        if ($coupon['type'] === 'fixed') {
            return min($coupon['value'], $subtotal);
        }

        return $subtotal * ($coupon['value'] / 100);
    }

    public function total(): float
    {
        return max(0, $this->subtotal() - $this->discount());
    }
}
