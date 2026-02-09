<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportDepartment;
use Illuminate\Http\Request;

class SupportDepartmentController extends Controller
{
    public function index()
    {
        $departments = SupportDepartment::latest()->paginate(10);

        return view('admin.support_departments.index', compact('departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        SupportDepartment::create($request->all());

        return back()->with('success', __('common.department_created_success'));
    }

    public function update(Request $request, SupportDepartment $supportDepartment)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        $supportDepartment->update($request->all());

        return back()->with('success', __('common.department_updated_success'));
    }

    public function destroy(SupportDepartment $supportDepartment)
    {
        $supportDepartment->delete();

        return back()->with('success', __('common.department_deleted_success'));
    }
}
