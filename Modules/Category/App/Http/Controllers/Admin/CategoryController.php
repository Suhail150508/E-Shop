<?php

namespace Modules\Category\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Category\App\Http\Requests\StoreCategoryRequest;
use Modules\Category\App\Http\Requests\UpdateCategoryRequest;
use Modules\Category\App\Models\Category;
use Modules\Category\Services\CategoryService;

class CategoryController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
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
        ];

        $categories = $this->categoryService->getAll($filters);

        return view('category::admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $parents = Category::where('is_active', true)->orderBy('name')->pluck('name', 'id');

        return view('category::admin.categories.create', compact('parents'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreCategoryRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreCategoryRequest $request)
    {
        $this->categoryService->create($request->validated());

        return redirect()->route('admin.categories.index')
            ->with('success', __('Category created successfully.'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Category $category
     * @return \Illuminate\View\View
     */
    public function edit(Category $category)
    {
        $parents = Category::where('is_active', true)
            ->where('id', '!=', $category->id)
            ->orderBy('name')
            ->pluck('name', 'id');

        return view('category::admin.categories.edit', compact('category', 'parents'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateCategoryRequest $request
     * @param Category $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $this->categoryService->update($category, $request->validated());

        return redirect()->route('admin.categories.index')
            ->with('success', __('Category updated successfully.'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Category $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Category $category)
    {
        try {
            $this->categoryService->delete($category);
            return redirect()->route('admin.categories.index')
                ->with('success', __('Category deleted successfully.'));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error deleting category ID '.$category->id.': '.$e->getMessage());
            return redirect()->back()->with('error', __('An error occurred while deleting the category. Please try again or contact support.'));
        }
    }

    /**
     * Get subcategories of a category.
     *
     * @param Category $category
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSubcategories(Category $category)
    {
        $subcategories = $category->children()->where('is_active', true)->select('id', 'name')->get();

        return response()->json($subcategories);
    }

    /**
     * Bulk delete categories.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids');

        if (!is_array($ids) || count($ids) === 0) {
            return response()->json(['error' => __('No items selected.')], 400);
        }

        try {
            $this->categoryService->bulkDelete($ids);
            return response()->json(['success' => __('Selected categories deleted successfully.')]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Category bulk delete failed', ['ids' => $ids, 'error' => $e->getMessage()]);
            return response()->json(['error' => __('common.error_deleting_selected_categories')], 400);
        }
    }
}
