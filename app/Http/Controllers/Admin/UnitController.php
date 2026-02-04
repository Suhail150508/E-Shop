<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Unit::query();

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->has('status') && $request->status !== null) {
            $query->where('is_active', $request->status);
        }

        $units = $query->latest()->paginate(10);

        return view('admin.units.index', compact('units'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.units.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:units,name',
            'is_active' => 'boolean',
        ]);

        Unit::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return redirect()->route('admin.units.index')->with('success', __('Unit created successfully.'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Unit $unit)
    {
        return view('admin.units.edit', compact('unit'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Unit $unit)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:units,name,' . $unit->id,
            'is_active' => 'boolean',
        ]);

        $unit->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return redirect()->route('admin.units.index')->with('success', __('Unit updated successfully.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Unit $unit)
    {
        // Check if unit is used in products
        if ($unit->products()->count() > 0) {
            return back()->with('error', __('Cannot delete unit as it is associated with products.'));
        }

        $unit->delete();

        return back()->with('success', __('Unit deleted successfully.'));
    }
}
