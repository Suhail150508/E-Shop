@extends('layouts.frontend')

@push('styles')
    <link rel="stylesheet" href="{{ asset('frontend/css/checkout.css') }}">
@endpush

@section('content')
<div class="container py-5">
    @include('frontend.checkout.steps', ['currentStep' => 4])

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="text-center mb-5">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary text-white mb-3 shadow-sm success-icon-wrapper">
                    <i class="fas fa-check"></i>
                </div>
                <h6 class="text-muted text-uppercase letter-spacing-2 mb-2">{{ __('Hooray!') }}</h6>
                <h2 class="fw-bold mb-3">{{ __('All Orders Wrapped Up!') }}</h2>
                <p class="text-muted mx-auto success-message-text">
                    {{ __('Thank you for your order! You can track your order status and view details from your account.') }}
                </p>
            </div>

            <div class="card border-0 shadow-sm mb-5 overflow-hidden">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-uppercase small text-muted">
                                <tr>
                                    <th class="py-3 ps-4 border-bottom-0">{{ __('Order ID') }}</th>
                                    <th class="py-3 border-bottom-0">{{ __('Invoice Number') }}</th>
                                    <th class="py-3 border-bottom-0">{{ __('Status') }}</th>
                                    <th class="py-3 border-bottom-0">{{ __('Order Amount') }}</th>
                                    <th class="py-3 pe-4 text-end border-bottom-0">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="py-4 ps-4 fw-bold text-primary">#{{ $order->order_number ?? $order->id }}</td>
                                    <td class="py-4 text-muted">{{ $order->invoice_number ?? '-' }}</td>
                                    <td class="py-4">
                                        @if($order->status == 'pending')
                                            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">{{ ucfirst($order->status) }}</span>
                                        @elseif($order->status == 'completed' || $order->status == 'delivered')
                                            <span class="badge bg-success px-3 py-2 rounded-pill">{{ ucfirst($order->status) }}</span>
                                        @elseif($order->status == 'cancelled')
                                            <span class="badge bg-danger px-3 py-2 rounded-pill">{{ ucfirst($order->status) }}</span>
                                        @else
                                            <span class="badge bg-secondary px-3 py-2 rounded-pill">{{ ucfirst($order->status) }}</span>
                                        @endif
                                    </td>
                                    <td class="py-4 fw-bold">{{ $order->formatPrice($order->total) }}</td>
                                    <td class="py-4 pe-4 text-end">
                                        <a href="{{ route('customer.orders.show', $order->id) }}" class="btn btn-outline-primary btn-sm rounded-circle btn-icon-circle-sm" title="{{ __('View Details') }}">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-center gap-4 mb-5 flex-wrap">
                <div class="d-flex align-items-center gap-2">
                    <span class="fw-bold text-muted">{{ __('Payment Gateway:') }}</span>
                    <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-2 rounded-pill">{{ ucfirst($order->payment_method) }}</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="fw-bold text-muted">{{ __('Payment Status:') }}</span>
                    @if($order->payment_status == 'paid')
                        <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-2 rounded-pill">{{ ucfirst($order->payment_status) }}</span>
                    @else
                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning px-3 py-2 rounded-pill">{{ ucfirst($order->payment_status) }}</span>
                    @endif
                </div>
            </div>

            <div class="text-center mb-5">
                <a href="{{ route('customer.orders.index') }}" class="btn btn-primary px-5 py-3 fw-bold me-3 rounded-pill shadow-sm">{{ __('My Order') }}</a>
                <a href="{{ route('shop.index') }}" class="btn btn-outline-primary px-5 py-3 fw-bold rounded-pill">{{ __('Continue Shopping') }}</a>
            </div>
        </div>
    </div>
</div>
@endsection
