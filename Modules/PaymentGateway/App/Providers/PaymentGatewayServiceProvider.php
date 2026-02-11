<?php

namespace Modules\PaymentGateway\App\Providers;

use App\Services\OrderService;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Modules\PaymentGateway\App\Services\PaymentManager;
use Modules\PaymentGateway\App\Services\Payments\BankPaymentService;
use Modules\PaymentGateway\App\Services\Payments\CodPaymentService;
use Modules\PaymentGateway\App\Services\Payments\PaypalPaymentService;
use Modules\PaymentGateway\App\Services\Payments\PaystackPaymentService;
use Modules\PaymentGateway\App\Services\Payments\RazorpayPaymentService;
use Modules\PaymentGateway\App\Services\Payments\StripePaymentService;
use Modules\PaymentGateway\App\Services\Payments\WalletPaymentService;

class PaymentGatewayServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PaymentManager::class, function ($app) {
            return new PaymentManager(
                $app->make(OrderService::class),
                $app->make(CodPaymentService::class),
                $app->make(BankPaymentService::class),
                $app->make(StripePaymentService::class),
                $app->make(WalletPaymentService::class),
                $app->make(PaypalPaymentService::class),
                $app->make(RazorpayPaymentService::class),
                $app->make(PaystackPaymentService::class)
            );
        });
    }

    public function boot(): void
    {
        $this->registerRoutes();
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'paymentgateway');
        $this->loadMigrationsFrom(__DIR__.'/../../Database/Migrations');
        $this->loadTranslationsFrom(__DIR__.'/../../resources/lang', 'paymentgateway');
    }

    protected function registerRoutes(): void
    {
        Route::middleware('web')
            ->group(__DIR__.'/../../routes/web.php');

        Route::middleware('api')
            ->prefix('api/paymentgateway')
            ->group(__DIR__.'/../../routes/api.php');
    }
}
