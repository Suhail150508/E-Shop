@extends('layouts.admin')

@section('page_title', 'Payment Method')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1">Payment Method</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Payment Method</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <!-- Sidebar List -->
        <div class="col-lg-3 mb-4">
            <div class="list-group shadow-sm">
                @foreach($gateways as $key => $name)
                    <a href="{{ route('admin.payment-methods.index', ['gateway' => $key]) }}" 
                       class="list-group-item list-group-item-action d-flex align-items-center py-3 {{ $currentGateway == $key ? 'active bg-primary border-primary' : '' }}">
                       <i class="fas fa-bars me-3 {{ $currentGateway == $key ? '' : 'text-muted' }}"></i>
                       <span class="fw-medium">{{ $name }}</span>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Configuration Form -->
        <div class="col-lg-9">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="mb-0 fw-bold">{{ $gateways[$currentGateway] }} Configuration</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.payment-methods.update', $currentGateway) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Visibility Status -->
                        <div class="mb-4">
                            <label class="form-label fw-bold d-block mb-2">Visibility Status</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="status" name="status" {{ ($settings['payment_' . $currentGateway . '_enabled'] ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label" for="status">Enable {{ $gateways[$currentGateway] }}</label>
                            </div>
                        </div>

                        <!-- Image Upload -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Image</label>
                            <div class="card p-3 bg-light border-dashed text-center mb-2" style="border: 2px dashed #dee2e6;">
                                @if(isset($settings['payment_' . $currentGateway . '_image']))
                                    <div class="mb-3">
                                        <img src="{{ asset($settings['payment_' . $currentGateway . '_image']) }}" alt="{{ $gateways[$currentGateway] }}" style="max-height: 80px; max-width: 100%;">
                                    </div>
                                @else
                                    <div class="mb-3">
                                        <h3 class="text-muted fw-bold">{{ $gateways[$currentGateway] }}</h3>
                                    </div>
                                @endif
                                <p class="text-muted small mb-0">Click here to Choose File and upload</p>
                                <input type="file" class="form-control position-absolute top-0 start-0 w-100 h-100 opacity-0" style="cursor: pointer;" name="image">
                            </div>
                        </div>

                        <!-- Currency -->
                        @if($currentGateway != 'bank')
                        <div class="mb-4">
                            <label for="currency" class="form-label fw-bold">Currency *</label>
                            <select class="form-select" id="currency" name="currency">
                                @foreach($currencies as $currency)
                                    <option value="{{ $currency->code }}" {{ ($settings['payment_' . $currentGateway . '_currency'] ?? 'USD') == $currency->code ? 'selected' : '' }}>
                                        {{ $currency->code }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                        <!-- Stripe Specific Fields -->
                        @if($currentGateway == 'stripe')
                            <div class="mb-4">
                                <label for="mode" class="form-label fw-bold">Mode *</label>
                                <select class="form-select" id="mode" name="mode">
                                    <option value="test" {{ ($settings['stripe_mode'] ?? 'test') == 'test' ? 'selected' : '' }}>Sandbox</option>
                                    <option value="live" {{ ($settings['stripe_mode'] ?? 'test') == 'live' ? 'selected' : '' }}>Live</option>
                                </select>
                            </div>

                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="test_key" class="form-label fw-bold">Test Publishable Key</label>
                                    <input type="text" class="form-control" id="test_key" name="test_key" value="{{ $settings['stripe_test_key'] ?? '' }}">
                                </div>
                                <div class="col-12">
                                    <label for="test_secret" class="form-label fw-bold">Test Secret Key</label>
                                    <input type="text" class="form-control" id="test_secret" name="test_secret" value="{{ $settings['stripe_test_secret'] ?? '' }}">
                                </div>
                                <div class="col-12">
                                    <label for="live_key" class="form-label fw-bold">Live Publishable Key</label>
                                    <input type="text" class="form-control" id="live_key" name="live_key" value="{{ $settings['stripe_live_key'] ?? '' }}">
                                </div>
                                <div class="col-12">
                                    <label for="live_secret" class="form-label fw-bold">Live Secret Key</label>
                                    <input type="text" class="form-control" id="live_secret" name="live_secret" value="{{ $settings['stripe_live_secret'] ?? '' }}">
                                </div>
                            </div>
                        @endif

                        <!-- Paypal Specific Fields -->
                        @if($currentGateway == 'paypal')
                            <div class="mb-4">
                                <label for="mode" class="form-label fw-bold">Mode *</label>
                                <select class="form-select" id="mode" name="mode">
                                    <option value="sandbox" {{ ($settings['paypal_mode'] ?? 'sandbox') == 'sandbox' ? 'selected' : '' }}>Sandbox</option>
                                    <option value="live" {{ ($settings['paypal_mode'] ?? 'sandbox') == 'live' ? 'selected' : '' }}>Live</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="client_id" class="form-label fw-bold">Client ID *</label>
                                <input type="text" class="form-control" id="client_id" name="client_id" value="{{ $settings['paypal_client_id'] ?? '' }}">
                            </div>
                            <div class="mb-3">
                                <label for="client_secret" class="form-label fw-bold">Client Secret *</label>
                                <input type="text" class="form-control" id="client_secret" name="client_secret" value="{{ $settings['paypal_client_secret'] ?? '' }}">
                            </div>
                        @endif

                        <!-- Razorpay Specific Fields -->
                        @if($currentGateway == 'razorpay')
                            <div class="mb-3">
                                <label for="key" class="form-label fw-bold">Razorpay Key *</label>
                                <input type="text" class="form-control" id="key" name="key" value="{{ $settings['razorpay_key'] ?? '' }}">
                            </div>
                            <div class="mb-3">
                                <label for="secret" class="form-label fw-bold">Razorpay Secret *</label>
                                <input type="text" class="form-control" id="secret" name="secret" value="{{ $settings['razorpay_secret'] ?? '' }}">
                            </div>
                        @endif

                        <!-- Paystack Specific Fields -->
                        @if($currentGateway == 'paystack')
                            <div class="mb-3">
                                <label for="public_key" class="form-label fw-bold">Public Key *</label>
                                <input type="text" class="form-control" id="public_key" name="public_key" value="{{ $settings['paystack_public_key'] ?? '' }}">
                            </div>
                            <div class="mb-3">
                                <label for="secret_key" class="form-label fw-bold">Secret Key *</label>
                                <input type="text" class="form-control" id="secret_key" name="secret_key" value="{{ $settings['paystack_secret_key'] ?? '' }}">
                            </div>
                            <div class="mb-3">
                                <label for="merchant_email" class="form-label fw-bold">Merchant Email *</label>
                                <input type="email" class="form-control" id="merchant_email" name="merchant_email" value="{{ $settings['paystack_merchant_email'] ?? '' }}">
                            </div>
                        @endif

                        <!-- Bank Payment Specific Fields -->
                        @if($currentGateway == 'bank')
                            <div class="mb-3">
                                <label for="bank_details" class="form-label fw-bold">Bank Details *</label>
                                <textarea class="form-control" id="bank_details" name="bank_details" rows="5">{{ $settings['bank_details'] ?? '' }}</textarea>
                                <div class="form-text">Enter bank name, account number, sort code, IBAN, etc.</div>
                            </div>
                        @endif

                        <div class="mt-4">
                            <button type="submit" class="btn btn-dark px-4 py-2">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
