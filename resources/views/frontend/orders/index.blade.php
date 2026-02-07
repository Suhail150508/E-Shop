@extends('layouts.customer')

@section('title', __('My Orders'))

@section('account_content')
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white border-bottom-0 py-3 px-4">
            <h5 class="mb-0 fw-bold">{{ __('Order History') }}</h5>
        </div>
        <div class="card-body p-0">
            @if($orders->isEmpty())
                <div class="text-center py-5">
                    <div class="mb-3 text-muted">
                        <i class="fa-solid fa-box-open fa-3x opacity-25"></i>
                    </div>
                    <p class="text-muted mb-3">{{ __('You have no orders yet.') }}</p>
                    <a href="{{ route('shop.index') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
                        {{ __('Start Shopping') }}
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 border-0 text-muted small text-uppercase fw-semibold">{{ __('SL') }}</th>
                                <th class="border-0 text-muted small text-uppercase fw-semibold">{{ __('Order ID') }}</th>
                                <th class="border-0 text-muted small text-uppercase fw-semibold">{{ __('Order Date') }}</th>
                                <th class="border-0 text-muted small text-uppercase fw-semibold">{{ __('Order Amount') }}</th>
                                <th class="border-0 text-muted small text-uppercase fw-semibold">{{ __('Payment Status') }}</th>
                                <th class="border-0 text-muted small text-uppercase fw-semibold">{{ __('Status') }}</th>
                                <th class="text-end pe-4 border-0 text-muted small text-uppercase fw-semibold">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $key => $order)
                                <tr>
                                    <td class="ps-4 text-muted">{{ $orders->firstItem() + $key }}</td>
                                    <td class="fw-medium">
                                        <a href="{{ route('customer.orders.show', $order) }}" class="text-decoration-none text-dark">
                                            #{{ $order->order_number }}
                                        </a>
                                    </td>
                                    <td class="text-muted">{{ $order->created_at?->format('d F Y h:i A') }}</td>
                                    <td class="fw-bold">
                                        {{ $order->formatPrice($order->total) }}
                                    </td>
                                    <td>
                                        <span class="badge rounded-pill bg-{{ $order->payment_status_color }} bg-opacity-10 text-{{ $order->payment_status_color }} px-3 py-2">{{ __(ucfirst($order->payment_status)) }}</span>
                                    </td>
                                    <td>
                                        <span class="badge rounded-pill bg-{{ $order->status_color }} bg-opacity-10 text-{{ $order->status_color }} text-capitalize px-3 py-2">
                                            {{ __(ucfirst($order->status)) }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="{{ route('customer.orders.show', $order) }}" class="btn btn-sm btn-outline-info rounded-2 shadow-sm d-flex align-items-center justify-content-center action-btn-circle" data-bs-toggle="tooltip" title="{{ __('View Details') }}">
                                                <i class="fa-regular fa-eye"></i>
                                            </a>
                                            <a href="{{ route('customer.orders.invoice', $order) }}" class="btn btn-sm btn-outline-secondary rounded-2 shadow-sm d-flex align-items-center justify-content-center action-btn-circle" data-bs-toggle="tooltip" title="{{ __('Download Invoice') }}">
                                                <i class="fa-regular fa-file-lines"></i>
                                            </a>
                                            @if($order->status === 'delivered' && $order->payment_status === 'paid' && $order->refunds->isEmpty())
                                                <a href="{{ route('customer.orders.show', $order) }}?trigger_refund=1" 
                                                   class="btn btn-sm btn-outline-danger rounded-2 shadow-sm d-flex align-items-center justify-content-center action-btn-circle" data-bs-toggle="tooltip" title="{{ __('Request Refund') }}">
                                                    <i class="fa-solid fa-arrow-rotate-left"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-top">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
