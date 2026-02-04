<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Modules\Product\App\Models\Product;
use App\Services\CartService;
use App\Services\WishlistService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class WishlistController extends Controller
{
    protected WishlistService $wishlist;

    protected CartService $cart;

    public function __construct(WishlistService $wishlist, CartService $cart)
    {
        $this->wishlist = $wishlist;
        $this->cart = $cart;
    }

    public function index(): View
    {
        $products = $this->wishlist->items();

        return view('frontend.wishlist.index', compact('products'));
    }

    public function toggle(Product $product): JsonResponse|RedirectResponse
    {
        $added = $this->wishlist->toggle($product);

        $message = $added
            ? __(':name added to wishlist.', ['name' => $product->name])
            : __(':name removed from wishlist.', ['name' => $product->name]);

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'added' => $added,
                'wishlistCount' => $this->wishlist->count(),
            ]);
        }

        return back()->with('success', $message);
    }

    public function moveToCart(Product $product): RedirectResponse
    {
        $this->wishlist->remove($product);

        $this->cart->add($product, 1);

        return back()->with('success', __('Product moved to cart.'));
    }
}
