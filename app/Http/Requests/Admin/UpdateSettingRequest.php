<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'app_name' => 'nullable|string|max:255',
            'app_currency' => 'nullable|string|max:10',
            'contact_email' => 'nullable|email|max:255',
            'google_maps_enabled' => 'nullable|boolean',
            'google_maps_api_key' => 'nullable|string|max:255',

            // General Payment Settings
            'payment_cod_enabled' => 'nullable|boolean',
            'payment_wallet_enabled' => 'nullable|boolean',
            'payment_bank_enabled' => 'nullable|boolean',

            // Stripe Payment Settings
            'payment_stripe_enabled' => 'nullable|boolean',
            'stripe_mode' => 'nullable|in:test,live',
            'stripe_test_key' => 'nullable|string|max:255',
            'stripe_test_secret' => 'nullable|string|max:255',
            'stripe_live_key' => 'nullable|string|max:255',
            'stripe_live_secret' => 'nullable|string|max:255',

            // PayPal Payment Settings
            'payment_paypal_enabled' => 'nullable|boolean',
            'paypal_mode' => 'nullable|in:sandbox,live',
            'paypal_sandbox_client_id' => 'nullable|string|max:255',
            'paypal_sandbox_secret' => 'nullable|string|max:255',
            'paypal_live_client_id' => 'nullable|string|max:255',
            'paypal_live_secret' => 'nullable|string|max:255',
            // Paystack Payment Settings
            'payment_paystack_enabled' => 'nullable|boolean',
            'paystack_public_key' => 'nullable|string|max:255',
            'paystack_secret_key' => 'nullable|string|max:255',

            // Razorpay Payment Settings
            'payment_razorpay_enabled' => 'nullable|boolean',
            'razorpay_key' => 'nullable|string|max:255',
            'razorpay_secret' => 'nullable|string|max:255',
            'razorpay_webhook_secret' => 'nullable|string|max:255',
        ];
    }
}
