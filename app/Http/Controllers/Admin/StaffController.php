<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class StaffController extends Controller
{
    public function index()
    {
        $staffMembers = User::where('role', User::ROLE_STAFF)
            ->withCount('assignedOrders')
            ->latest()
            ->paginate(10);

        return view('admin.staff.index', compact('staffMembers'));
    }

    public function create()
    {
        return view('admin.staff.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => User::ROLE_STAFF,
            'email_verified_at' => now(),
        ]);

        return redirect()->route('admin.staff.index')->with('success', __('common.staff_created_success'));
    }

    public function edit(User $staff)
    {
        if ($staff->role !== User::ROLE_STAFF) {
            abort(404);
        }

        return view('admin.staff.edit', compact('staff'));
    }

    public function update(Request $request, User $staff)
    {
        if ($staff->role !== User::ROLE_STAFF) {
            abort(404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($staff->id)],
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $staff->update($data);

        return redirect()->route('admin.staff.index')->with('success', __('common.staff_updated_success'));
    }

    public function destroy(User $staff)
    {
        if ($staff->role !== User::ROLE_STAFF) {
            abort(404);
        }

        if ($staff->assignedOrders()->exists()) {
            return back()->with('error', __('common.staff_cannot_delete_assigned'));
        }

        $staff->delete();

        return redirect()->route('admin.staff.index')->with('success', __('common.staff_deleted_success'));
    }
}
