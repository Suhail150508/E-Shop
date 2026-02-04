@extends('layouts.customer')

@section('title', __('Dashboard'))

@section('account_content')
    <div class="mb-4">
        <h4 class="fw-bold mb-1">{{ __('My Dashboard') }}</h4>
        <p class="text-muted">{{ __('Hello, :name. Welcome to your personalized My Account Dashboard.', ['name' => Auth::user()->name]) }}</p>
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
                        <div class="text-muted small">{{ __('Total Orders') }}</div>
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
                        <div class="text-muted small">{{ __('My Wishlist') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3 dashboard-stat-icon-gold">
                        <i class="fa-solid fa-wallet fs-5"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0 text-gold">${{ number_format($walletBalance ?? 0, 0) }}</h4>
                        <div class="text-muted small">{{ __('My Wallet') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3 dashboard-stat-icon-terracotta">
                        <i class="fa-solid fa-ticket fs-5"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0 text-terracotta">{{ $supportTicketCount ?? 0 }}</h4>

                        <div class="text-muted small">{{ __('Support Ticket') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white border-0 py-3 px-4">
            <h5 class="fw-bold mb-0">{{ __("Recent Order's List") }}</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted">
                    <tr>
                        <th scope="col" class="ps-4 py-3 fw-semibold small">{{ __('Order ID') }}</th>
                        <th scope="col" class="py-3 fw-semibold small">{{ __('Order Date') }}</th>
                        <th scope="col" class="py-3 fw-semibold small">{{ __('Order Amount') }}</th>
                        <th scope="col" class="py-3 fw-semibold small">{{ __('Payment Status') }}</th>
                        <th scope="col" class="py-3 fw-semibold small">{{ __('Status') }}</th>
                        <th scope="col" class="text-end pe-4 py-3 fw-semibold small">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentOrders ?? [] as $order)
                        <tr>
                            <td class="ps-4 fw-bold">#{{ $order->order_number }}</td>
                            <td class="text-muted">{{ $order->created_at->format('d F Y h:i A') }}</td>
                            <td class="fw-bold">${{ number_format($order->total, 2) }}</td>
                            <td>
                                @if($order->payment_status == 'paid')
                                    <span class="badge rounded-pill bg-success bg-opacity-10 text-success px-3 py-2">{{ __('Paid') }}</span>
                                @else
                                    <span class="badge rounded-pill bg-warning bg-opacity-10 text-warning px-3 py-2">{{ __(ucfirst($order->payment_status)) }}</span>
                                @endif
                            </td>
                            <td>
                                @if($order->status == 'delivered')
                                    <span class="badge rounded-pill bg-success bg-opacity-10 text-success px-3 py-2">{{ __('Delivered') }}</span>
                                @elseif($order->status == 'cancelled')
                                    <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger px-3 py-2">{{ __('Cancelled') }}</span>
                                @else
                                    <span class="badge rounded-pill bg-warning bg-opacity-10 text-warning px-3 py-2">{{ ucfirst($order->status) }}</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('customer.orders.show', $order->id) }}" class="btn btn-sm btn-light rounded-circle shadow-sm action-btn-circle">
                                    <i class="fa-solid fa-chevron-right text-muted"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="mb-3 text-muted">
                                    <i class="fa-solid fa-box-open fa-3x opacity-25"></i>
                                </div>
                                <p class="text-muted mb-0">{{ __('No recent orders found.') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection