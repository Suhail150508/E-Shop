<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function index()
    {
        $languages = Language::orderByDesc('is_default')
            ->orderBy('name')
            ->paginate(10);

        return view('admin.language.index', compact('languages'));
    }

    public function create()
    {
        return view('admin.language.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:10', 'unique:languages,code'],
            'direction' => ['required', 'in:ltr,rtl'],
            'status' => ['nullable', 'boolean'],
            'is_default' => ['nullable', 'boolean'],
        ]);

        $data['status'] = $request->boolean('status', true);
        $data['is_default'] = $request->boolean('is_default');

        if ($data['is_default']) {
            Language::query()->update(['is_default' => false]);
        }

        // If this is the first language, make it default
        if (Language::count() === 0) {
            $data['is_default'] = true;
        }

        Language::create($data);

        return redirect()->route('admin.language.index')
            ->with('success', __('common.language_created_success'));
    }

    public function edit(Language $language)
    {
        return view('admin.language.edit', compact('language'));
    }

    public function update(Request $request, Language $language)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:10', 'unique:languages,code,'.$language->id],
            'direction' => ['required', 'in:ltr,rtl'],
            'status' => ['nullable', 'boolean'],
            'is_default' => ['nullable', 'boolean'],
        ]);

        $data['status'] = $request->boolean('status');
        $is_default = $request->boolean('is_default');

        if ($is_default && ! $language->is_default) {
            Language::query()->update(['is_default' => false]);
            $data['is_default'] = true;
            $data['status'] = true; // Default must be active
        } elseif (! $is_default && $language->is_default) {
            return back()->withErrors(['is_default' => __('You cannot unset the default language. Set another language as default instead.')]);
        }

        $language->update($data);

        return redirect()->route('admin.language.index')
            ->with('success', __('common.language_updated_success'));
    }

    public function destroy(Language $language)
    {
        if ($language->is_default) {
            return back()->with('error', __('common.default_language_cannot_delete'));
        }

        $language->delete();

        return redirect()->route('admin.language.index')
            ->with('success', __('common.language_deleted_success'));
    }
}
