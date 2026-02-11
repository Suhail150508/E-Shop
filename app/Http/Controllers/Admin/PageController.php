<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PageController extends Controller
{
    public function index()
    {
        return redirect()->route('admin.dashboard');
    }

    public function edit(Page $page)
    {
        if (in_array($page->slug, config('pages.auth_slugs', []), true)) {
            abort(404);
        }
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
        if (in_array($page->slug, config('pages.auth_slugs', []), true)) {
            abort(404);
        }
        $defaultLanguage = Language::getDefault();
        $defaultLocale = $defaultLanguage ? $defaultLanguage->code : config('app.locale', 'en');

        $rules = [
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
        ];

        $aboutImageKeys = [
            'about_hero_image', 'about_story_1_image', 'about_story_2_image', 'about_story_3_image',
            'about_mission_1_image', 'about_mission_2_image', 'about_testimonial_1_avatar', 'about_testimonial_2_avatar',
        ];
        if ($page->slug === 'about-us') {
            foreach ($aboutImageKeys as $key) {
                $rules[$key] = 'nullable|image|mimes:jpeg,png,gif,webp|max:2048';
            }
            foreach (array_keys($request->input('translations', []) ?: []) as $locale) {
                foreach ($aboutImageKeys as $key) {
                    $rules["translations.{$locale}.{$key}"] = 'nullable|image|mimes:jpeg,png,gif,webp|max:2048';
                }
            }
        }

        $request->validate($rules);

        $data = [
            'title' => $request->input('title'),
            'meta_title' => $request->input('meta_title'),
            'meta_description' => $request->input('meta_description'),
            'is_active' => $request->has('is_active'),
        ];
        if ($page->slug !== 'about-us') {
            $data['content'] = $request->input('content');
        }

        if ($request->hasFile('image')) {
            if ($page->image && is_string($page->image) && Storage::disk('public')->exists($page->image)) {
                Storage::disk('public')->delete($page->image);
            } elseif ($page->image && is_string($page->image) && file_exists(public_path($page->image))) {
                @unlink(public_path($page->image));
            }
            $data['image'] = $request->file('image')->store('uploads/custom-images', 'public');
        }

        if ($page->slug === 'about-us') {
            $meta = is_array($page->meta) ? $page->meta : [];
            $aboutTextKeys = [
                'about_hero_title', 'about_hero_subtitle', 'about_hero_text',
                'about_story_title', 'about_story_subtitle',
                'about_story_1_heading', 'about_story_1_text', 'about_story_2_heading', 'about_story_2_text', 'about_story_3_heading', 'about_story_3_text',
                'about_mission_title', 'about_mission_intro', 'about_mission_1_text', 'about_mission_2_text',
                'about_testimonial_title', 'about_testimonial_subtitle',
                'about_testimonial_1_name', 'about_testimonial_1_role', 'about_testimonial_1_quote',
                'about_testimonial_2_name', 'about_testimonial_2_role', 'about_testimonial_2_quote',
            ];
            foreach ($aboutTextKeys as $key) {
                $meta[$key] = $request->input($key, '');
            }
            $aboutImageKeys = [
                'about_hero_image', 'about_story_1_image', 'about_story_2_image', 'about_story_3_image',
                'about_mission_1_image', 'about_mission_2_image', 'about_testimonial_1_avatar', 'about_testimonial_2_avatar',
            ];
            foreach ($aboutImageKeys as $key) {
                if ($request->hasFile($key)) {
                    $oldPath = $meta[$key] ?? null;
                    if ($oldPath && is_string($oldPath) && Storage::disk('public')->exists($oldPath)) {
                        Storage::disk('public')->delete($oldPath);
                    }
                    $meta[$key] = $request->file($key)->store('uploads/custom-images', 'public');
                }
            }
            $data['meta'] = $meta;
        }

        $page->update($data);

        $translations = $request->input('translations', []);
        if ($page->slug === 'about-us' && ! empty($translations)) {
            foreach (array_keys($translations) as $locale) {
                if ($locale === $defaultLocale) {
                    continue;
                }
                foreach ($aboutImageKeys as $key) {
                    $file = $request->file("translations.{$locale}.{$key}");
                    if ($file && $file->isValid()) {
                        $oldPath = $page->contentTranslations()
                            ->where('language_code', $locale)
                            ->where('field', $key)
                            ->value('value');
                        if ($oldPath && is_string($oldPath) && Storage::disk('public')->exists($oldPath)) {
                            Storage::disk('public')->delete($oldPath);
                        }
                        $path = $file->store('uploads/custom-images', 'public');
                        $translations[$locale][$key] = $path;
                    }
                }
            }
        }
        if (! empty($translations)) {
            $page->saveContentTranslationsFromInput($translations);
        }

        return redirect()->back()->with('success', __('common.page_updated_success'));
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
        
        return response()->json(['error' => __('common.no_file_uploaded')], 400);
    }
}
