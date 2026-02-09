<?php

use App\Models\Currency;
use App\Services\SettingService;
use Illuminate\Support\Facades\Storage;

if (! function_exists('getImageOrPlaceholder')) {
    /**
     * Return the image URL or a placeholder if the image does not exist
     */
    function getImageOrPlaceholder($path = null, $size = '300x300')
    {
        // Ensure size format is valid (WxH)
        if (! preg_match('/^\d+x\d+$/', $size)) {
            $size = '300x300';
        }

        $placeholder = route('placeholder', ['size' => $size]) . '?v=2';

        if ($path === null || $path === '' || (is_string($path) && trim($path) === '')) {
            return $placeholder;
        }

        // Handle Data URLs
        if (is_string($path) && strpos($path, 'data:image') === 0) {
            return $path;
        }

        // Handle External URLs
        if (is_string($path) && preg_match('#^https?://#', $path)) {
            // Check if it's a local storage URL
            $storagePrefix = rtrim(asset('storage'), '/').'/';
            if (strpos($path, $storagePrefix) === 0) {
                $rel = ltrim(substr($path, strlen($storagePrefix)), '/');
                if (Storage::disk('public')->exists($rel)) {
                    return asset('storage/' . $rel);
                }
                return $placeholder;
            }
            
            // Check if it's a local public URL
            $urlPath = parse_url($path, PHP_URL_PATH) ?? '';
            $relAbs = ltrim($urlPath, '/');
            if ($relAbs !== '') {
                // If path starts with storage/
                if (strpos($relAbs, 'storage/') === 0) {
                    $relStorage = substr($relAbs, strlen('storage/'));
                    if (Storage::disk('public')->exists($relStorage)) {
                        return asset('storage/' . $relStorage);
                    }
                }
                // If file exists in public/
                if (file_exists(public_path($relAbs))) {
                    $url = asset($relAbs);
                    $ts = @filemtime(public_path($relAbs));
                    return $ts ? ($url.'?v='.$ts) : $url;
                }
            }

            // Return original external URL (we can't easily verify existence without overhead)
            // But for local dev (localhost), we might want to be stricter? 
            // For now, return as is if not local storage/public.
            return $path;
        }

        // Handle Array (e.g. from some media managers)
        if (is_array($path)) {
            if (isset($path['url']) && is_string($path['url'])) {
                $path = $path['url'];
            } elseif (isset($path['src']) && is_string($path['src'])) {
                $path = $path['src'];
            } else {
                foreach ($path as $v) {
                    if (is_string($v) && $v !== '') {
                        $path = $v;
                        break;
                    }
                }
            }
            if (! is_string($path) || $path === '') {
                return $placeholder;
            }
        }

        // Handle Relative Paths
        $rel = ltrim((string) $path, '/');
        
        // Remove storage/ prefix if present to check disk
        $checkRel = $rel;
        if (strpos($rel, 'storage/') === 0) {
            $checkRel = substr($rel, strlen('storage/'));
        }

        if (Storage::disk('public')->exists($checkRel)) {
            return asset('storage/' . $checkRel);
        }

        if (file_exists(public_path($rel))) {
            $url = asset($rel);
            $ts = @filemtime(public_path($rel));
            return $ts ? ($url.'?v='.$ts) : $url;
        }

        return $placeholder;
    }
}

if (! function_exists('module_path')) {
    function module_path($name, $path = '')
    {
        $modulePath = base_path('Modules' . DIRECTORY_SEPARATOR . $name);
        return $path ? $modulePath . DIRECTORY_SEPARATOR . ltrim($path, '/\\') : $modulePath;
    }
}

if (! function_exists('setting')) {
    function setting($key, $default = null)
    {
        return app(SettingService::class)->get($key, $default);
    }
}

if (! function_exists('account_setting')) {
    function account_setting($key, $default = null)
    {
        // Placeholder implementation as getAccount is missing in SettingService
        // return app(SettingService::class)->getAccount($key, $default);
        return $default;
    }
}

if (! function_exists('account_setting_image')) {
    function account_setting_image($key, $default = null)
    {
        // Placeholder implementation as getAccountImage is missing in SettingService
        // return app(SettingService::class)->getAccountImage($key, $default);
        return $default;
    }
}

if (! function_exists('setting_clear_cache')) {
    function setting_clear_cache($key = null)
    {
        // SettingService doesn't have clearCache public method, but it does internal cache clearing on set.
        // We can manually clear cache if needed.
        if ($key) {
            Illuminate\Support\Facades\Cache::forget("setting_{$key}");
        } else {
            Illuminate\Support\Facades\Cache::flush();
        }
    }
}

if (! function_exists('default_currency')) {
    function default_currency(): ?Currency
    {
        static $default;
        if ($default) {
            return $default;
        }
        $default = Currency::where('status', true)->where('is_default', true)->first();
        if (! $default) {
            $default = Currency::where('status', true)->first();
        }

        return $default;
    }
}

if (! function_exists('current_currency')) {
    function current_currency(): ?Currency
    {
        // Remove static cache to handle session changes dynamically
        $code = session('currency');
        $current = null;
        if ($code) {
            $current = Currency::where('status', true)->where('code', $code)->first();
        }
        if (! $current) {
            $current = default_currency();
        }

        return $current;
    }
}

if (! function_exists('convert_price')) {
    function convert_price(float $amount, ?Currency $currency = null): float
    {
        $currency = $currency ?: current_currency();
        if (! $currency) {
            return round($amount, 2);
        }

        return round($amount * (float) $currency->rate, 2);
    }
}

if (! function_exists('format_price')) {
    function format_price(float $amount, ?Currency $currency = null): string
    {
        $currency = $currency ?: current_currency();
        if (! $currency) {
            return number_format($amount, 2);
        }
        $value = convert_price($amount, $currency);

        return $currency->symbol.number_format($value, 2);
    }
}
