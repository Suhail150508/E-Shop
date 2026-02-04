<?php

namespace Modules\LiveChat\App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class LiveChatServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'livechat');
        $this->loadMigrationsFrom(__DIR__.'/../../Database/Migrations');

        $this->registerRoutes();
    }

    protected function registerRoutes(): void
    {
        Route::middleware('web')
            ->group(__DIR__.'/../../routes/web.php');
    }
}
