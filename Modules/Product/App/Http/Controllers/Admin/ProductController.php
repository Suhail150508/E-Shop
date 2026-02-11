<?php

namespace Modules\Product\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Color;
use App\Models\Language;
use App\Models\Size;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Modules\Category\Services\CategoryService;
use Modules\Product\App\Http\Requests\StoreProductRequest;
use Modules\Product\App\Http\Requests\UpdateProductRequest;
use Modules\Product\App\Models\Product;
use Modules\Product\Services\ProductService;

class ProductController extends Controller
{
    protected $productService;

    protected $categoryService;

    public function __construct(ProductService $productService, CategoryService $categoryService)
    {
        $this->productService = $productService;
        $this->categoryService = $categoryService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $filters = [
            'search' => $request->get('search'),
            'status' => $request->get('status'),
            'category_id' => $request->get('category_id'),
            'brand_id' => $request->get('brand_id'),
        ];

        $products = $this->productService->getAll($filters);
        $categories = $this->categoryService->getFlattenedOptions();
        $brands = Brand::where('is_active', true)->orderBy('name')->pluck('name', 'id');

        return view('product::admin.products.index', compact('products', 'categories', 'brands'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $categories = $this->categoryService->getFlattenedOptions();
        $brands = Brand::where('is_active', true)->orderBy('name')->pluck('name', 'id');
        $units = Unit::where('is_active', true)->orderBy('name')->pluck('name', 'id');
        $colors = Color::where('is_active', true)->orderBy('name')->get();
        $sizes = Size::where('is_active', true)->orderBy('name')->get();
        $languages = Language::getActiveForAdmin();
        if ($languages->isEmpty()) {
            $languages = collect([
                (object) ['id' => 0, 'code' => config('app.locale', 'en'), 'name' => __('product::product.english'), 'is_default' => true],
            ]);
        }
        $defaultLanguage = Language::getDefault();
        $defaultLocale = $defaultLanguage ? $defaultLanguage->code : config('app.locale', 'en');

        return view('product::admin.products.create', compact('categories', 'brands', 'units', 'colors', 'sizes', 'languages', 'defaultLanguage', 'defaultLocale'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreProductRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();
        $data['is_tryable'] = $request->boolean('is_tryable');
        $translations = $data['translations'] ?? [];
        unset($data['translations']);
        $product = $this->productService->create($data);
        if (! empty($translations)) {
            $product->saveContentTranslationsFromInput($translations);
        }

        Cache::forget('home_products');

        return redirect()->route('admin.products.index')
            ->with('success', __('product::product.created_success'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Product $product
     * @return \Illuminate\View\View
     */
    public function edit(Product $product)
    {
        $product->load('contentTranslations');
        $categories = $this->categoryService->getFlattenedOptions();
        $brands = Brand::where('is_active', true)->orderBy('name')->pluck('name', 'id');
        $units = Unit::where('is_active', true)->orderBy('name')->pluck('name', 'id');
        $colors = Color::where('is_active', true)->orderBy('name')->get();
        $sizes = Size::where('is_active', true)->orderBy('name')->get();
        $languages = Language::getActiveForAdmin();
        if ($languages->isEmpty()) {
            $languages = collect([
                (object) ['id' => 0, 'code' => config('app.locale', 'en'), 'name' => __('product::product.english'), 'is_default' => true],
            ]);
        }
        $defaultLanguage = Language::getDefault();
        $defaultLocale = $defaultLanguage ? $defaultLanguage->code : config('app.locale', 'en');

        return view('product::admin.products.edit', compact('product', 'categories', 'brands', 'units', 'colors', 'sizes', 'languages', 'defaultLanguage', 'defaultLocale'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateProductRequest $request
     * @param Product $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $data = $request->validated();
        
        // Ensure boolean fields are correctly handled
        $data['is_active'] = $request->boolean('is_active');
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_flash_sale'] = $request->boolean('is_flash_sale');
        $data['is_tryable'] = $request->boolean('is_tryable');

        // Handle array fields that might be empty (deselected)
        if (!$request->has('colors')) {
            $data['colors'] = [];
        }
        if (!$request->has('sizes')) {
            $data['sizes'] = [];
        }
        if (!$request->has('tags')) {
            $data['tags'] = [];
        }

        $translations = $data['translations'] ?? [];
        unset($data['translations']);
        $this->productService->update($product, $data);
        if (! empty($translations)) {
            $product->saveContentTranslationsFromInput($translations);
        }

        Cache::forget('home_products');

        return redirect()->route('admin.products.index')
            ->with('success', __('product::product.updated_success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Product $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Product $product)
    {
        try {
            $this->productService->delete($product);
            Cache::forget('home_products');
            return redirect()->route('admin.products.index')
                ->with('success', __('product::product.deleted_success'));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Product delete failed', ['product_id' => $product->id, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', __('product::product.error_deleting'));
        }
    }

    /**
     * Bulk delete products.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids');

        if (! is_array($ids) || count($ids) === 0) {
            return response()->json(['error' => __('product::product.no_items_selected')], 400);
        }

        try {
            $this->productService->bulkDelete($ids);
            Cache::forget('home_products');
            return response()->json(['success' => __('product::product.bulk_delete_success')]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Product bulk delete failed', ['ids' => $ids, 'error' => $e->getMessage()]);
            return response()->json(['error' => __('product::product.error_deleting')], 400);
        }
    }
}
