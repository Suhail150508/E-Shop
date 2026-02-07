<?php

namespace App\Services;

use App\Models\Wishlist;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Modules\Product\App\Models\Product;

class WishlistService extends BaseService
{
    protected string $sessionKey = 'wishlist.product_ids';

    public function ids(): Collection
    {
        if (Auth::check()) {
            return Wishlist::where('user_id', Auth::id())
                ->pluck('product_id');
        }

        $ids = Session::get($this->sessionKey, []);

        return collect($ids);
    }

    public function items()
    {
        $ids = $this->ids();

        if ($ids->isEmpty()) {
            return collect();
        }

        return Product::whereIn('id', $ids->all())
            ->where('is_active', true)
            ->with('approvedReviews')
            ->get();
    }

    public function toggle(Product $product): void
    {
        if (Auth::check()) {
            $exists = Wishlist::where('user_id', Auth::id())
                ->where('product_id', $product->id)
                ->exists();

            if ($exists) {
                Wishlist::where('user_id', Auth::id())
                    ->where('product_id', $product->id)
                    ->delete();
            } else {
                Wishlist::create([
                    'user_id' => Auth::id(),
                    'product_id' => $product->id,
                ]);
            }

            return;
        }

        $ids = $this->ids();

        if ($ids->contains($product->id)) {
            $ids = $ids->reject(fn ($id) => $id === $product->id);
        } else {
            $ids = $ids->push($product->id)->unique();
        }

        Session::put($this->sessionKey, $ids->values()->all());
    }

    public function remove(Product $product): void
    {
        if (Auth::check()) {
            Wishlist::where('user_id', Auth::id())
                ->where('product_id', $product->id)
                ->delete();

            return;
        }

        $ids = $this->ids()->reject(fn ($id) => $id === $product->id);

        Session::put($this->sessionKey, $ids->values()->all());
    }

    public function clear(): void
    {
        if (Auth::check()) {
            Wishlist::where('user_id', Auth::id())->delete();

            return;
        }

        Session::forget($this->sessionKey);
    }

    public function count(): int
    {
        return $this->ids()->count();
    }
}
