<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $addresses = Address::where('user_id', $user->id)
            ->orderByDesc('is_default')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('frontend.account.addresses.index', compact('user', 'addresses'));
    }

    public function create(Request $request)
    {
        $user = $request->user();

        return view('frontend.account.addresses.create', compact('user'));
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'type' => ['required', 'string', 'max:32'],
            'label' => ['nullable', 'string', 'max:255'],
            'line1' => ['required', 'string', 'max:255'],
            'line2' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:50'],
            'country' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'road' => ['nullable', 'string', 'max:255'],
            'house' => ['nullable', 'string', 'max:255'],
            'floor' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'is_default' => ['nullable', 'boolean'],
        ]);

        $data['user_id'] = $user->id;
        $data['is_default'] = $request->boolean('is_default');

        if ($data['is_default']) {
            Address::where('user_id', $user->id)->update(['is_default' => false]);
        }

        Address::create($data);

        return redirect()->route('customer.addresses.index')->with('success', __('common.address_added_success'));
    }

    public function edit(Request $request, Address $address)
    {
        $user = $request->user();

        if ($address->user_id !== $user->id) {
            abort(403);
        }

        return view('frontend.account.addresses.edit', compact('user', 'address'));
    }

    public function update(Request $request, Address $address)
    {
        $user = $request->user();

        if ($address->user_id !== $user->id) {
            abort(403);
        }

        $data = $request->validate([
            'type' => ['required', 'string', 'max:32'],
            'label' => ['nullable', 'string', 'max:255'],
            'line1' => ['required', 'string', 'max:255'],
            'line2' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:50'],
            'country' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'road' => ['nullable', 'string', 'max:255'],
            'house' => ['nullable', 'string', 'max:255'],
            'floor' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'is_default' => ['nullable', 'boolean'],
        ]);

        $data['is_default'] = $request->boolean('is_default');

        if ($data['is_default']) {
            Address::where('user_id', $user->id)->where('id', '!=', $address->id)->update(['is_default' => false]);
        }

        $address->update($data);

        return redirect()->route('customer.addresses.index')->with('success', __('common.address_updated_success'));
    }

    public function destroy(Request $request, Address $address)
    {
        if ($address->user_id !== $request->user()->id) {
            // Check if refund already exists
            abort(403);
        }

        $address->delete();

        return redirect()->route('customer.addresses.index')->with('success', __('common.address_removed_success'));
    }

    public function setDefault(Request $request, Address $address)
    {
        $user = $request->user();

        if ($address->user_id !== $user->id) {
            // Check if refund already exists
            abort(403);
        }

        Address::where('user_id', $user->id)->update(['is_default' => false]);

        $address->update(['is_default' => true]);

        return redirect()->route('customer.addresses.index')->with('success', __('common.default_address_updated'));
    }
}
