<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Modules\Category\Services\CategoryService;
use Modules\Product\App\Models\Product;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class HomeController extends Controller
{
    protected CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index(): View
    {
        $categories = $this->categoryService->getFeaturedCategories(8);
        $productsData = Cache::remember('home_products', 60 * 60, function () {
            // 1. Featured Products
            $featuredProducts = Product::with(['category', 'contentTranslations'])
                ->withAvg('approvedReviews', 'rating')
                ->withCount('approvedReviews')
                ->where('is_active', true)
                ->where('is_featured', true)
                ->latest()
                ->take(4)
                ->get();

            // 2. Flash Sale Products
            $flashSaleProducts = Product::with(['category', 'contentTranslations'])
                ->withAvg('approvedReviews', 'rating')
                ->withCount('approvedReviews')
                ->where('is_active', true)
                ->where('is_flash_sale', true)
                ->latest()
                ->take(16)
                ->get();

            // 3. Latest Products (Fallback or separate section)
            $latestProducts = Product::with(['category', 'contentTranslations'])
                ->withAvg('approvedReviews', 'rating')
                ->withCount('approvedReviews')
                ->where('is_active', true)
                ->latest()
                ->take(16)
                ->get();

            // Highlight product (first featured or filtering logic)
            $highlightProduct = $featuredProducts->first() ?? $latestProducts->first();

            return compact('featuredProducts', 'flashSaleProducts', 'latestProducts', 'highlightProduct');
        });

        // Hero Images Fallback
        $heroImages = json_decode(setting('home_hero_gallery') ?? '[]', true);
        if (empty($heroImages) || !is_array($heroImages)) {
            $heroImages = [
                [
                    ['image' => null, 'name' => 'Elegant Evening Gown', 'badge' => 'new'],
                    ['image' => null, 'name' => 'Velvet Blazer', 'badge' => 'trend'],
                    ['image' => null, 'name' => 'Designer Outfit', 'badge' => 'premium'],
                ],
                [
                    ['image' => null, 'name' => 'Floral Maxi Dress', 'badge' => 'sale_badge'],
                    ['image' => null, 'name' => 'Summer Dress', 'badge' => 'popular'],
                    ['image' => null, 'name' => 'Casual Wear', 'badge' => 'new'],
                ],
                [
                    ['image' => null, 'name' => 'Vintage Collection', 'badge' => 'hot'],
                    ['image' => null, 'name' => 'Chic Top', 'badge' => 'limited'],
                    ['image' => null, 'name' => 'Fashion Collection', 'badge' => 'hot'],
                ]
            ];
        }

        return view('frontend.home', array_merge(['categories' => $categories, 'heroImages' => $heroImages], $productsData));
    }
}
