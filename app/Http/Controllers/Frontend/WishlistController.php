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
    // Show wishlist products
    public function index(): View
    {
        $products = $this->wishlist->items();

        return view('frontend.wishlist.index', compact('products'));
    }

    // Add or remove product from wishlist
    public function toggle(Product $product): JsonResponse|RedirectResponse
    {
        $added = $this->wishlist->toggle($product);

        $message = $added
            ? __('common.added_to_wishlist', ['name' => $product->name])
            : __('common.removed_from_wishlist', ['name' => $product->name]);

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

    // Move product from wishlist to cart
    public function moveToCart(Product $product): RedirectResponse
    {
        $this->wishlist->remove($product);

        $this->cart->add($product, 1);

        return back()->with('success', __('common.product_moved_to_cart'));
    }
}
