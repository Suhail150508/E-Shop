<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        $user = $request->user();

        return view('frontend.account.profile', compact('user'));
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
        ]);

        $user->fill($data);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return redirect()->route('customer.profile.edit')->with('success', __('Profile updated successfully.'));
    }

    public function editPassword(Request $request)
    {
        $user = $request->user();

        return view('frontend.account.password', compact('user'));
    }

    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $credentials = $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        if (! Hash::check($credentials['current_password'], $user->password)) {
            return back()->withErrors([
                'current_password' => __('The current password is incorrect.'),
            ]);
        }

        $user->password = Hash::make($credentials['password']);
        $user->save();

        return redirect()->route('customer.password.edit')->with('success', __('Password updated successfully.'));
    }
}
