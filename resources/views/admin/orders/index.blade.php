@extends('layouts.admin')

@section('page_title', __('Orders'))

@section('content')
<div class="d-flex flex-column gap-4">
    <!-- Header & Search -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <h2 class="h4 fw-bold mb-0">
            <i class="bi bi-cart3 me-2 text-primary"></i>{{ __('Orders') }}
        </h2>
        <div class="flex-grow-1 mx-md-4" style="max-width: 500px;">
            <form action="{{ route('admin.orders.index') }}" method="GET" class="position-relative">
                <input type="text" name="customer" value="{{ request('customer') }}" 
                    class="form-control border-0 bg-white shadow-sm rounded-pill py-2 ps-4 pe-5" 
                    placeholder="{{ __('Search by customer name or email...') }}">
                <button type="submit" class="btn position-absolute top-50 end-0 translate-middle-y me-2 text-primary">
                    <i class="bi bi-search"></i>
                </button>
            </form>
        </div>
        <div class="d-flex gap-2">
            <!-- Additional top-level actions if needed -->
        </div>
    </div>

    <!-- Filters & Tabs -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <!-- Filter Row -->
            <div class="p-3 border-bottom">
                <form action="{{ route('admin.orders.index') }}" method="GET" id="filter-form">
                    <div class="row g-3">
                         <!-- Hidden inputs to preserve search/status when filtering -->
                        @if(request('customer')) <input type="hidden" name="customer" value="{{ request('customer') }}"> @endif
                        @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif

                        <div class="col-12 col-md-3">
                             <select name="payment_status" class="form-select border-0 bg-light rounded-3 py-2 fs-14" onchange="this.form.submit()">
                                <option value="">{{ __('Select Payment Status') }}</option>
                                @foreach($paymentStatuses as $value => $label)
                                    <option value="{{ $value }}" {{ request('payment_status') === $value ? 'selected' : '' }}>{{ __($label) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0 rounded-start-3 ps-3"><i class="bi bi-calendar3"></i></span>
                                <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control border-0 bg-light py-2 fs-14" placeholder="{{ __('From') }}" onchange="this.form.submit()">
                                <span class="input-group-text bg-light border-0">-</span>
                                <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control border-0 bg-light rounded-end-3 py-2 fs-14" placeholder="{{ __('To') }}" onchange="this.form.submit()">
                            </div>
                        </div>
                        <div class="col-12 col-md-3 ms-auto text-md-end">
                            <a href="{{ route('admin.orders.index') }}" class="btn btn-soft-secondary rounded-3">
                                <i class="bi bi-arrow-counterclockwise me-1"></i> {{ __('Reset') }}
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Status Tabs -->
            <div class="p-3 bg-light-subtle overflow-auto">
                <div class="d-flex gap-2 flex-nowrap status-tabs">
                    <a href="{{ route('admin.orders.index', request()->except(['status', 'page'])) }}" 
                       class="btn btn-sm rounded-pill px-3 text-nowrap {{ !request('status') ? 'btn-primary' : 'btn-white border text-muted' }}">
                        {{ __('All') }} <span class="badge {{ !request('status') ? 'bg-white text-primary' : 'bg-light text-dark' }} ms-1">{{ $statusCounts['all'] ?? 0 }}</span>
                    </a>
                    @foreach($statuses as $value => $label)
                        <a href="{{ route('admin.orders.index', array_merge(request()->except('page'), ['status' => $value])) }}" 
                           class="btn btn-sm rounded-pill px-3 text-nowrap {{ request('status') === $value ? 'btn-primary' : 'btn-white border text-muted' }}">
                            {{ __($label) }} 
                            <span class="badge {{ request('status') === $value ? 'bg-white text-primary' : 'bg-light text-dark' }} ms-1">
                                {{ $statusCounts[$value] ?? 0 }}
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Orders Table -->
            <div class="table-responsive">
                <table class="table align-middle mb-0 table-hover">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4" style="width: 60px;">{{ __('SL') }}</th>
                            <th>{{ __('Order ID') }}</th>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Customer') }}</th>
                            <th>{{ __('Amount') }}</th>
                            <th class="text-center">{{ __('Payment') }}</th>
                            <th class="text-center">{{ __('Status') }}</th>
                            <th class="text-end pe-4">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $index => $order)
                            <tr>
                                <td class="ps-4 text-muted fw-medium" data-label="{{ __('SL') }}">
                                    {{ $orders->firstItem() + $index }}
                                </td>
                                <td data-label="{{ __('Order ID') }}">
                                    <div class="d-flex flex-column">
                                        <a href="{{ route('admin.orders.show', $order->id) }}" class="fw-bold text-dark text-decoration-none font-monospace">
                                            {{ $order->order_number }}
                                        </a>
                                        @if($order->invoice_number)
                                            <small class="text-muted">{{ $order->invoice_number }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td data-label="{{ __('Date') }}">
                                    <div class="d-flex flex-column">
                                        <span class="fw-medium text-dark">{{ $order->created_at->format('M d, Y') }}</span>
                                        <small class="text-muted">{{ $order->created_at->format('h:i A') }}</small>
                                    </div>
                                </td>
                                <td data-label="{{ __('Customer') }}">
                                    @if($order->user)
                                        <div class="d-flex flex-column">
                                            <a href="{{ route('admin.customers.show', $order->user_id) }}" class="text-dark text-decoration-none fw-medium">
                                                {{ $order->user->name }}
                                            </a>
                                            <small class="text-muted">{{ $order->user->email }}</small>
                                        </div>
                                    @else
                                        <div class="d-flex flex-column">
                                            <span class="fw-medium text-dark">{{ $order->customer_name }}</span>
                                            <small class="text-muted">{{ $order->customer_email }}</small>
                                        </div>
                                    @endif
                                </td>
                                <td data-label="{{ __('Amount') }}">
                                    <span class="fw-bold text-dark">
                                        {{ $order->currency ?? '$' }}{{ number_format($order->total, 2) }}
                                    </span>
                                </td>
                                <td class="text-center" data-label="{{ __('Payment') }}">
                                    @php
                                        $paymentColor = match($order->payment_status) {
                                            'paid' => 'success',
                                            'pending' => 'warning',
                                            'failed' => 'danger',
                                            'refunded' => 'info',
                                            default => 'secondary'
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $paymentColor }}-subtle text-{{ $paymentColor }} border border-{{ $paymentColor }}-subtle rounded-pill px-3 py-1 text-capitalize">
                                        {{ $paymentStatuses[$order->payment_status] ?? __(ucfirst($order->payment_status)) }}
                                    </span>
                                </td>
                                <td class="text-center" data-label="{{ __('Status') }}">
                                    @php
                                        $statusColor = match($order->status) {
                                            'delivered' => 'success',
                                            'shipped' => 'info',
                                            'processing' => 'primary',
                                            'pending' => 'warning',
                                            'cancelled' => 'danger',
                                            default => 'secondary'
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $statusColor }}-subtle text-{{ $statusColor }} border border-{{ $statusColor }}-subtle rounded-pill px-3 py-1 text-capitalize">
                                        {{ $statuses[$order->status] ?? __(ucfirst($order->status)) }}
                                    </span>
                                </td>
                                <td class="text-end pe-4" data-label="{{ __('Actions') }}">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-soft-primary rounded-2" title="{{ __('View Details') }}">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.orders.invoice', $order->id) }}" class="btn btn-sm btn-soft-secondary rounded-2" title="{{ __('Download Invoice') }}" target="_blank">
                                            <i class="bi bi-file-earmark-pdf"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="empty-state">
                                        <div class="mb-3 text-muted opacity-50">
                                            <i class="bi bi-cart-x display-1"></i>
                                        </div>
                                        <h5 class="text-muted">{{ __('No orders found') }}</h5>
                                        <p class="text-muted small">{{ __('Try adjusting your filters or search criteria') }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($orders->hasPages())
                <div class="card-footer bg-white border-top-0 py-3">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .status-tabs::-webkit-scrollbar {
        height: 4px;
    }
    .status-tabs::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    .status-tabs::-webkit-scrollbar-thumb {
        background: #ddd;
        border-radius: 4px;
    }
    
    .btn-soft-primary {
        color: #0d6efd;
        background-color: rgba(13, 110, 253, 0.1);
        border: none;
    }
    .btn-soft-primary:hover {
        color: #fff;
        background-color: #0d6efd;
    }

    .btn-soft-secondary {
        color: #6c757d;
        background-color: rgba(108, 117, 125, 0.1);
        border: none;
    }
    .btn-soft-secondary:hover {
        color: #fff;
        background-color: #6c757d;
    }

    .btn-white {
        background-color: #fff;
    }
</style>
@endpush
