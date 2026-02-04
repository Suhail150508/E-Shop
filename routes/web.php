<?php

use App\Http\Controllers\Admin\CurrencyController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Customer\AddressController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\Customer\OrderController as CustomerOrderController;
use App\Http\Controllers\Customer\ProfileController;
use App\Http\Controllers\Customer\WishlistController as CustomerWishlistController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Frontend\CheckoutController;
use App\Http\Controllers\Frontend\ContactController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\NewsletterController;
use App\Http\Controllers\Frontend\PageController;
use App\Http\Controllers\Frontend\ProductController as FrontendProductController;
use App\Http\Controllers\Frontend\ReviewController as FrontendReviewController;
use App\Http\Controllers\Frontend\WishlistController as FrontendWishlistController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PlaceholderController;
use App\Models\Currency;
use Illuminate\Support\Facades\Route;
use Modules\Category\App\Http\Controllers\Admin\CategoryController;
use Modules\Product\App\Http\Controllers\Admin\ProductController;

// Notification Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-read', [NotificationController::class, 'markRead'])->name('notifications.mark-read');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'read'])->name('notifications.read');
});

// Public Routes
Route::get('placeholder/{size}', [PlaceholderController::class, 'show'])
    ->where('size', '[0-9]+x[0-9]+')
    ->name('placeholder');

Route::get('currency/{code}', function (string $code) {
    $currency = Currency::where('status', true)->where('code', $code)->first();
    if ($currency) {
        session(['currency' => $currency->code]);
    }

    return back();
})->name('currency.switch');

Route::get('lang/{lang}', [LocaleController::class, 'setLocale'])->name('lang.switch');
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/shop', [FrontendProductController::class, 'index'])->name('shop.index');
Route::get('/category/{category:slug}', [FrontendProductController::class, 'category'])->name('shop.category');
Route::get('/product/{product:slug}', [FrontendProductController::class, 'show'])->name('shop.product.show');
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');
Route::post('/contact/submit', [ContactController::class, 'submit'])->name('contact.submit');

// Static Pages
Route::controller(PageController::class)->group(function () {
    Route::get('/about', 'about')->name('pages.about');
    Route::get('/contact', 'contact')->name('pages.contact');
    Route::get('/terms', 'terms')->name('pages.terms');
    Route::get('/privacy', 'privacy')->name('pages.privacy');
    Route::get('/shipping', 'shipping')->name('pages.shipping');
    Route::get('/coupons', 'coupons')->name('pages.coupons');
});

// Cart Routes
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/{product}', [CartController::class, 'store'])->name('cart.store');
Route::put('/cart/{id}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{id}', [CartController::class, 'destroy'])->name('cart.destroy');
Route::delete('/cart', [CartController::class, 'clear'])->name('cart.clear');
Route::post('/checkout', [CartController::class, 'checkout'])->name('cart.checkout')->middleware('auth');

// Checkout Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/checkout/shipping', [CheckoutController::class, 'shipping'])->name('checkout.shipping');
    Route::post('/checkout/shipping', [CheckoutController::class, 'storeShipping'])->name('checkout.shipping.store');
    Route::get('/checkout/payment', [CheckoutController::class, 'payment'])->name('checkout.payment');
    Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/checkout/confirmation/{order}', [CheckoutController::class, 'confirmation'])->name('checkout.confirmation');

    // Coupon Routes
    Route::post('/checkout/coupon/apply', [CheckoutController::class, 'applyCoupon'])->name('checkout.coupon.apply');
    Route::post('/checkout/coupon/remove', [CheckoutController::class, 'removeCoupon'])->name('checkout.coupon.remove');
});

// Wishlist Routes
Route::get('/wishlist', [FrontendWishlistController::class, 'index'])->name('wishlist.index');
Route::post('/wishlist/{product}', [FrontendWishlistController::class, 'toggle'])->name('wishlist.toggle');
Route::post('/wishlist/{product}/move-to-cart', [FrontendWishlistController::class, 'moveToCart'])->name('wishlist.move-to-cart');

// Review Routes
Route::post('/product/{product}/review', [FrontendReviewController::class, 'store'])
    ->name('product.review.store')
    ->middleware('auth');

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login'])->middleware('throttle:6,1');
    Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [RegisterController::class, 'register'])->middleware('throttle:6,1');

    Route::get('forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
});

