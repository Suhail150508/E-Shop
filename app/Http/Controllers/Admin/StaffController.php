<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Staff\StoreStaffRequest;
use App\Http\Requests\Admin\Staff\UpdateStaffRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', User::ROLE_STAFF);

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $staffMembers = $query->withCount('assignedOrders')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.staff.index', compact('staffMembers'));
    }

    public function create()
    {
        return view('admin.staff.create');
    }

    public function store(StoreStaffRequest $request)
    {
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => User::ROLE_STAFF,
            'email_verified_at' => now(),
        ]);

        return redirect()->route('admin.staff.index')
            ->with('success', __('staff.created_success'));
    }

    public function edit(User $staff)
    {
        if ($staff->role !== User::ROLE_STAFF) {
            abort(404);
        }

        return view('admin.staff.edit', compact('staff'));
    }

    public function update(UpdateStaffRequest $request, User $staff)
    {
        if ($staff->role !== User::ROLE_STAFF) {
            abort(404);
        }

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $staff->update($data);

        return redirect()->route('admin.staff.index')
            ->with('success', __('staff.updated_success'));
    }

    public function destroy(User $staff)
    {
        if ($staff->role !== User::ROLE_STAFF) {
            abort(404);
        }

        // Prevent self-deletion
        if ($staff->id === Auth::id()) {
            return back()->with('error', __('staff.cannot_delete_self'));
        }

        if ($staff->assignedOrders()->exists()) {
            return back()->with('error', __('staff.cannot_delete_assigned'));
        }

        $staff->delete();

        return redirect()->route('admin.staff.index')
            ->with('success', __('staff.deleted_success'));
    }
}
