@extends('layouts.staff')

@section('page_title', __('Assigned Orders'))

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <div class="row g-3 align-items-center">
            <div class="col-md-6">
                <h5 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>{{ __('My Orders') }}</h5>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">{{ __('Order ID') }}</th>
                        <th>{{ __('Customer') }}</th>
                        <th>{{ __('Total') }}</th>
                        <th>{{ __('Payment') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Date') }}</th>
                        <th class="text-end pe-4">{{ __('Action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td class="ps-4 fw-medium">#{{ $order->order_number }}</td>
                            <td>
                                @if($order->user)
                                    {{ $order->user->name }}
                                @else
                                    <span class="text-muted">{{ __('Guest') }}</span>
                                @endif
                            </td>
                            <td>{{ number_format($order->total, 2) }}</td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    {{ ucfirst($order->payment_method) }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $statusClass = match($order->status) {
                                        \App\Models\Order::STATUS_DELIVERED => 'success',
                                        \App\Models\Order::STATUS_PENDING => 'warning',
                                        \App\Models\Order::STATUS_PROCESSING => 'info',
                                        \App\Models\Order::STATUS_SHIPPED => 'primary',
                                        \App\Models\Order::STATUS_CANCELLED => 'danger',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge bg-{{ $statusClass }}-subtle text-{{ $statusClass }} border border-{{ $statusClass }}-subtle">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td class="text-muted small">{{ $order->created_at->format('M d, Y') }}</td>
                            <td class="text-end pe-4">
                                <a href="{{ route('staff.orders.show', $order->id) }}" class="btn btn-sm btn-secondary-soft">
                                    <i class="fas fa-eye me-1"></i> {{ __('View') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-box-open fa-3x mb-3 opacity-50"></i>
                                <p class="mb-0">{{ __('No orders assigned yet.') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($orders->hasPages())
            <div class="card-footer bg-white py-3">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
