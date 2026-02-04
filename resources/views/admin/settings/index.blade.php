@extends('layouts.admin')

@section('page_title', __('General Settings'))

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form action="{{ route('admin.settings.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="app_name" class="form-label">{{ __('App Name') }}</label>
                <input type="text" class="form-control @error('app_name') is-invalid @enderror" id="app_name" name="app_name" value="{{ old('app_name', $settings['app_name'] ?? config('app.name')) }}">
                @error('app_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="app_currency" class="form-label">{{ __('Currency Symbol') }}</label>
                <input type="text" class="form-control @error('app_currency') is-invalid @enderror" id="app_currency" name="app_currency" value="{{ old('app_currency', $settings['app_currency'] ?? '$') }}">
                @error('app_currency')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="contact_email" class="form-label">{{ __('Contact Email') }}</label>
                <input type="email" class="form-control @error('contact_email') is-invalid @enderror" id="contact_email" name="contact_email" value="{{ old('contact_email', $settings['contact_email'] ?? '') }}">
                @error('contact_email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <h2 class="h6 mb-3">{{ __('Payment Gateway Settings') }}</h2>

            <!-- Cash On Delivery Settings -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0 text-dark"><i class="fas fa-money-bill-wave me-2"></i>{{ __('Cash On Delivery') }}</h6>
                </div>
                <div class="card-body">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="payment_cod_enabled" name="payment_cod_enabled" value="1" {{ old('payment_cod_enabled', $settings['payment_cod_enabled'] ?? '1') ? 'checked' : '' }}>
                        <label class="form-check-label" for="payment_cod_enabled">{{ __('Enable Cash On Delivery') }}</label>
                    </div>
                </div>
            </div>

            <!-- Wallet Settings -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0 text-dark"><i class="fas fa-wallet me-2"></i>{{ __('Wallet') }}</h6>
                </div>
                <div class="card-body">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="payment_wallet_enabled" name="payment_wallet_enabled" value="1" {{ old('payment_wallet_enabled', $settings['payment_wallet_enabled'] ?? '1') ? 'checked' : '' }}>
                        <label class="form-check-label" for="payment_wallet_enabled">{{ __('Enable Wallet Payment') }}</label>
                    </div>
                </div>
            </div>

            <!-- Bank Transfer Settings -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0 text-dark"><i class="fas fa-university me-2"></i>{{ __('Bank Transfer') }}</h6>
                </div>
                <div class="card-body">
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" role="switch" id="payment_bank_enabled" name="payment_bank_enabled" value="1" {{ old('payment_bank_enabled', $settings['payment_bank_enabled'] ?? '') ? 'checked' : '' }}>
                        <label class="form-check-label" for="payment_bank_enabled">{{ __('Enable Bank Transfer') }}</label>
                    </div>
                </div>
            </div>

            <!-- Paystack Settings -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0 text-dark"><i class="fas fa-money-bill-wave me-2"></i>Paystack</h6>
                </div>
                <div class="card-body">
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" role="switch" id="payment_paystack_enabled" name="payment_paystack_enabled" value="1" {{ old('payment_paystack_enabled', $settings['payment_paystack_enabled'] ?? '') ? 'checked' : '' }}>
                        <label class="form-check-label" for="payment_paystack_enabled">{{ __('Enable Paystack Payment') }}</label>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Public Key') }}</label>
                            <input type="text" class="form-control" name="paystack_public_key" value="{{ old('paystack_public_key', $settings['paystack_public_key'] ?? '') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Secret Key') }}</label>
                            <input type="text" class="form-control" name="paystack_secret_key" value="{{ old('paystack_secret_key', $settings['paystack_secret_key'] ?? '') }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Razorpay Settings -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0 text-dark"><i class="fas fa-rupee-sign me-2"></i>Razorpay</h6>
                </div>
                <div class="card-body">
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" role="switch" id="payment_razorpay_enabled" name="payment_razorpay_enabled" value="1" {{ old('payment_razorpay_enabled', $settings['payment_razorpay_enabled'] ?? '') ? 'checked' : '' }}>
                        <label class="form-check-label" for="payment_razorpay_enabled">{{ __('Enable Razorpay Payment') }}</label>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Key ID') }}</label>
                            <input type="text" class="form-control" name="razorpay_key" value="{{ old('razorpay_key', $settings['razorpay_key'] ?? '') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Key Secret') }}</label>
                            <input type="text" class="form-control" name="razorpay_secret" value="{{ old('razorpay_secret', $settings['razorpay_secret'] ?? '') }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">{{ __('Webhook Secret') }}</label>
                            <input type="text" class="form-control" name="razorpay_webhook_secret" value="{{ old('razorpay_webhook_secret', $settings['razorpay_webhook_secret'] ?? '') }}">
                        </div>
                    </div>
                </div>
            </div>

            <hr class="my-4">

            <h2 class="h6 mb-3">{{ __('Payment Gateway Settings') }}</h2>

            <!-- Stripe Settings -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0 text-dark"><i class="fab fa-stripe me-2"></i>Stripe</h6>
                </div>
                <div class="card-body">
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" role="switch" id="payment_stripe_enabled" name="payment_stripe_enabled" value="1" {{ old('payment_stripe_enabled', $settings['payment_stripe_enabled'] ?? '') ? 'checked' : '' }}>
                        <label class="form-check-label" for="payment_stripe_enabled">{{ __('Enable Stripe Payment') }}</label>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Mode') }}</label>
                        <select class="form-select" name="stripe_mode">
                            <option value="test" {{ ($settings['stripe_mode'] ?? 'test') === 'test' ? 'selected' : '' }}>Test (Sandbox)</option>
                            <option value="live" {{ ($settings['stripe_mode'] ?? '') === 'live' ? 'selected' : '' }}>Live (Production)</option>
                        </select>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Test Public Key') }}</label>
                            <input type="text" class="form-control" name="stripe_test_public_key" value="{{ old('stripe_test_public_key', $settings['stripe_test_public_key'] ?? '') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Test Secret Key') }}</label>
                            <input type="text" class="form-control" name="stripe_test_secret_key" value="{{ old('stripe_test_secret_key', $settings['stripe_test_secret_key'] ?? '') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Live Public Key') }}</label>
                            <input type="text" class="form-control" name="stripe_live_public_key" value="{{ old('stripe_live_public_key', $settings['stripe_live_public_key'] ?? '') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Live Secret Key') }}</label>
                            <input type="text" class="form-control" name="stripe_live_secret_key" value="{{ old('stripe_live_secret_key', $settings['stripe_live_secret_key'] ?? '') }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">{{ __('Webhook Secret') }}</label>
                            <input type="text" class="form-control" name="stripe_webhook_secret" value="{{ old('stripe_webhook_secret', $settings['stripe_webhook_secret'] ?? '') }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- PayPal Settings -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0 text-dark"><i class="fab fa-paypal me-2"></i>PayPal</h6>
                </div>
                <div class="card-body">
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" role="switch" id="payment_paypal_enabled" name="payment_paypal_enabled" value="1" {{ old('payment_paypal_enabled', $settings['payment_paypal_enabled'] ?? '') ? 'checked' : '' }}>
                        <label class="form-check-label" for="payment_paypal_enabled">{{ __('Enable PayPal Payment') }}</label>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Mode') }}</label>
                        <select class="form-select" name="paypal_mode">
                            <option value="sandbox" {{ ($settings['paypal_mode'] ?? 'sandbox') === 'sandbox' ? 'selected' : '' }}>Sandbox</option>
                            <option value="live" {{ ($settings['paypal_mode'] ?? '') === 'live' ? 'selected' : '' }}>Live</option>
                        </select>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Sandbox Client ID') }}</label>
                            <input type="text" class="form-control" name="paypal_sandbox_client_id" value="{{ old('paypal_sandbox_client_id', $settings['paypal_sandbox_client_id'] ?? '') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Sandbox Secret') }}</label>
                            <input type="text" class="form-control" name="paypal_sandbox_secret" value="{{ old('paypal_sandbox_secret', $settings['paypal_sandbox_secret'] ?? '') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Live Client ID') }}</label>
                            <input type="text" class="form-control" name="paypal_live_client_id" value="{{ old('paypal_live_client_id', $settings['paypal_live_client_id'] ?? '') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Live Secret') }}</label>
                            <input type="text" class="form-control" name="paypal_live_secret" value="{{ old('paypal_live_secret', $settings['paypal_live_secret'] ?? '') }}">
                        </div>
                    </div>
                </div>
            </div>

            <hr class="my-4">

            <h2 class="h6 mb-3">{{ __('Google Maps Settings') }}</h2>

            <div class="form-check form-switch mb-3">
                <input class="form-check-input" type="checkbox" role="switch" id="google_maps_enabled" name="google_maps_enabled" value="1" {{ old('google_maps_enabled', $settings['google_maps_enabled'] ?? '') ? 'checked' : '' }}>
                <label class="form-check-label" for="google_maps_enabled">{{ __('Enable Google Maps') }}</label>
            </div>

            <div class="mb-3">
                <label for="google_maps_api_key" class="form-label">{{ __('Google Maps API Key') }}</label>
                <input type="text" class="form-control @error('google_maps_api_key') is-invalid @enderror" id="google_maps_api_key" name="google_maps_api_key" value="{{ old('google_maps_api_key', $settings['google_maps_api_key'] ?? '') }}">
                @error('google_maps_api_key')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text text-muted">{{ __('Required for map functionality. Leave empty to use GOOGLE_MAPS_API_KEY from .env file.') }}</div>
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">{{ __('Save Settings') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
