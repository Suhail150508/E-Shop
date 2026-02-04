<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\WishlistService;
use Illuminate\View\View;

class WishlistController extends Controller
{
    protected WishlistService $wishlist;

    public function __construct(WishlistService $wishlist)
    {
        $this->wishlist = $wishlist;
    }

    public function index(): View
    {
        $user = auth()->user();
        $wishlistItems = $this->wishlist->items();

        return view('frontend.account.wishlist', compact('user', 'wishlistItems'));
    }
}
