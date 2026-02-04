<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingService extends BaseService
{
    public function get(string $key, $default = null)
    {
        return Cache::rememberForever("setting_{$key}", function () use ($key, $default) {
            $setting = Setting::where('key', $key)->first();

            return $setting ? $setting->value : $default;
        });
    }

    public function set(string $key, $value = null)
    {
        $setting = Setting::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        Cache::forget("setting_{$key}");
        Cache::forget('settings_all');

        return $setting;
    }

    public function all()
    {
        return Cache::rememberForever('settings_all', function () {
            return Setting::all()->pluck('value', 'key');
        });
    }
}
