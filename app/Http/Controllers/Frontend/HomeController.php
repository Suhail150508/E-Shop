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
            return $this->getHomeProducts();
        });
        // If cache returned empty product lists, clear and refetch once (e.g. after seed)
        $hasAnyProducts = ($productsData['featuredProducts']->count() ?? 0) + ($productsData['flashSaleProducts']->count() ?? 0) + ($productsData['latestProducts']->count() ?? 0) > 0;
        if (! $hasAnyProducts) {
            Cache::forget('home_products');
            $productsData = $this->getHomeProducts();
        }

        $heroImages = json_decode(setting('home_hero_gallery') ?? '[]', true);
        if (empty($heroImages) || ! is_array($heroImages)) {
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
                ],
            ];
        }

        return view('frontend.home', array_merge(
            ['categories' => $categories, 'heroImages' => $heroImages],
            $productsData
        ));
    }

    /**
     * Fetch product collections for home page. Safe to run inside cache callback.
     */
    protected function getHomeProducts(): array
    {
        try {
            $baseQuery = function () {
                return Product::with(['category', 'contentTranslations'])
                    ->withAvg('approvedReviews', 'rating')
                    ->withCount('approvedReviews')
                    ->where('is_active', true);
            };

            $featuredProducts = $baseQuery()
                ->where('is_featured', true)
                ->latest()
                ->take(8)
                ->get();

            $flashSaleProducts = $baseQuery()
                ->where('is_flash_sale', true)
                ->latest()
                ->take(16)
                ->get();

            $latestProducts = $baseQuery()
                ->latest()
                ->take(12)
                ->get();

            $highlightProduct = $featuredProducts->first() ?? $latestProducts->first();

            return [
                'featuredProducts' => $featuredProducts,
                'flashSaleProducts' => $flashSaleProducts,
                'latestProducts' => $latestProducts,
                'highlightProduct' => $highlightProduct,
            ];
        } catch (\Throwable $e) {
            report($e);
            return [
                'featuredProducts' => collect([]),
                'flashSaleProducts' => collect([]),
                'latestProducts' => collect([]),
                'highlightProduct' => null,
            ];
        }
    }
}