Route::post('logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Admin Routes
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin'])
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('settings', [SettingController::class, 'update'])->name('settings.update');

        Route::resource('currency', CurrencyController::class);
        Route::resource('language', LanguageController::class);

        // Email Configuration
        Route::get('email-configuration', [\App\Http\Controllers\Admin\EmailConfigurationController::class, 'index'])->name('email-configuration.index');
        Route::put('email-configuration', [\App\Http\Controllers\Admin\EmailConfigurationController::class, 'update'])->name('email-configuration.update');
        Route::get('email-configuration/templates', [\App\Http\Controllers\Admin\EmailConfigurationController::class, 'templates'])->name('email-configuration.templates');
        Route::get('email-configuration/templates/{id}/edit', [\App\Http\Controllers\Admin\EmailConfigurationController::class, 'editTemplate'])->name('email-configuration.templates.edit');
        Route::put('email-configuration/templates/{id}', [\App\Http\Controllers\Admin\EmailConfigurationController::class, 'updateTemplate'])->name('email-configuration.templates.update');

        // Payment Methods
        Route::get('payment-methods', [\App\Http\Controllers\Admin\PaymentMethodController::class, 'index'])->name('payment-methods.index');
        Route::put('payment-methods/{gateway}', [\App\Http\Controllers\Admin\PaymentMethodController::class, 'update'])->name('payment-methods.update');

        Route::get('categories/{category}/subcategories', [CategoryController::class, 'getSubcategories'])->name('categories.subcategories');
        Route::resource('categories', CategoryController::class)->except(['show']);
        Route::resource('subcategories', \Modules\Category\App\Http\Controllers\Admin\SubCategoryController::class)->except(['show']);
        Route::post('brands/bulk-delete', [\App\Http\Controllers\Admin\BrandController::class, 'bulkDelete'])->name('brands.bulk-delete');
        Route::resource('brands', \App\Http\Controllers\Admin\BrandController::class)->except(['show']);
        Route::resource('coupons', \App\Http\Controllers\Admin\CouponController::class)->except(['show']);
        Route::resource('products', ProductController::class)->except(['show']);
        Route::resource('reviews', AdminReviewController::class)->only(['index', 'update', 'destroy']);
        Route::resource('orders', AdminOrderController::class)->only(['index', 'show', 'update']);
        Route::resource('coupons', \App\Http\Controllers\Admin\CouponController::class);
        Route::get('orders/{order}/invoice', [AdminOrderController::class, 'invoice'])->name('orders.invoice');
        
        // Refund Routes
        Route::resource('refund-reasons', \App\Http\Controllers\Admin\RefundReasonController::class);
        Route::get('refund-requests', [\App\Http\Controllers\Admin\RefundController::class, 'index'])->name('refund-requests.index');
        Route::get('refund-requests/{refund}', [\App\Http\Controllers\Admin\RefundController::class, 'show'])->name('refund-requests.show');
        Route::put('refund-requests/{refund}', [\App\Http\Controllers\Admin\RefundController::class, 'update'])->name('refund-requests.update');

        // Support Ticket Routes
        Route::resource('support-tickets', \App\Http\Controllers\Admin\SupportTicketController::class)->only(['index', 'show', 'update']);
        Route::post('support-tickets/{ticket}/reply', [\App\Http\Controllers\Admin\SupportTicketController::class, 'reply'])->name('support-tickets.reply');
        Route::resource('support-departments', \App\Http\Controllers\Admin\SupportDepartmentController::class);
        Route::resource('staff', \App\Http\Controllers\Admin\StaffController::class);
        Route::resource('customers', \App\Http\Controllers\Admin\CustomerController::class);
        
        // Contact Messages Routes
        Route::get('contact', [\App\Http\Controllers\Admin\ContactController::class, 'index'])->name('contact.index');
        Route::delete('contact/{id}', [\App\Http\Controllers\Admin\ContactController::class, 'destroy'])->name('contact.destroy');
        Route::post('contact/{id}/reply', [\App\Http\Controllers\Admin\ContactController::class, 'reply'])->name('contact.reply');

        // Newsletter Routes
        Route::resource('newsletter', \App\Http\Controllers\Admin\NewsletterController::class)->only(['index', 'destroy']);

        Route::post('pages/upload-image', [\App\Http\Controllers\Admin\PageController::class, 'uploadImage'])->name('pages.upload-image');
        Route::resource('pages', \App\Http\Controllers\Admin\PageController::class)->only(['index', 'edit', 'update']);

        // Menu Builder Routes
        Route::resource('menus', \App\Http\Controllers\Admin\MenuController::class);
        Route::get('menus/{menu}/builder', [\App\Http\Controllers\Admin\MenuController::class, 'builder'])->name('menus.builder');
        Route::post('menus/{menu}/items', [\App\Http\Controllers\Admin\MenuController::class, 'addItem'])->name('menus.item.add');
        Route::put('menus/items/{item}', [\App\Http\Controllers\Admin\MenuController::class, 'updateItem'])->name('menus.item.update');
        Route::delete('menus/items/{item}', [\App\Http\Controllers\Admin\MenuController::class, 'deleteItem'])->name('menus.item.delete');
        Route::post('menus/sort', [\App\Http\Controllers\Admin\MenuController::class, 'sortItems'])->name('menus.sort');

        // Unit Routes
        Route::resource('units', \App\Http\Controllers\Admin\UnitController::class);

        // Color Routes
        Route::resource('colors', \App\Http\Controllers\Admin\ColorController::class);

        // Website Setup Routes
        Route::get('website-setup', [\App\Http\Controllers\Admin\WebsiteSetupController::class, 'index'])->name('website-setup.index');
        Route::post('website-setup', [\App\Http\Controllers\Admin\WebsiteSetupController::class, 'update'])->name('website-setup.update');

        // Admin Notification Routes
        Route::get('/notifications/unread', [\App\Http\Controllers\Admin\NotificationController::class, 'getUnread'])->name('notifications.unread');
        Route::post('/notifications/{id}/read', [\App\Http\Controllers\Admin\NotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::post('/notifications/read-all', [\App\Http\Controllers\Admin\NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');

    });

// Customer Routes
Route::middleware(['auth', 'role:customer'])->group(function () {
    Route::get('/account', [CustomerDashboardController::class, 'index'])->name('customer.dashboard');

    Route::get('/account/profile', [ProfileController::class, 'edit'])->name('customer.profile.edit');
    Route::put('/account/profile', [ProfileController::class, 'update'])->name('customer.profile.update');

    Route::get('/account/password', [ProfileController::class, 'editPassword'])->name('customer.password.edit');
    Route::put('/account/password', [ProfileController::class, 'updatePassword'])->name('customer.password.update');

    Route::get('/account/addresses', [AddressController::class, 'index'])->name('customer.addresses.index');
    Route::get('/account/addresses/create', [AddressController::class, 'create'])->name('customer.addresses.create');
    Route::post('/account/addresses', [AddressController::class, 'store'])->name('customer.addresses.store');
    Route::get('/account/addresses/{address}/edit', [AddressController::class, 'edit'])->name('customer.addresses.edit');
    Route::put('/account/addresses/{address}', [AddressController::class, 'update'])->name('customer.addresses.update');
    Route::delete('/account/addresses/{address}', [AddressController::class, 'destroy'])->name('customer.addresses.destroy');
    Route::post('/account/addresses/{address}/default', [AddressController::class, 'setDefault'])->name('customer.addresses.default');

    Route::get('/orders', [CustomerOrderController::class, 'index'])->name('customer.orders.index');
    Route::get('/orders/{order}', [CustomerOrderController::class, 'show'])->name('customer.orders.show');
    Route::get('/orders/{order}/invoice', [CustomerOrderController::class, 'invoice'])->name('customer.orders.invoice');
    Route::post('/orders/{order}/refund', [\App\Http\Controllers\Customer\RefundController::class, 'store'])->name('customer.orders.refund.store');

    Route::get('/account/wishlist', [CustomerWishlistController::class, 'index'])->name('customer.wishlist.index');

    // Support Ticket Routes
    Route::resource('support-tickets', \App\Http\Controllers\Frontend\SupportTicketController::class)
        ->only(['index', 'store', 'show'])
        ->names('customer.support-tickets');
    Route::post('support-tickets/{ticket}/reply', [\App\Http\Controllers\Frontend\SupportTicketController::class, 'reply'])
        ->name('customer.support-tickets.reply');
});

// Staff Routes
Route::prefix('staff')
    ->name('staff.')
    ->middleware(['auth', 'role:staff'])
    ->group(function () {
        Route::get('/', [App\Http\Controllers\Staff\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/orders', [App\Http\Controllers\Staff\OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [App\Http\Controllers\Staff\OrderController::class, 'show'])->name('orders.show');
        Route::put('/orders/{order}', [App\Http\Controllers\Staff\OrderController::class, 'updateStatus'])->name('orders.update');

        Route::get('/profile', [App\Http\Controllers\Staff\ProfileController::class, 'edit'])->name('profile');
        Route::put('/profile', [App\Http\Controllers\Staff\ProfileController::class, 'update'])->name('profile.update');
        Route::put('/password', [App\Http\Controllers\Staff\ProfileController::class, 'updatePassword'])->name('password.update');
    });
