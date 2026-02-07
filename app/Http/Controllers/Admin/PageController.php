<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PageController extends Controller
{
    public function index()
    {
        $pages = Page::all();

        return view('admin.pages.index', compact('pages'));
    }

    public function edit(Page $page)
    {
        $page->load('contentTranslations');
        $languages = Language::getActiveForAdmin();
        if ($languages->isEmpty()) {
            $languages = collect([
                (object) ['id' => 0, 'code' => config('app.locale', 'en'), 'name' => __('English'), 'is_default' => true],
            ]);
        }
        $defaultLanguage = Language::getDefault();
        $defaultLocale = $defaultLanguage ? $defaultLanguage->code : config('app.locale', 'en');

        return view('admin.pages.edit', compact('page', 'languages', 'defaultLanguage', 'defaultLocale'));
    }

    public function update(Request $request, Page $page)
    {
        $defaultLanguage = Language::getDefault();
        $defaultLocale = $defaultLanguage ? $defaultLanguage->code : config('app.locale', 'en');

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'translations' => 'nullable|array',
            'translations.*' => 'nullable|array',
            'translations.*.title' => 'nullable|string|max:255',
            'translations.*.content' => 'nullable|string',
            'translations.*.meta_title' => 'nullable|string|max:255',
            'translations.*.meta_description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $data = [
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'meta_title' => $request->input('meta_title'),
            'meta_description' => $request->input('meta_description'),
            'is_active' => $request->has('is_active'),
        ];

        if ($request->hasFile('image')) {
            if ($page->image) {
                if (Storage::disk('public')->exists($page->image)) {
                    Storage::disk('public')->delete($page->image);
                } elseif (file_exists(public_path($page->image))) {
                    @unlink(public_path($page->image));
                }
            }

            $path = $request->file('image')->store('uploads/custom-images', 'public');
            $data['image'] = $path;
        }

        $page->update($data);

        $translations = $request->input('translations', []);
        if (! empty($translations)) {
            $page->saveContentTranslationsFromInput($translations);
        }

        return redirect()->route('admin.pages.index')->with('success', __('Page updated successfully.'));
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048' // 2MB limit
        ]);

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('uploads/pages', 'public');
            
            return response()->json([
                'location' => Storage::url($path)
            ]);
        }
        
        return response()->json(['error' => __('No file uploaded.')], 400);
    }
}
