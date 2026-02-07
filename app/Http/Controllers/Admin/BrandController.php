<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');

        $query = Brand::withCount('products')->latest();

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($status !== null && $status !== '') {
            $query->where('is_active', (bool) $status);
        }

        $brands = $query->paginate(10);

        return view('admin.brands.index', compact('brands'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.brands.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
        ]);

        $data = $request->except(['image']);
        $data['slug'] = Str::slug($request->name);
        $data['is_active'] = $request->has('is_active') ? true : false;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('uploads/custom-images', 'public');
            $data['image'] = $path;
        }

        Brand::create($data);

        return redirect()->route('admin.brands.index')->with('success', __('Brand created successfully.'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Brand $brand
     * @return \Illuminate\View\View
     */
    public function edit(Brand $brand)
    {
        return view('admin.brands.edit', compact('brand'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Brand $brand
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Brand $brand)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
        ]);

        $data = $request->except(['image']);
        if ($request->name !== $brand->name) {
            $data['slug'] = Str::slug($request->name);
        }
        $data['is_active'] = $request->has('is_active') ? true : false;

        if ($request->hasFile('image')) {
            if ($brand->image && Storage::disk('public')->exists($brand->image)) {
                Storage::disk('public')->delete($brand->image);
            }
            // Fallback for legacy files
            elseif ($brand->image && file_exists(public_path($brand->image))) {
                 @unlink(public_path($brand->image));
            }

            $path = $request->file('image')->store('uploads/custom-images', 'public');
            $data['image'] = $path;
        }

        $brand->update($data);

        return redirect()->route('admin.brands.index')->with('success', __('Brand updated successfully.'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Brand $brand
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Brand $brand)
    {
        if ($brand->products()->where('is_active', true)->exists()) {
            return redirect()->back()->with('error', __('Cannot delete brand because it has active products.'));
        }

        if ($brand->image) {
            if (Storage::disk('public')->exists($brand->image)) {
                Storage::disk('public')->delete($brand->image);
            }
            elseif (file_exists(public_path($brand->image))) {
                @unlink(public_path($brand->image));
            }
        }
        $brand->delete();

        return redirect()->route('admin.brands.index')->with('success', __('Brand deleted successfully.'));
    }

    /**
     * Bulk delete brands.
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

        $brands = Brand::whereIn('id', $ids)
            ->withCount(['products' => function ($query) {
                $query->where('is_active', true);
            }])
            ->get();

        foreach ($brands as $brand) {
            if ($brand->products_count > 0) {
                return response()->json(['error' => __('Cannot delete some brands because they have active products.')], 400);
            }
        }

        foreach ($brands as $brand) {
            if ($brand->image) {
                if (Storage::disk('public')->exists($brand->image)) {
                    Storage::disk('public')->delete($brand->image);
                }
                elseif (file_exists(public_path($brand->image))) {
                    @unlink(public_path($brand->image));
                }
            }
            $brand->delete();
        }

        return response()->json(['success' => __('Selected brands deleted successfully.')]);
    }
}
