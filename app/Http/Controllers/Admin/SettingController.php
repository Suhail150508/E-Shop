<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSettingRequest;
use App\Services\SettingService;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    protected $settingService;

    public function __construct(SettingService $settingService)
    {
        $this->settingService = $settingService;
    }

    public function index()
    {
        $settings = $this->settingService->all();

        return view('admin.settings.index', compact('settings'));
    }

    public function update(UpdateSettingRequest $request)
    {
        $validated = $request->validated();

        // Handle App Logo
        if ($request->hasFile('app_logo')) {
            $oldLogo = $this->settingService->get('app_logo');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }

            $path = $request->file('app_logo')->store('uploads/settings', 'public');
            $this->settingService->set('app_logo', $path);
        }
        unset($validated['app_logo']);

        // Handle App Favicon
        if ($request->hasFile('app_favicon')) {
            $oldFavicon = $this->settingService->get('app_favicon');
            if ($oldFavicon && Storage::disk('public')->exists($oldFavicon)) {
                Storage::disk('public')->delete($oldFavicon);
            }

            $path = $request->file('app_favicon')->store('uploads/settings', 'public');
            $this->settingService->set('app_favicon', $path);
        }
        unset($validated['app_favicon']);

        // Handle Payment Method Image
        if ($request->hasFile('payment_method_image')) {
            $oldImage = $this->settingService->get('payment_method_image');
            if ($oldImage && Storage::disk('public')->exists($oldImage)) {
                Storage::disk('public')->delete($oldImage);
            }

            $path = $request->file('payment_method_image')->store('uploads/settings', 'public');
            $this->settingService->set('payment_method_image', $path);
        }
        unset($validated['payment_method_image']);

        foreach ($validated as $key => $value) {
            $this->settingService->set($key, $value);
        }

        return redirect()->route('admin.settings.index')
            ->with('success', __('common.settings_updated_success'));
    }
}
