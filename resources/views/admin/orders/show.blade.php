@extends('layouts.admin')

@section('page_title', __('Order').' #'.$order->order_number)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h2 class="h5 mb-1">{{ __('Order') }} #{{ $order->order_number }}</h2>
        <div class="text-muted small">{{ $order->created_at?->format('Y-m-d H:i') }}</div>
    </div>
    <div class="d-flex align-items-center gap-2">
        <span class="badge bg-secondary text-capitalize">
            {{ $statuses[$order->status] ?? __(ucfirst($order->status)) }}
        </span>
        <span class="badge bg-light text-dark text-capitalize border">
            {{ __(ucfirst($order->payment_status)) }}
        </span>
        <a href="{{ route('admin.orders.invoice', $order) }}" class="btn btn-sm btn-secondary-soft">
            {{ __('Download invoice') }}
        </a>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h3 class="h6 mb-3">{{ __('Customer') }}</h3>
                <div class="mb-2">
                    <div class="fw-semibold">
                        {{ $order->user?->name ?? $order->customer_name }}
                    </div>
                    <div class="text-muted small">
                        {{ $order->user?->email ?? $order->customer_email }}
                    </div>
                    @if($order->customer_phone)
                        <div class="text-muted small">{{ $order->customer_phone }}</div>
                    @endif
                </div>
                <div class="mb-2">
                    <div class="text-muted small">{{ __('Billing address') }}</div>
                    <div>{{ $order->billing_address ?: __('common.na') }}</div>
                </div>
                <div>
                    <div class="text-muted small">{{ __('Shipping address') }}</div>
                    <div>{{ $order->shipping_address ?: __('common.na') }}</div>

                    @if($order->shipping_latitude && $order->shipping_longitude)
                        <div class="mt-3">
                            <div class="text-muted small mb-1">{{ __('Delivery Location') }}</div>
                            <div id="admin-order-map" class="rounded border" style="height: 200px; width: 100%;"></div>
                            <a href="https://www.google.com/maps/search/?api=1&query={{ $order->shipping_latitude }},{{ $order->shipping_longitude }}" target="_blank" rel="noopener" class="btn btn-sm btn-secondary-soft mt-2 w-100">
                                <i class="fas fa-external-link-alt me-1"></i> {{ __('Open in Google Maps') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h3 class="h6 mb-3">{{ __('Payment') }}</h3>
                <div class="mb-2">
                    <div class="text-muted small">{{ __('Method') }}</div>
                    <div>{{ $order->payment_method ?: __('common.na') }}</div>
                </div>
                <div class="mb-2">
                    <div class="text-muted small">{{ __('Subtotal') }}</div>
                    <div>{{ $order->formatPrice($order->subtotal) }}</div>
                </div>
                <div class="mb-2 d-flex justify-content-between">
                    <span class="text-muted small">{{ __('Discount') }}</span>
                    <span>{{ $order->formatPrice($order->discount_total) }}</span>
                </div>
                <div class="mb-2 d-flex justify-content-between">
                    <span class="text-muted small">{{ __('Shipping') }}</span>
                    <span>{{ $order->formatPrice($order->shipping_total) }}</span>
                </div>
                <div class="mb-2 d-flex justify-content-between">
                    <span class="text-muted small">{{ __('Tax') }}</span>
                    <span>{{ $order->formatPrice($order->tax_total) }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <span class="text-muted small">{{ __('Total') }}</span>
                    <span class="fw-bold fs-5">
                        {{ $order->formatPrice($order->total) }}
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h3 class="h6 mb-3">{{ __('Status and assignment') }}</h3>
                <form action="{{ route('admin.orders.update', $order) }}" method="POST" class="mb-3">
                    @csrf
                    @method('PUT')
                    <div class="mb-2">
                        <label class="form-label small text-muted" for="status">{{ __('Status') }}</label>
                        <select name="status" id="status" class="form-select form-select-sm">
                            @foreach($statuses as $value => $label)
                                <option value="{{ $value }}" @selected($order->status === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small text-muted" for="staff_id">{{ __('Assign staff') }}</label>
                        <select name="staff_id" id="staff_id" class="form-select form-select-sm">
                            <option value="">{{ __('Unassigned') }}</option>
                            @foreach($staffUsers as $staff)
                                <option value="{{ $staff->id }}" @selected($order->staff_id === $staff->id)>{{ $staff->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary btn-sm">
                            {{ __('Update order') }}
                        </button>
                    </div>
                </form>
                <div>
                    <h4 class="h6 mb-2">{{ __('Status history') }}</h4>
                    <div class="small text-muted">
                        @forelse($order->statusHistories->sortByDesc('created_at') as $history)
                            <div class="mb-1">
                                <span class="text-capitalize">{{ __(ucfirst($history->status)) }}</span>
                                <span>·</span>
                                <span>{{ $history->created_at?->format('Y-m-d H:i') }}</span>
                                @if($history->changedBy)
                                    <span>· {{ $history->changedBy->name }}</span>
                                @endif
                            </div>
                        @empty
                            <div>{{ __('No history yet.') }}</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>{{ __('Product') }}</th>
                    <th class="text-center">{{ __('Quantity') }}</th>
                    <th class="text-end">{{ __('Unit price') }}</th>
                    <th class="text-end">{{ __('Discount') }}</th>
                    <th class="text-end">{{ __('Total') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($order->items as $item)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $item->product_name }}</div>
                            @if($item->product_sku)
                                <div class="text-muted small">{{ __('SKU') }}: {{ $item->product_sku }}</div>
                            @endif
                        </td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-end">{{ $order->formatPrice($item->unit_price) }}</td>
                        <td class="text-end">{{ $order->formatPrice($item->discount) }}</td>
                        <td class="text-end">{{ $order->formatPrice($item->total) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            {{ __('No items found.') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@if($order->shipping_latitude && $order->shipping_longitude)
@push('styles')
    <link rel="stylesheet" href="{{ asset('global/leaflet/leaflet.css') }}">
@endpush
@push('scripts')
    <script src="{{ asset('global/leaflet/leaflet.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var el = document.getElementById('admin-order-map');
            if (el && typeof L !== 'undefined') {
                var map = L.map(el).setView([{{ $order->shipping_latitude }}, {{ $order->shipping_longitude }}], 15);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OpenStreetMap' }).addTo(map);
                L.marker([{{ $order->shipping_latitude }}, {{ $order->shipping_longitude }}]).addTo(map);
            }
        });
    </script>
@endpush
@endif