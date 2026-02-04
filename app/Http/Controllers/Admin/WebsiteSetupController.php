<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SettingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class WebsiteSetupController extends Controller
{
    protected $settingService;

    public function __construct(SettingService $settingService)
    {
        $this->settingService = $settingService;
    }

    public function index()
    {
        $settings = $this->settingService->all();

        // Parse JSON settings if needed
        $home_hero_gallery = json_decode($settings['home_hero_gallery'] ?? '[]', true);

        return view('admin.website-setup.index', compact('settings', 'home_hero_gallery'));
    }

    public function update(Request $request)
    {
        // Handle file uploads first
        $inputs = $request->except(['_token', 'home_hero_gallery_files']);

        // Process Hero Gallery Images
        if ($request->has('home_hero_gallery')) {
            $gallery = $request->input('home_hero_gallery');

            // Handle file uploads for gallery
            if ($request->hasFile('home_hero_gallery_files')) {
                foreach ($request->file('home_hero_gallery_files') as $posIndex => $images) {
                    foreach ($images as $imgIndex => $file) {
                        $extension = $file->getClientOriginalExtension();
                        $imageName = 'hero-' . $posIndex . '-' . $imgIndex . '-' . date('Y-m-d-h-i-s') . '-' . rand(999, 9999) . '.' . $extension;
                        $destinationPath = public_path('uploads/custom-images');
                        $file->move($destinationPath, $imageName);
                        $gallery[$posIndex][$imgIndex]['image'] = 'uploads/custom-images/' . $imageName;
                    }
                }
            }

            // Encode and save
            $this->settingService->set('home_hero_gallery', json_encode($gallery));
            unset($inputs['home_hero_gallery']);
        }

        // Handle other single file uploads
        foreach ($request->files as $key => $file) {
            if ($key !== 'home_hero_gallery_files') {
                $extension = $file->getClientOriginalExtension();
                $imageName = Str::slug($key) . '-' . date('Y-m-d-h-i-s') . '-' . rand(999, 9999) . '.' . $extension;
                $destinationPath = public_path('uploads/custom-images');
                $file->move($destinationPath, $imageName);
                $inputs[$key] = 'uploads/custom-images/' . $imageName;
            }
        }

        // Save other inputs
        foreach ($inputs as $key => $value) {
            $this->settingService->set($key, $value);
        }

        return redirect()->back()->with('success', __('Website settings updated successfully.'));
    }
}
