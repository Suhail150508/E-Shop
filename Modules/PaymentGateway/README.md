Payment Gateway Module
======================

Overview
--------
This module provides a centralized `PaymentManager` and multiple gateway implementations (Stripe, PayPal, Razorpay, Paystack, Bank, COD, Wallet). Gateways are implemented as services and can be enabled/disabled in the application settings.

Important
---------
This module requires external composer packages (not bundled). Do NOT include vendor/ in your upload. Install the required SDKs in your project root via composer.

Recommended composer packages (install in project root)
------------------------------------------------------
- Stripe: `composer require stripe/stripe-php`
- PayPal Checkout: `composer require paypal/paypal-checkout-sdk` (or the SDK you use)
- Razorpay: `composer require razorpay/razorpay`
- Paystack: `composer require yabacon/paystack` (or the Paystack PHP package you prefer)
- Pusher (for broadcasting if needed): `composer require pusher/pusher-php-server`
- Laravel Websockets (optional self-hosted): `composer require beyondcode/laravel-websockets`

Installation
------------
1. Copy `Modules/PaymentGateway` to `Modules/`.
2. Install required composer packages (project root), then run:

```powershell
composer dump-autoload;
php artisan migrate;
```

3. Configure gateway credentials and secrets in your application settings or `.env` (see examples below). Ensure webhook endpoints are registered in each provider dashboard and that you configure the webhook secret where applicable.

Example environment / settings
------------------------------
- Stripe (in settings or .env)
  - stripe_mode: test|live
  - stripe_test_secret_key
  - stripe_live_secret_key
  - stripe_webhook_secret (configure Stripe webhook in dashboard)

- PayPal
  - paypal_client_id
  - paypal_client_secret

Webhooks & Security
-------------------
- Configure webhook secrets for providers that support them and enable signature verification in settings.
- Protect webhook endpoints with rate limiting and ensure they are excluded from CSRF middleware if needed.

Notes
-----
- This module only contains gateway integration code. Buyers must install the actual SDKs via composer.
- Do not leave API keys in the package; instruct buyers to input keys via settings or `.env`.
