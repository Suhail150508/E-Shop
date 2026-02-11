@extends('layouts.frontend')

@section('title', __('common.order_confirmation'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('frontend/css/checkout.css') }}">
@endpush

@section('content')
<div class="checkout-page-wrap">
    <div class="container py-5">
        @include('frontend.checkout.steps', ['currentStep' => 4])

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="text-center mb-5">
                    <div class="success-animation-wrapper">
                        <div class="success-checkmark">
                            <i class="fas fa-check"></i>
                        </div>
                    </div>
                    <h6 class="text-primary text-uppercase letter-spacing-2 mb-2 fw-bold">{{ __('common.hooray') }}</h6>
                    <h2 class="fw-bold mb-3 display-6">{{ __('common.all_orders_wrapped_up') }}</h2>
                    <p class="text-muted mx-auto" style="max-width: 500px; font-size: 1.1rem;">
                        {{ __('common.order_success_message') }}
                    </p>
                </div>

                <div class="order-summary-card mb-5">
                    <div class="order-summary-header text-center bg-light-blue">
                        <h5 class="mb-0 fw-bold">{{ __('common.order_details') }}</h5>
                    </div>
                    <div class="card-body p-4 p-md-5">
                        <div class="row g-4 justify-content-center text-center">
                            <div class="col-6 col-md-3">
                                <div class="order-info-item">
                                    <span class="order-info-label">{{ __('common.order_id') }}</span>
                                    <span class="order-info-value text-primary">#{{ $order->order_number ?? $order->id }}</span>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="order-info-item">
                                    <span class="order-info-label">{{ __('common.date') }}</span>
                                    <span class="order-info-value">{{ $order->created_at->format('d M, Y') }}</span>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="order-info-item">
                                    <span class="order-info-label">{{ __('common.order_amount') }}</span>
                                    <span class="order-info-value">{{ $order->formatPrice($order->total) }}</span>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="order-info-item">
                                    <span class="order-info-label">{{ __('common.payment_method') }}</span>
                                    <span class="order-info-value text-uppercase">{{ $order->payment_method }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 pt-4 border-top text-center">
                            <a href="{{ route('customer.orders.show', $order->id) }}" class="btn btn-link text-decoration-none fw-bold">
                                {{ __('common.view_order_details') }} <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="text-center mb-5">
                    <a href="{{ route('customer.orders.index') }}" class="btn btn-primary px-5 py-3 fw-bold me-md-3 mb-3 mb-md-0 rounded-pill shadow-sm">
                        <i class="fas fa-box-open me-2"></i> {{ __('common.my_order') }}
                    </a>
                    <a href="{{ route('shop.index') }}" class="btn btn-outline-primary px-5 py-3 fw-bold rounded-pill">
                        {{ __('common.continue_shopping') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
