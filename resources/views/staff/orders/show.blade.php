@extends('layouts.staff')

@section('page_title', __('Order Details'))

@section('content')
<div class="row">
    <div class="col-lg-8">
        <!-- Order Items -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">{{ __('Order Items') }}</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th>{{ __('Product') }}</th>
                                <th>{{ __('Price') }}</th>
                                <th>{{ __('Quantity') }}</th>
                                <th class="text-end">{{ __('Total') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($item->product)
                                                <img src="{{ $item->product->image_url }}" alt="{{ $item->product_name }}" class="rounded me-3 object-fit-cover" width="50" height="50">
                                            @else
                                                <div class="rounded me-3 bg-light d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="fw-medium">{{ $item->product_name }}</div>
                                                @if($item->product_sku)
                                                    <div class="small text-muted">{{ __('SKU:') }} {{ $item->product_sku }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ number_format($item->unit_price, 2) }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td class="text-end fw-bold">{{ number_format($item->total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="border-top">
                            <tr>
                                <td colspan="3" class="text-end">{{ __('Subtotal') }}</td>
                                <td class="text-end">{{ number_format($order->subtotal, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-end">{{ __('Tax') }}</td>
                                <td class="text-end">{{ number_format($order->tax_total, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-end">{{ __('Shipping') }}</td>
                                <td class="text-end">{{ number_format($order->shipping_total, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-end">{{ __('Discount') }}</td>
                                <td class="text-end text-danger">-{{ number_format($order->discount_total, 2) }}</td>
                            </tr>
                            <tr class="fw-bold fs-5">
                                <td colspan="3" class="text-end">{{ __('Total') }}</td>
                                <td class="text-end">{{ number_format($order->total, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Order Actions -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">{{ __('Order Status') }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('staff.orders.update', $order->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label">{{ __('Current Status') }}</label>
                        <select name="status" class="form-select form-select-lg">
                            <option value="{{ \App\Models\Order::STATUS_PENDING }}" {{ $order->status == \App\Models\Order::STATUS_PENDING ? 'selected' : '' }}>{{ __('Pending') }}</option>
                            <option value="{{ \App\Models\Order::STATUS_PROCESSING }}" {{ $order->status == \App\Models\Order::STATUS_PROCESSING ? 'selected' : '' }}>{{ __('Processing') }}</option>
                            <option value="{{ \App\Models\Order::STATUS_SHIPPED }}" {{ $order->status == \App\Models\Order::STATUS_SHIPPED ? 'selected' : '' }}>{{ __('Shipped') }}</option>
                            <option value="{{ \App\Models\Order::STATUS_DELIVERED }}" {{ $order->status == \App\Models\Order::STATUS_DELIVERED ? 'selected' : '' }}>{{ __('Delivered') }}</option>
                            <option value="{{ \App\Models\Order::STATUS_CANCELLED }}" {{ $order->status == \App\Models\Order::STATUS_CANCELLED ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        {{ __('Update Status') }}
                    </button>
                </form>
            </div>
        </div>

        <!-- Customer Info -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">{{ __('Customer Details') }}</h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-4">
                    <div class="avatar-circle bg-primary text-white d-flex align-items-center justify-content-center rounded-circle me-3" style="width: 48px; height: 48px; font-size: 1.2rem;">
                        {{ substr($order->customer_name ?? 'Guest', 0, 1) }}
                    </div>
                    <div>
                        <div class="fw-bold">{{ $order->customer_name ?? 'Guest' }}</div>
                        <div class="text-muted small">{{ $order->customer_email }}</div>
                        <div class="text-muted small">{{ $order->customer_phone }}</div>
                    </div>
                </div>

                <div class="mb-3">
                    <h6 class="text-muted text-uppercase small fw-bold mb-2">{{ __('Shipping Address') }}</h6>
                    <p class="mb-0 small text-secondary bg-light p-3 rounded">
                        {{ $order->shipping_address }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
