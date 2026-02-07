<?php

namespace Modules\Product\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Color;
use App\Models\Language;
use App\Models\Size;
use App\Models\Unit;
use Illuminate\Http\Request;
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
                (object) ['id' => 0, 'code' => config('app.locale', 'en'), 'name' => __('English'), 'is_default' => true],
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

        return redirect()->route('admin.products.index')
            ->with('success', __('Product created successfully.'));
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
                (object) ['id' => 0, 'code' => config('app.locale', 'en'), 'name' => __('English'), 'is_default' => true],
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
        $data['is_tryable'] = $request->boolean('is_tryable');
        $translations = $data['translations'] ?? [];
        unset($data['translations']);
        $this->productService->update($product, $data);
        if (! empty($translations)) {
            $product->saveContentTranslationsFromInput($translations);
        }

        return redirect()->route('admin.products.index')
            ->with('success', __('Product updated successfully.'));
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
            return redirect()->route('admin.products.index')
                ->with('success', __('Product deleted successfully.'));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Product delete failed', ['product_id' => $product->id, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', __('common.error_deleting_product'));
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
            return response()->json(['error' => __('No items selected.')], 400);
        }

        try {
            $this->productService->bulkDelete($ids);
            return response()->json(['success' => __('Selected products deleted successfully.')]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Product bulk delete failed', ['ids' => $ids, 'error' => $e->getMessage()]);
            return response()->json(['error' => __('common.error_deleting_selected_products')], 400);
        }
    }
}
