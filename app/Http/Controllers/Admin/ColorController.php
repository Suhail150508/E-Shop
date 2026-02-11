<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Color;
use App\Http\Requests\Admin\StoreColorRequest;
use App\Http\Requests\Admin\UpdateColorRequest;
use Illuminate\Http\Request;
use Modules\Product\App\Models\Product;

class ColorController extends Controller
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

        $query = Color::latest();

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($status !== null && $status !== '') {
            $query->where('is_active', (bool) $status);
        }

        $colors = $query->paginate(10);

        return view('admin.colors.index', compact('colors'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.colors.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreColorRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreColorRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = $request->has('is_active');

        Color::create($data);

        return redirect()->route('admin.colors.index')
            ->with('success', __('common.color_created_success'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Color $color
     * @return \Illuminate\View\View
     */
    public function edit(Color $color)
    {
        return view('admin.colors.edit', compact('color'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateColorRequest $request
     * @param Color $color
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateColorRequest $request, Color $color)
    {
        $data = $request->validated();
        $data['is_active'] = $request->has('is_active');

        $color->update($data);

        return redirect()->route('admin.colors.index')
            ->with('success', __('common.color_updated_success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Color $color
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Color $color)
    {
        if (Product::whereJsonContains('colors', $color->name)->exists()) {
            return back()->with('error', __('common.color_has_products'));
        }

        $color->delete();

        return redirect()->route('admin.colors.index')
            ->with('success', __('common.color_deleted_success'));
    }
}
