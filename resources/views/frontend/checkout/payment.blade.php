@extends('layouts.frontend')

@section('title', __('common.payment_information'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('frontend/css/checkout.css') }}">
@endpush

@section('content')
<div class="checkout-page-wrap">
<div class="container py-5">
    @include('frontend.checkout.steps', ['currentStep' => 3])

    <div class="row g-4">
        <div class="col-lg-8">
            <form id="payment-form" action="{{ route('checkout.process') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="bank_transaction_id" id="hidden_transaction_id">
                <input type="hidden" name="bank_name" id="hidden_bank_name">

                <!-- Payment Method Section -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold">{{ setting('checkout_payment_title', __('common.payment_method')) }}</h5>
                    </div>
                    <div class="card-body">

                        <!-- Cash on Delivery -->
                        @if(isset($gateways['cod']))
                        <div class="mb-3">
                            <input type="radio" class="btn-check" name="payment_method" id="payment_cod" value="cod" checked>
                            <label class="btn btn-outline-secondary d-flex align-items-center justify-content-between w-100 p-3 text-start payment-option" for="payment_cod">
                                <div class="d-flex align-items-center">
                                    <div class="ms-2">
                                        <h6 class="mb-0 fw-bold">{{ __('common.cash_on_delivery') }}</h6>
                                        <small class="text-muted">{{ __('common.cod_description') }}</small>
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
                                        <h6 class="mb-0 fw-bold">{{ __('common.wallet') }}</h6>
                                        <small class="text-muted">{{ __('common.wallet_balance') }} {{ format_price($walletBalance) }}</small>
                                        @if($walletBalance < $total)
                                            <div class="text-danger small mt-1">{{ __('common.insufficient_balance') }}</div>
                                        @endif
                                    </div>
                                </div>
                                <i class="fas fa-wallet fa-lg"></i>
                            </label>
                        </div>
                        @endif

                        <!-- Bank Transfer -->
                        @if(isset($gateways['bank']))
                        <div class="mb-3">
                            <input type="radio" class="btn-check" name="payment_method" id="payment_bank" value="bank">
                            <label class="btn btn-outline-secondary d-flex align-items-center justify-content-between w-100 p-3 text-start payment-option" for="payment_bank">
                                <div class="d-flex align-items-center">
                                    <div class="ms-2">
                                        <h6 class="mb-0 fw-bold">{{ __('common.bank_transfer') }}</h6>
                                        <small class="text-muted">{{ __('common.bank_transfer_description') }}</small>
                                    </div>
                                </div>
                                <i class="fas fa-university fa-lg"></i>
                            </label>
                        </div>
                        @endif

                        @if(isset($gateways['stripe']) || isset($gateways['paypal']) || isset($gateways['razorpay']) || isset($gateways['paystack']))
                        <h6 class="fw-bold mt-4 mb-3">{{ __('common.online_payment_method') }}</h6>
                        <div class="row g-3">
                            <!-- Stripe -->
                            @if(isset($gateways['stripe']))
                            <div class="col-6 col-md-4">
                                <input type="radio" class="btn-check" name="payment_method" id="payment_stripe" value="stripe">
                                <label class="btn btn-outline-secondary d-flex align-items-center justify-content-center p-2 w-100 payment-option h-100" for="payment_stripe">
                                    <span class="fw-bold"><i class="fab fa-stripe fa-3x align-middle icon-stripe"></i></span>
                                </label>
                            </div>
                            @endif

                            <!-- PayPal -->
                            @if(isset($gateways['paypal']))
                            <div class="col-6 col-md-4">
                                <input type="radio" class="btn-check" name="payment_method" id="payment_paypal" value="paypal">
                                <label class="btn btn-outline-secondary d-flex align-items-center justify-content-center p-2 w-100 payment-option h-100" for="payment_paypal">
                                    <span class="fw-bold"><i class="fab fa-paypal fa-3x align-middle icon-paypal"></i></span>
                                </label>
                            </div>
                            @endif

                            <!-- Razorpay -->
                            @if(isset($gateways['razorpay']))
                            <div class="col-6 col-md-4">
                                <input type="radio" class="btn-check" name="payment_method" id="payment_razorpay" value="razorpay">
                                <label class="btn btn-outline-secondary d-flex align-items-center justify-content-center p-2 w-100 payment-option h-100" for="payment_razorpay">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="fas fa-money-bill-wave fa-2x mb-1 icon-razorpay"></i>
                                        <span class="fw-bold small">{{ __('Razorpay') }}</span>
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
                                        <i class="fas fa-layer-group fa-2x mb-1 icon-paystack"></i>
                                        <span class="fw-bold small">{{ __('Paystack') }}</span>
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
                    <h5 class="fw-bold mb-4">{{ __('common.order_summary') }}</h5>

                    @if($freeShippingMin > 0)
                        <div class="mb-3">
                            @if($subtotal >= $freeShippingMin)
                                <div class="alert alert-success py-2 small">
                                    <i class="fas fa-check-circle me-1"></i> {{ __('common.free_shipping_eligible') }}
                                </div>
                            @else
                                <div class="alert alert-info py-2 small">
                                    <i class="fas fa-info-circle me-1"></i> {{ __('common.spend_more_for_free_shipping', ['amount' => format_price($freeShippingMin - $subtotal)]) }}
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Order Items Preview -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="small fw-bold text-muted mb-0">{{ __('common.package_count', ['current' => 1, 'total' => 1]) }}</h6>
                            <small class="text-muted">{{ __('common.shipped_by_store') }}</small>
                        </div>
                        <div class="bg-light p-3 rounded">
                            <div class="d-flex justify-content-between small text-muted mb-2">
                                <span>{{ __('common.delivery_charge') }} {{ format_price($shippingCost) }}</span>
                                <span>{{ __('common.tax') }} {{ $taxPercent }}%</span>
                            </div>

                            <div class="d-flex align-items-center mt-3">
                                <div>
                                    <h6 class="mb-0 small fw-bold text-truncate text-truncate-150">{{ $cartItems->first()['name'] ?? __('common.item') }}</h6>
                                    <small class="text-muted">{{ $cartItems->sum('quantity') }} {{ __('common.items') }}</small>
                                </div>
                                <div class="ms-auto fw-bold">{{ format_price($subtotal) }}</div>
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
                                        <div class="small text-xs">{{ __('common.applied') }}</div>
                                    </div>
                                </div>
                                <button type="button" class="btn-close btn-sm" id="remove_coupon_btn" aria-label="Remove"></button>
                            </div>
                        @else
                            <a href="#" class="d-flex justify-content-between align-items-center text-decoration-none" data-bs-toggle="collapse" data-bs-target="#couponCollapse">
                                <span class="fw-bold text-primary">{{ __('common.import_coupon') }}</span>
                                <i class="fas fa-chevron-right text-primary small"></i>
                            </a>
                            <div class="collapse mt-2" id="couponCollapse">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="coupon_code" placeholder="{{ __('common.enter_coupon_code') }}">
                                    <button class="btn btn-primary" id="apply_coupon_btn" type="button">{{ __('common.apply') }}</button>
                                </div>
                                <div id="coupon_message" class="small mt-1"></div>
                            </div>
                        @endif
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">{{ __('common.subtotal') }}</span>
                        <span class="fw-bold">{{ format_price($subtotal ?? 0) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">{{ __('common.discount') }}</span>
                        <span class="fw-bold text-success" id="summary_discount">(-) {{ format_price($discount ?? 0) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">{{ __('common.tax') }} ({{ $taxPercent ?? 0 }}%)</span>
                        <span class="fw-bold">{{ format_price($tax ?? 0) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">{{ __('common.delivery_fee') }}</span>
                        <span class="fw-bold">{{ format_price($shippingCost ?? 0) }}</span>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between mb-4">
                        <span class="fw-bold fs-5">{{ __('common.total_amount') }}</span>
                        <span class="fw-bold fs-5 text-primary" id="summary_total">{{ format_price($total ?? 0) }}</span>
                    </div>

                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" name="terms" id="termsCheck" form="payment-form" required>
                        <label class="form-check-label small text-muted" for="termsCheck">
                            {!! __('common.terms_accept', [
                                'terms_link' => '<a href="'.e(route('pages.terms')).'">'.e(__('common.terms_and_conditions')).'</a>',
                                'privacy_link' => '<a href="'.e(route('pages.privacy')).'">'.e(__('common.privacy_policy')).'</a>',
                            ]) !!}
                        </label>
                    </div>

                    <button type="submit" form="payment-form" class="btn btn-primary w-100 py-3 fw-bold shadow-sm">{{ __('common.pay_now') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bank Transfer Modal -->
<div class="modal fade" id="bankTransferModal" tabindex="-1" aria-labelledby="bankTransferModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="bankTransferModalLabel">{{ __('common.bank_transfer_details') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-3">{{ __('common.bank_transfer_instruction_modal') }}</p>
                
                <div class="mb-3">
                    <label for="modal_bank_name" class="form-label small fw-bold">{{ __('common.bank_name') }}</label>
                    <input type="text" class="form-control" id="modal_bank_name" placeholder="{{ __('common.enter_bank_name') }}">
                </div>

                <div class="mb-3">
                    <label for="modal_transaction_id" class="form-label small fw-bold required">{{ __('common.transaction_id') }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="modal_transaction_id" placeholder="{{ __('common.enter_transaction_id') }}">
                    <div class="invalid-feedback" id="modal_transaction_id_error">
                        {{ __('common.transaction_id_required') }}
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                <button type="button" class="btn btn-primary px-4" id="confirmBankTransfer">{{ __('common.submit') }}</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const paymentForm = document.getElementById('payment-form');
        const bankModalEl = document.getElementById('bankTransferModal');
        const bankModal = new bootstrap.Modal(bankModalEl);
        const confirmBankTransferBtn = document.getElementById('confirmBankTransfer');
        const transactionIdInput = document.getElementById('modal_transaction_id');
        const transactionIdError = document.getElementById('modal_transaction_id_error');

        paymentForm.addEventListener('submit', function(e) {
            const selectedPayment = document.querySelector('input[name="payment_method"]:checked');
            
            // If Bank Transfer is selected
            if (selectedPayment && selectedPayment.value === 'bank') {
                // Check if we already have the transaction ID populated (from modal)
                const hiddenTransactionId = document.getElementById('hidden_transaction_id').value;
                
                if (!hiddenTransactionId) {
                    e.preventDefault(); // Stop form submission
                    bankModal.show(); // Show modal
                }
            }
        });

        confirmBankTransferBtn.addEventListener('click', function() {
            const tid = transactionIdInput.value.trim();
            const bankName = document.getElementById('modal_bank_name').value.trim();

            // Validate Transaction ID
            if (!tid) {
                transactionIdInput.classList.add('is-invalid');
                transactionIdError.style.display = 'block';
                return;
            } else {
                transactionIdInput.classList.remove('is-invalid');
                transactionIdError.style.display = 'none';
            }

            // Copy values to hidden fields
            document.getElementById('hidden_transaction_id').value = tid;
            document.getElementById('hidden_bank_name').value = bankName;

            // Hide modal
            bankModal.hide();

            // Submit the form
            paymentForm.submit();
        });
        
        // Clear error on input
        transactionIdInput.addEventListener('input', function() {
            if (this.value.trim()) {
                this.classList.remove('is-invalid');
                transactionIdError.style.display = 'none';
            }
        });
    });
</script>
@endpush
