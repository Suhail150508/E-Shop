<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\ViewServiceProvider::class,
    App\Providers\BroadcastServiceProvider::class,
    Modules\LiveChat\App\Providers\LiveChatServiceProvider::class,
    Modules\PaymentGateway\App\Providers\PaymentGatewayServiceProvider::class,
    Modules\Product\App\Providers\ProductServiceProvider::class,
    Modules\Category\App\Providers\CategoryServiceProvider::class,
];
