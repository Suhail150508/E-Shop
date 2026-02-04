@extends('layouts.frontend')

@section('content')
<div class="container py-5">
    @include('frontend.checkout.steps', ['currentStep' => 3])

    <div class="row g-4">
        <div class="col-lg-8">
            <form id="payment-form" action="{{ route('checkout.process') }}" method="POST">
                @csrf
                
                <!-- Payment Method Section -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold">{{ __('Payment Method') }}</h5>
                    </div>
                    <div class="card-body">
                        
                        <!-- Cash on Delivery -->
                        @if(isset($gateways['cod']))
                        <div class="mb-3">
                            <input type="radio" class="btn-check" name="payment_method" id="payment_cod" value="cod" checked>
                            <label class="btn btn-outline-secondary d-flex align-items-center justify-content-between w-100 p-3 text-start payment-option" for="payment_cod">
                                <div class="d-flex align-items-center">
                                    <div class="ms-2">
                                        <h6 class="mb-0 fw-bold">{{ __('Cash On Delivery') }}</h6>
                                        <small class="text-muted">{{ __('Pay nicely when you receive your order') }}</small>
                                    </div>
                                </div>
                                <i class="fas fa-money-bill-wave fa-lg"></i>
                            </label>
                        </div>
                        @endif

                        <!-- Wallet -->
                        @if(isset($gateways['wallet']))
                        <div class="mb-3">
                            <input type="radio" class="btn-check" name="payment_method" id="payment_wallet" value="wallet" {{ $walletBalance < $total ? 'disabled' : '' }}>
                            <label class="btn btn-outline-secondary d-flex align-items-center justify-content-between w-100 p-3 text-start payment-option {{ $walletBalance < $total ? 'opacity-50' : '' }}" for="payment_wallet">
                                <div class="d-flex align-items-center">
                                    <div class="ms-2">
                                        <h6 class="mb-0 fw-bold">{{ __('Wallet') }}</h6>
                                        <small class="text-muted">{{ __('Balance:') }} {{ config('app.currency', '$') }}{{ number_format($walletBalance, 2) }}</small>
                                        @if($walletBalance < $total)
                                            <div class="text-danger small mt-1">{{ __('Insufficient balance') }}</div>
                                        @endif
                                    </div>
                                </div>
                                <i class="fas fa-wallet fa-lg"></i>
                            </label>
                        </div>
                        @endif

                        @if(isset($gateways['stripe']) || isset($gateways['paypal']) || isset($gateways['razorpay']) || isset($gateways['paystack']))
                        <h6 class="fw-bold mt-4 mb-3">{{ __('Online Payment Method') }}</h6>
                        <div class="row g-3">
                            <!-- Stripe -->
                            @if(isset($gateways['stripe']))
                            <div class="col-6 col-md-4">
                                <input type="radio" class="btn-check" name="payment_method" id="payment_stripe" value="stripe">
                                <label class="btn btn-outline-secondary d-flex align-items-center justify-content-center p-2 w-100 payment-option h-100" for="payment_stripe">
                                    <span class="fw-bold"><i class="fab fa-stripe-s fa-3x align-middle"></i></span>
                                </label>
                            </div>
                            @endif

                            <!-- PayPal -->
                            @if(isset($gateways['paypal']))
                            <div class="col-6 col-md-4">
                                <input type="radio" class="btn-check" name="payment_method" id="payment_paypal" value="paypal">
                                <label class="btn btn-outline-secondary d-flex align-items-center justify-content-center p-2 w-100 payment-option h-100" for="payment_paypal">
                                    <span class="fw-bold"><i class="fab fa-paypal fa-3x align-middle"></i></span>
                                </label>
                            </div>
                            @endif

                            <!-- Razorpay -->
                            @if(isset($gateways['razorpay']))
                            <div class="col-6 col-md-4">
                                <input type="radio" class="btn-check" name="payment_method" id="payment_razorpay" value="razorpay">
                                <label class="btn btn-outline-secondary d-flex align-items-center justify-content-center p-2 w-100 payment-option h-100" for="payment_razorpay">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="fas fa-money-bill-wave fa-2x mb-1"></i>
                                        <span class="fw-bold small">Razorpay</span>
                                    </div>
                                </label>
                            </div>
                            @endif

                            <!-- Paystack -->
                            @if(isset($gateways['paystack']))
                            <div class="col-6 col-md-4">
                                <input type="radio" class="btn-check" name="payment_method" id="payment_paystack" value="paystack">
                                <label class="btn btn-outline-secondary d-flex align-items-center justify-content-center p-2 w-100 payment-option h-100" for="payment_paystack">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="fas fa-layer-group fa-2x mb-1"></i>
                                        <span class="fw-bold small">Paystack</span>
                                    </div>
                                </label>
                            </div>
                            @endif
                        </div>
                        @endif
                        @error('payment_method')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </form>
        </div>

        <!-- Order Summary Sidebar -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="fw-bold mb-4">{{ __('Order Summary') }}</h5>

                    <!-- Order Items Preview -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="small fw-bold text-muted mb-0">{{ __('Package 1 of 1') }}</h6>
                            <small class="text-muted">{{ __('Shipped by: Fresh Grocer') }}</small>
                        </div>
                        <div class="bg-light p-3 rounded">
                            <div class="d-flex justify-content-between small text-muted mb-2">
                                <span>{{ __('Delivery Charge:') }} {{ config('app.currency', '$') }}{{ number_format($shippingCost, 2) }}</span>
                                <span>{{ __('Tax:') }} {{ $taxPercent }}%</span>
                            </div>
                            
                            <div class="d-flex align-items-center mt-3">
                                <div>
                                    <h6 class="mb-0 small fw-bold text-truncate" style="max-width: 150px;">{{ $cartItems->first()['name'] ?? 'Item' }}</h6>
                                    <small class="text-muted">{{ $cartItems->sum('quantity') }} {{ __('Items') }}</small>
                                </div>
                                <div class="ms-auto fw-bold">{{ config('app.currency', '$') }}{{ number_format($subtotal, 2) }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Coupon -->
                    <div class="mb-4">
                        @if($coupon)
                            <div class="alert alert-success d-flex justify-content-between align-items-center mb-0 p-2">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-tag me-2"></i>
                                    <div>
                                        <div class="fw-bold small">{{ $coupon['code'] }}</div>
                                        <div class="small" style="font-size: 0.75rem;">{{ __('Applied') }}</div>
                                    </div>
                                </div>
                                <button type="button" class="btn-close btn-sm" id="remove_coupon_btn" aria-label="Remove"></button>
                            </div>
                        @else
                            <a href="#" class="d-flex justify-content-between align-items-center text-decoration-none" data-bs-toggle="collapse" data-bs-target="#couponCollapse">
                                <span class="fw-bold text-primary">{{ __('Import your coupon') }}</span>
                                <i class="fas fa-chevron-right text-primary small"></i>
                            </a>
                            <div class="collapse mt-2" id="couponCollapse">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="coupon_code" placeholder="{{ __('Enter coupon code') }}">
                                    <button class="btn btn-primary" id="apply_coupon_btn" type="button">{{ __('Apply') }}</button>
                                </div>
                                <div id="coupon_message" class="small mt-1"></div>
                            </div>
                        @endif
                    </div>
                    
                    <hr>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">{{ __('Sub Total') }}</span>
                        <span class="fw-bold">{{ config('app.currency', '$') }}{{ number_format($subtotal, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">{{ __('Discount') }}</span>
                        <span class="fw-bold text-success" id="summary_discount">(-) {{ config('app.currency', '$') }}{{ number_format($discount, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">{{ __('Tax') }} ({{ $taxPercent }}%)</span>
                        <span class="fw-bold">{{ config('app.currency', '$') }}{{ number_format($tax, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">{{ __('Delivery Fee') }}</span>
                        <span class="fw-bold">{{ config('app.currency', '$') }}{{ number_format($shippingCost, 2) }}</span>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between mb-4">
                        <span class="fw-bold fs-5">{{ __('Total Amount') }}</span>
                        <span class="fw-bold fs-5 text-primary" id="summary_total">{{ config('app.currency', '$') }}{{ number_format($total, 2) }}</span>
                    </div>

                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" name="terms" id="termsCheck" form="payment-form" required>
                        <label class="form-check-label small text-muted" for="termsCheck">
                            {!! __('I confirm I\'ve read and accept the <a href="#">Terms And Conditions</a> & <a href="#">Privacy Policy</a>') !!}
                        </label>
                    </div>

                    <button type="submit" form="payment-form" class="btn btn-primary w-100 py-2 fw-bold">
                        {{ __('Proceed To Checkout') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .payment-option {
        cursor: pointer;
        transition: all 0.2s;
        border: 2px solid #e9ecef;
    }
    .payment-option:hover {
        border-color: var(--rust);
        background-color: rgba(209, 122, 92, 0.05);
    }
    .btn-check:checked + .payment-option {
        border-color: var(--rust);
        background-color: rgba(209, 122, 92, 0.1);
        color: var(--black);
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const applyBtn = document.getElementById('apply_coupon_btn');
        const removeBtn = document.getElementById('remove_coupon_btn');
        const couponInput = document.getElementById('coupon_code');
        const messageDiv = document.getElementById('coupon_message');

        if (applyBtn) {
            applyBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const code = couponInput.value;
                if (!code) {
                    toastr.error('{{ __("Please enter a coupon code.") }}', 'Error');
                    return;
                }

                applyBtn.disabled = true;
                const originalText = applyBtn.innerHTML;
                applyBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
                messageDiv.innerHTML = '';

                fetch('{{ route("checkout.coupon.apply") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ code: code })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        toastr.success(data.message, 'Success');
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        applyBtn.disabled = false;
                        applyBtn.innerHTML = originalText;
                        toastr.error(data.message, 'Error');
                    }
                })
                .catch(error => {
                    applyBtn.disabled = false;
                    applyBtn.innerHTML = originalText;
                    toastr.error('{{ __("An error occurred. Please try again.") }}', 'Error');
                    console.error('Error:', error);
                });
            });
        }

        if (removeBtn) {
            removeBtn.addEventListener('click', function(e) {
                e.preventDefault();
                if(!confirm('{{ __("Are you sure you want to remove this coupon?") }}')) return;

                fetch('{{ route("checkout.coupon.remove") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                });
            });
        }

        // Payment Form Submission Handler
        const paymentForm = document.getElementById('payment-form');
        if(paymentForm) {
            paymentForm.addEventListener('submit', function(e) {
                const submitBtn = document.querySelector('button[type="submit"][form="payment-form"]');
                if(submitBtn && !submitBtn.disabled) {
                    submitBtn.disabled = true;
                    const originalText = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> {{ __("Processing...") }}';
                    
                    // Re-enable after 15s in case of error/timeout to allow retry
                    setTimeout(() => {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    }, 15000);
                }
            });
        }
    });
</script>
@endsection
