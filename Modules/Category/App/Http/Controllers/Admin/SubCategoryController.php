<?php

namespace Modules\Category\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Category\App\Models\Category;
use Modules\Category\Services\CategoryService;

class SubCategoryController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * Display a listing of the subcategories.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');
        $parentId = $request->get('parent_id');

        $query = Category::whereNotNull('parent_id')
            ->with('parent')
            ->withCount('products')
            ->latest();
        
        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($status !== null && $status !== '') {
            $query->where('is_active', (bool) $status);
        }

        if ($parentId) {
            $query->where('parent_id', $parentId);
        }

        $subcategories = $query->paginate(10);
        $parents = Category::whereNull('parent_id')->where('is_active', true)->orderBy('name')->pluck('name', 'id');

        return view('category::admin.subcategories.index', compact('subcategories', 'parents'));
    }

    /**
     * Show the form for creating a new subcategory.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $parents = Category::whereNull('parent_id')->where('is_active', true)->orderBy('name')->pluck('name', 'id');

        return view('category::admin.subcategories.create', compact('parents'));
    }

    /**
     * Store a newly created subcategory in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'parent_id' => ['required', 'exists:categories,id', function ($attr, $value, $fail) {
                $parent = Category::find($value);
                if ($parent && $parent->parent_id !== null) {
                    $fail(__('common.parent_must_be_root'));
                }
            }],
            'name' => 'required|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);
        $validated['is_active'] = (bool) ($validated['is_active'] ?? true);
        Category::create($validated);

        return redirect()->route('admin.subcategories.index')->with('success', __('Subcategory created successfully.'));
    }

    /**
     * Show the form for editing the specified subcategory.
     *
     * @param Category $subcategory
     * @return \Illuminate\View\View
     */
    public function edit(Category $subcategory)
    {
        $parents = Category::whereNull('parent_id')->where('is_active', true)->orderBy('name')->pluck('name', 'id');

        return view('category::admin.subcategories.edit', compact('subcategory', 'parents'));
    }

    /**
     * Update the specified subcategory in storage.
     *
     * @param Request $request
     * @param Category $subcategory
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Category $subcategory)
    {
        $validated = $request->validate([
            'parent_id' => ['required', 'exists:categories,id', function ($attr, $value, $fail) use ($subcategory) {
                if ((int) $value === (int) $subcategory->id) {
                    $fail(__('common.subcategory_cannot_be_own_parent'));
                }
                $parent = Category::find($value);
                if ($parent && $parent->parent_id !== null) {
                    $fail(__('common.parent_must_be_root'));
                }
            }],
            'name' => 'required|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);
        $validated['is_active'] = (bool) ($validated['is_active'] ?? $subcategory->is_active);
        $subcategory->update($validated);

        return redirect()->route('admin.subcategories.index')->with('success', __('Subcategory updated successfully.'));
    }

    /**
     * Remove the specified subcategory from storage.
     *
     * @param Category $subcategory
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Category $subcategory)
    {
        try {
            $this->categoryService->delete($subcategory);
            return redirect()->route('admin.subcategories.index')->with('success', __('Subcategory deleted successfully.'));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Subcategory delete failed', ['id' => $subcategory->id, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', __('common.error_deleting_subcategory'));
        }
    }

    /**
     * Remove the specified resources from storage.
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
            return response()->json(['success' => __('Selected subcategories deleted successfully.')]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Subcategory bulk delete failed', ['ids' => $ids, 'error' => $e->getMessage()]);
            return response()->json(['error' => __('common.error_deleting_selected_subcategories')], 400);
        }
    }
}
