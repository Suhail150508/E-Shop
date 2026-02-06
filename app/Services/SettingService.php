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
        if (is_array($value) || is_object($value)) {
            $value = json_encode($value);
        }
        if ($value !== null && ! is_string($value)) {
            $value = (string) $value;
        }

        $setting = Setting::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        $this->clearCache($key);

        return $setting;
    }

    /**
     * Clear cached settings so frontend and admin see fresh data.
     */
    public function clearCache(?string $key = null): void
    {
        if ($key !== null) {
            Cache::forget("setting_{$key}");
        }
        Cache::forget('settings_all');
    }

    public function all()
    {
        return Cache::rememberForever('settings_all', function () {
            return Setting::all()->pluck('value', 'key');
        });
    }
}
