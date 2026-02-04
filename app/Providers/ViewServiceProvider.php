<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Services\CartService;
use App\Services\SettingService;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Share data with the frontend layout
        View::composer('layouts.frontend', function ($view) {
            // Cart Count
            $cartService = app(CartService::class);
            $view->with('cartCount', $cartService->count());

            // Global Settings
            // Only load settings if not already passed (to avoid double query if passed from controller)
            if (!isset($view->settings)) {
                $settingService = app(SettingService::class);
                $view->with('settings', $settingService->all());
            }
        });
    }
}
