<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSettingRequest;
use App\Services\SettingService;

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
            $file = $request->file('app_logo');
            $filename = 'logo-' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/settings'), $filename);
            $this->settingService->set('app_logo', 'uploads/settings/' . $filename);
        }
        unset($validated['app_logo']);

        // Handle App Favicon
        if ($request->hasFile('app_favicon')) {
            $file = $request->file('app_favicon');
            $filename = 'favicon-' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/settings'), $filename);
            $this->settingService->set('app_favicon', 'uploads/settings/' . $filename);
        }
        unset($validated['app_favicon']);

        foreach ($validated as $key => $value) {
            $this->settingService->set($key, $value);
        }

        return redirect()->route('admin.settings.index')
            ->with('success', __('Settings updated successfully.'));
    }
}
