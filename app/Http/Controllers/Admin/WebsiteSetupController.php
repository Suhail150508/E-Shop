<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SettingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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

        $allowedKeys = [
            'home_hero_title', 'home_hero_subtitle',
            'home_category_title', 'home_category_subtitle', 'home_category_badge',
            'home_flash_title', 'home_flash_badge',
            'home_promo_title', 'home_promo_subtitle', 'home_promo_badge',
            'home_promo_btn1_text', 'home_promo_btn1_link', 'home_promo_btn2_text', 'home_promo_btn2_link',
            'home_banner_title', 'home_banner_badge', 'home_banner_text',
            'home_banner_btn_text', 'home_banner_btn_link',
            'home_banner_rating', 'home_banner_review_count',
            'home_banner_testimonial_1_name', 'home_banner_testimonial_2_name',
            'home_featured_title', 'home_featured_badge',
            'home_latest_title', 'home_latest_badge',
            'shop_page_title', 'shop_breadcrumb', 'shop_header_title', 'shop_header_subtitle',
            'category_default_subtitle',
            'product_related_title',
            'auth_login_title', 'auth_login_subtitle',
            'auth_register_title', 'auth_register_subtitle',
            'auth_forgot_title', 'auth_forgot_subtitle',
            'auth_reset_title', 'auth_reset_subtitle',
            'cart_breadcrumb', 'cart_title', 'cart_subtitle',
            'checkout_shipping_title', 'checkout_payment_title',
            'home_promo_image', 'home_banner_image',
            'auth_login_image', 'auth_register_image', 'auth_forgot_image', 'auth_reset_image'
        ];

        $inputs = $request->only($allowedKeys);

        // Process Hero Gallery Images
        if ($request->has('home_hero_gallery') || $request->hasFile('home_hero_gallery_files')) {
            $galleryInput = $request->input('home_hero_gallery', []);
            $finalGallery = [];

            // Get OLD gallery for deletion handling
            $oldGalleryJson = $this->settingService->get('home_hero_gallery');
            $oldGallery = json_decode($oldGalleryJson, true) ?? [];

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
                            // Delete old image
                            $oldImage = $oldGallery[$p][$i]['image'] ?? null;
                            if ($oldImage && Storage::disk('public')->exists($oldImage)) {
                                Storage::disk('public')->delete($oldImage);
                            }
                            
                            $path = $file->store('uploads/custom-images', 'public');
                            $itemData['image'] = $path;
                        }
                    }
                    
                    $colItems[] = $itemData;
                }
                $finalGallery[] = $colItems;
            }

            $this->settingService->set('home_hero_gallery', json_encode($finalGallery));
            unset($inputs['home_hero_gallery']);
        }

        foreach ($inputs as $key => $value) {
            if ($request->hasFile($key)) {
                // Delete old image only if it is in storage (do not delete public placeholder paths)
                $oldImage = $this->settingService->get($key);
                if ($oldImage && is_string($oldImage) && Storage::disk('public')->exists($oldImage)) {
                    Storage::disk('public')->delete($oldImage);
                }

                $path = $request->file($key)->store('uploads/custom-images', 'public');
                $inputs[$key] = $path;
            }
        }

        // Keys that are text/content: do not overwrite with empty so one section edit doesn't blank others
        $contentKeys = [
            'home_hero_title', 'home_hero_subtitle', 'home_category_title', 'home_category_subtitle', 'home_category_badge',
            'home_flash_title', 'home_flash_badge', 'home_promo_title', 'home_promo_subtitle', 'home_promo_badge',
            'home_promo_btn1_text', 'home_promo_btn1_link', 'home_promo_btn2_text', 'home_promo_btn2_link',
            'home_banner_title', 'home_banner_badge', 'home_banner_text', 'home_banner_btn_text', 'home_banner_btn_link',
            'home_banner_rating', 'home_banner_review_count', 'home_banner_testimonial_1_name', 'home_banner_testimonial_2_name',
            'home_featured_title', 'home_featured_badge',
            'home_latest_title', 'home_latest_badge',
            'shop_page_title', 'shop_breadcrumb', 'shop_header_title', 'shop_header_subtitle', 'category_default_subtitle',
            'product_related_title', 'auth_login_title', 'auth_login_subtitle', 'auth_register_title', 'auth_register_subtitle',
            'auth_forgot_title', 'auth_forgot_subtitle', 'auth_reset_title', 'auth_reset_subtitle',
            'cart_breadcrumb', 'cart_title', 'cart_subtitle', 'checkout_shipping_title', 'checkout_payment_title',
        ];

        foreach ($inputs as $key => $value) {
            if (in_array($key, $contentKeys) && $value !== null && (string) $value === '') {
                continue; // preserve existing value when form sends empty for this key
            }
            if (is_array($value)) {
                $value = json_encode($value);
            }
            $this->settingService->set($key, $value);
        }

        // Clear settings cache and home page product/section cache so frontend updates immediately
        $this->settingService->clearCache();
        Cache::forget('home_products');

        return redirect()->back()->with('success', __('common.website_settings_updated_success'));
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
