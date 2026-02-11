<?php

namespace App\Providers;

use App\Models\Brand;
use App\Models\Menu;
use App\Models\Page;
use App\Services\CartService;
use App\Services\SettingService;
use App\Services\WishlistService;
use Modules\Category\Services\CategoryService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        try {
            $settings = app(SettingService::class)->all();

            if (isset($settings['mail_mailer'])) {
                config([
                    'mail.default' => $settings['mail_mailer'],
                    'mail.mailers.smtp.host' => $settings['mail_host'] ?? config('mail.mailers.smtp.host'),
                    'mail.mailers.smtp.port' => $settings['mail_port'] ?? config('mail.mailers.smtp.port'),
                    'mail.mailers.smtp.encryption' => $settings['mail_encryption'] ?? config('mail.mailers.smtp.encryption'),
                    'mail.mailers.smtp.username' => $settings['mail_username'] ?? config('mail.mailers.smtp.username'),
                    'mail.mailers.smtp.password' => $settings['mail_password'] ?? config('mail.mailers.smtp.password'),
                    'mail.from.address' => $settings['mail_from_address'] ?? config('mail.from.address'),
                    'mail.from.name' => $settings['mail_from_name'] ?? config('mail.from.name'),
                ]);
            }

            // Share general_setting with all views
            View::share('general_setting', (object) $settings->toArray());
        } catch (\Exception $e) {
            // Fallback during migrations or if table missing
        }

        Password::defaults(function () {
            return Password::min(8)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols()
                ->uncompromised();
        });

        View::composer('layouts.admin', function ($view) {
            try {
                $adminPages = Page::select('id', 'title', 'slug')
                    ->whereNotIn('slug', array_merge(config('pages.auth_slugs', []), ['coupons']))
                    ->orderBy('title')
                    ->get();
                $view->with('adminPages', $adminPages);
            } catch (\Exception $e) {
                $view->with('adminPages', collect([]));
            }
        });

        View::composer('layouts.frontend', function ($view) {
            $categoryService = app(CategoryService::class);
            $view->with('headerCategories', $categoryService->getTree());
            $view->with('headerBrands', Brand::where('is_active', true)->orderBy('name')->get());

            // Share cart count with all frontend views
            try {
                $view->with('cartCount', app(CartService::class)->count());
            } catch (\Exception $e) {
                $view->with('cartCount', 0);
            }

            // Share wishlist count with all frontend views
            try {
                $view->with('wishlistCount', app(WishlistService::class)->count());
            } catch (\Exception $e) {
                $view->with('wishlistCount', 0);
            }

            $view->with('headerMenu', Menu::where('position', 'header')
                ->where('is_active', true)
                ->with(['items' => function ($q) {
                    $q->orderBy('order')->with('children');
                }])
                ->first());

            $view->with('footerMenu1', Menu::where('position', 'footer_1')->where('is_active', true)->with(['items' => function ($q) {
                $q->orderBy('order');
            }])->first());
            $view->with('footerMenu2', Menu::where('position', 'footer_2')->where('is_active', true)->with(['items' => function ($q) {
                $q->orderBy('order');
            }])->first());
            $view->with('footerMenu3', Menu::where('position', 'footer_3')->where('is_active', true)->with(['items' => function ($q) {
                $q->orderBy('order');
            }])->first());
            $view->with('footerMenu4', Menu::where('position', 'footer_4')->where('is_active', true)->with(['items' => function ($q) {
                $q->orderBy('order');
            }])->first());
        });
    }
}
