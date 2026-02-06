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
        $maxFileKb = 2048; // 2MB per file
        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

        $rules = [
            'home_hero_gallery_files' => 'nullable',
            'home_hero_gallery_files.*' => 'nullable',
            'home_hero_gallery_files.*.*' => 'nullable|image|mimes:jpeg,png,gif,webp|max:' . $maxFileKb,
        ];

        // Dynamic rules for other file inputs (logo, favicon, etc.)
        foreach (array_keys($request->file() ?? []) as $fileKey) {
            if ($fileKey !== 'home_hero_gallery_files') {
                $rules[$fileKey] = 'nullable|image|mimes:jpeg,png,gif,webp|max:' . $maxFileKb;
            }
        }

        $validated = $request->validate($rules, [
            'max' => __('Each image must be no larger than :max KB.'),
            'image' => __('Each file must be an image (jpeg, png, gif or webp).'),
        ]);

        $inputs = $request->except(['_token', 'home_hero_gallery_files']);

        // Process Hero Gallery Images
        if ($request->has('home_hero_gallery') || $request->hasFile('home_hero_gallery_files')) {
            $galleryInput = $request->input('home_hero_gallery', []);
            $finalGallery = [];

            // Explicitly iterate 3 columns x 3 rows to match the fixed UI structure
            for ($p = 0; $p < 3; $p++) {
                $colItems = [];
                for ($i = 0; $i < 3; $i++) {
                    // Get existing data from input or defaults
                    $current = $galleryInput[$p][$i] ?? [];
                    
                    $itemData = [
                        'image' => $current['image'] ?? null,
                        'name' => $current['name'] ?? '',
                        'badge' => $current['badge'] ?? '',
                    ];

                    // Check for new file upload at this specific position
                    // Note: accessing nested files via array keys from the request object
                    $files = $request->file('home_hero_gallery_files');
                    if (isset($files[$p][$i]) && $files[$p][$i] instanceof \Illuminate\Http\UploadedFile) {
                        $file = $files[$p][$i];
                        if ($file->isValid()) {
                            $extension = $file->getClientOriginalExtension();
                            $imageName = "hero-{$p}-{$i}-" . time() . '-' . rand(1000, 9999) . '.' . $extension;
                            $destinationPath = public_path('uploads/custom-images');
                            
                            if (!file_exists($destinationPath)) {
                                mkdir($destinationPath, 0755, true);
                            }
                            
                            if ($file->move($destinationPath, $imageName)) {
                                $itemData['image'] = 'uploads/custom-images/' . $imageName;
                            }
                        }
                    }
                    
                    $colItems[] = $itemData;
                }
                $finalGallery[] = $colItems;
            }

            $this->settingService->set('home_hero_gallery', json_encode($finalGallery));
            unset($inputs['home_hero_gallery']);
        }

        // Handle other single file uploads
        foreach ($request->files ?? [] as $key => $file) {
            if ($key === 'home_hero_gallery_files') {
                continue;
            }
            if (is_array($file)) {
                continue;
            }
            if (! $file->isValid()) {
                continue;
            }
            $extension = $file->getClientOriginalExtension();
            $imageName = Str::slug($key) . '-' . date('Y-m-d-h-i-s') . '-' . rand(999, 9999) . '.' . $extension;
            $destinationPath = public_path('uploads/custom-images');
            if (! is_dir($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            if ($file->move($destinationPath, $imageName)) {
                $inputs[$key] = 'uploads/custom-images/' . $imageName;
            }
        }

        foreach ($inputs as $key => $value) {
            if (is_array($value)) {
                $value = json_encode($value);
            }
            $this->settingService->set($key, $value);
        }

        // Clear all settings cache so frontend home page shows updated content immediately
        $this->settingService->clearCache();

        return redirect()->back()->with('success', __('Website settings updated successfully.'));
    }

    /**
     * Normalize gallery to sequential array [ [item, item, item], [item, item, item], [item, item, item] ]
     * so JSON structure is consistent and frontend can iterate reliably.
     */
    private function normalizeGallery(array $gallery): array
    {
        $out = [];
        foreach (array_values($gallery) as $position) {
            if (! is_array($position)) {
                continue;
            }
            $row = [];
            foreach (array_values($position) as $item) {
                $row[] = is_array($item) ? array_merge(
                    ['image' => $item['image'] ?? '', 'name' => $item['name'] ?? '', 'badge' => $item['badge'] ?? ''],
                    $item
                ) : ['image' => '', 'name' => '', 'badge' => ''];
            }
            $out[] = $row;
        }
        return $out;
    }
}
