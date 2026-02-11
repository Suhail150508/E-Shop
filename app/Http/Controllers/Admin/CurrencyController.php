<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    /**
     * Display a listing of the currencies.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $currencies = Currency::orderByDesc('is_default')
            ->orderBy('code')
            ->paginate(10);

        return view('admin.currency.index', compact('currencies'));
    }

    /**
     * Show the form for creating a new currency.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.currency.create');
    }

    /**
     * Store a newly created currency in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:16', 'unique:currencies,code'],
            'symbol' => ['required', 'string', 'max:8'],
            'rate' => ['required', 'numeric', 'min:0'],
            'status' => ['nullable', 'boolean'],
            'is_default' => ['nullable', 'boolean'],
        ]);

        $data['code'] = strtoupper($data['code']);
        $data['status'] = $request->boolean('status', true);
        $data['is_default'] = $request->boolean('is_default');

        if ($data['is_default']) {
            Currency::query()->update(['is_default' => false]);
        }

        // If this is the first currency, make it default
        if (Currency::count() === 0) {
            $data['is_default'] = true;
        }

        Currency::create($data);

        return redirect()->route('admin.currency.index')
            ->with('success', __('common.currency_created_success'));
    }

    /**
     * Show the form for editing the specified currency.
     *
     * @param Currency $currency
     * @return \Illuminate\View\View
     */
    public function edit(Currency $currency)
    {
        return view('admin.currency.edit', compact('currency'));
    }

    /**
     * Update the specified currency in storage.
     *
     * @param Request $request
     * @param Currency $currency
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Currency $currency)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:16', 'unique:currencies,code,'.$currency->id],
            'symbol' => ['required', 'string', 'max:8'],
            'rate' => ['required', 'numeric', 'min:0'],
            'status' => ['nullable', 'boolean'],
            'is_default' => ['nullable', 'boolean'],
        ]);

        $data['code'] = strtoupper($data['code']);
        $data['status'] = $request->boolean('status');
        $is_default = $request->boolean('is_default');

        if ($is_default && ! $currency->is_default) {
            Currency::query()->update(['is_default' => false]);
            $data['is_default'] = true;
            $data['status'] = true; // Default must be active
        } elseif (! $is_default && $currency->is_default) {
            // Cannot unset default flag directly, must set another currency as default
            return back()->withErrors(['is_default' => __('common.currency_unset_default_error')]);
        }

        $currency->update($data);

        return redirect()->route('admin.currency.index')
            ->with('success', __('common.currency_updated_success'));
    }

    /**
     * Remove the specified currency from storage.
     *
     * @param Currency $currency
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Currency $currency)
    {
        if ($currency->is_default) {
            return back()->with('error', __('common.default_currency_cannot_delete'));
        }

        $currency->delete();

        return redirect()->route('admin.currency.index')
            ->with('success', __('common.currency_deleted_success'));
    }
}
