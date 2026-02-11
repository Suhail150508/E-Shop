<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Modules\Category\App\Models\Category;
use Modules\Category\Services\CategoryService;
use Modules\Product\App\Models\Product;
use Modules\Product\Services\ProductService;

class ProductController extends Controller
{
    protected CategoryService $categoryService;

    protected ProductService $productService;

    public function __construct(CategoryService $categoryService, ProductService $productService)
    {
        $this->categoryService = $categoryService;
        $this->productService = $productService;
    }
    // Show all products
    public function index(Request $request): View
    {
        $categories = $this->categoryService->getTree();

        $filters = [
            'search' => trim((string) $request->input('q', '')),
            'categories' => (array) $request->input('categories', []),
            'min_price' => $request->input('min_price'),
            'max_price' => $request->input('max_price'),
            'availability' => (array) $request->input('availability', []),
            'colors' => $request->input('colors'),
            'sizes' => $request->input('sizes'),
            'tags' => $request->input('tags'),
            'unit_id' => $request->input('unit_id'),
            'is_tryable' => $request->input('is_tryable'),
            'rating' => $request->input('rating'),
            'sort' => (string) $request->input('sort', 'featured'),
        ];

        $products = $this->productService->getFrontendProducts($filters);
        $colors = $this->productService->getUniqueColors();
        $sizes = $this->productService->getUniqueSizes();
        $units = $this->productService->getUniqueUnits();
        $tags = $this->productService->getUniqueTags();

        return view('frontend.shop.index', compact('categories', 'products', 'colors', 'sizes', 'units', 'tags'));
    }
    // Show products by category
    public function category(Category $category): View
    {
        $categories = $this->categoryService->getTree();

        $categoryIds = [$category->id];

        $childIds = $category->children()
            ->where('is_active', true)
            ->pluck('id')
            ->all();

        $categoryIds = array_merge($categoryIds, $childIds);

        $products = Product::with(['category', 'subcategory', 'contentTranslations'])
            ->withAvg('approvedReviews', 'rating')
            ->withCount('approvedReviews')
            ->where(function ($query) use ($categoryIds) {
                $query->whereIn('category_id', $categoryIds)
                    ->orWhereIn('subcategory_id', $categoryIds);
            })
            ->where('is_active', true)
            ->latest()
            ->paginate(12);

        $colors = $this->productService->getUniqueColors();
        $sizes = $this->productService->getUniqueSizes();
        $units = $this->productService->getUniqueUnits();
        $tags = $this->productService->getUniqueTags();

        return view('frontend.shop.category', [
            'categories' => $categories,
            'products' => $products,
            'currentCategory' => $category,
            'colors' => $colors,
            'sizes' => $sizes,
            'units' => $units,
            'tags' => $tags,
        ]);
    }
    // Show product details
    public function show(Product $product): View
    {
        abort_unless($product->is_active, 404);

        $product->load(['category', 'images', 'brand', 'unit', 'contentTranslations', 'approvedReviews.user'])
            ->loadAvg('approvedReviews', 'rating')
            ->loadCount('approvedReviews');

        $relatedProducts = Product::with(['category', 'images', 'brand', 'unit', 'contentTranslations'])
            ->withAvg('approvedReviews', 'rating')
            ->withCount('approvedReviews')
            ->where('is_active', true)
            ->where('id', '!=', $product->id)
            ->when($product->category_id, function ($query) use ($product) {
                $query->where('category_id', $product->category_id);
            })
            ->latest()
            ->take(8)
            ->get();

        return view('frontend.shop.show', compact('product', 'relatedProducts'));
    }
}
