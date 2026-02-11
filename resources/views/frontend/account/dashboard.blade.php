@extends('layouts.customer')

@section('title', __('common.dashboard'))

@section('account_content')
    <div class="mb-4">
        <h4 class="fw-bold mb-1">{{ __('common.my_dashboard') }}</h4>
        <p class="text-muted">{{ __('common.dashboard_welcome', ['name' => Auth::user()->name]) }}</p>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-5">
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3 dashboard-stat-icon">
                                <i class="fa-solid fa-cart-shopping fs-5"></i>
                            </div>
                            <div>
                                <h4 class="fw-bold mb-0 text-rust">{{ $totalOrders ?? 0 }}</h4>
                        <div class="text-muted small">{{ __('common.total_orders') }}</div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3 dashboard-stat-icon-sage">
                        <i class="fa-solid fa-heart fs-5"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0 text-sage">{{ $wishlistCount ?? 0 }}</h4>
                        <div class="text-muted small">{{ __('common.my_wishlist') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <a href="{{ route('customer.wallet.index') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-3 h-100 card-hover-effect">
                    <div class="card-body p-3 d-flex align-items-center">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3 dashboard-stat-icon-gold">
                            <i class="fa-solid fa-wallet fs-5"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-0 text-gold">{{ format_price($walletBalance ?? 0) }}</h4>
                            <div class="text-muted small">{{ __('common.my_wallet') }}</div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3 dashboard-stat-icon-terracotta">
                        <i class="fa-solid fa-ticket fs-5"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0 text-terracotta">{{ $supportTicketCount ?? 0 }}</h4>

                        <div class="text-muted small">{{ __('common.support_ticket') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white border-0 py-3 px-4">
            <h5 class="fw-bold mb-0">{{ __('common.recent_orders_list') }}</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted">
                    <tr>
                        <th scope="col" class="ps-4 py-3 fw-semibold small">{{ __('common.order_id') }}</th>
                        <th scope="col" class="py-3 fw-semibold small">{{ __('common.order_date') }}</th>
                        <th scope="col" class="py-3 fw-semibold small">{{ __('common.order_amount') }}</th>
                        <th scope="col" class="py-3 fw-semibold small">{{ __('common.payment_status') }}</th>
                        <th scope="col" class="py-3 fw-semibold small">{{ __('common.status') }}</th>
                        <th scope="col" class="text-end pe-4 py-3 fw-semibold small">{{ __('common.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentOrders ?? [] as $order)
                        <tr>
                            <td class="ps-4 fw-bold">#{{ $order->order_number }}</td>
                            <td class="text-muted">{{ $order->created_at->format('d F Y h:i A') }}</td>
                            <td class="fw-bold">{{ format_price($order->total) }}</td>
                            <td>
                                @if($order->payment_status == 'paid')
                                    <span class="badge rounded-pill bg-success bg-opacity-10 text-success px-3 py-2">{{ __('common.paid') }}</span>
                                @else
                                    <span class="badge rounded-pill bg-warning bg-opacity-10 text-warning px-3 py-2">{{ __('common.' . $order->payment_status) }}</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $statusClass = match($order->status) {
                                        'pending' => 'warning',
                                        'confirmed' => 'info',
                                        'processing' => 'primary',
                                        'pickup' => 'info',
                                        'shipped' => 'primary',
                                        'delivered' => 'success',
                                        'cancelled' => 'danger',
                                        'refunded' => 'secondary',
                                        'failed' => 'danger',
                                        default => 'primary'
                                    };
                                @endphp
                                <span class="badge rounded-pill bg-{{ $statusClass }} bg-opacity-10 text-{{ $statusClass }} px-3 py-2">{{ __('common.' . $order->status) }}</span>
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('customer.orders.show', $order->id) }}" class="btn btn-sm btn-light rounded-circle shadow-sm" data-bs-toggle="tooltip" title="{{ __('common.view_details') }}">
                                    <i class="fa-solid fa-eye text-primary"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fa-solid fa-box-open fa-3x mb-3 opacity-25"></i>
                                    <p class="mb-0">{{ __('common.no_recent_orders') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
