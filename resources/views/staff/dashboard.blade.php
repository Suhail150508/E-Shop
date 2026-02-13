@extends('layouts.staff')

@section('page_title', __('Dashboard'))

@section('content')
<div class="row g-4 mb-4">
    <!-- Total Assigned Orders -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="flex-shrink-0">
                        <span class="d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-3" style="width: 48px; height: 48px;">
                            <i class="fas fa-shopping-bag fa-lg"></i>
                        </span>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-0">{{ __('Total Assigned') }}</h6>
                    </div>
                </div>
                <h3 class="fw-bold mb-0">{{ $totalOrders }}</h3>
            </div>
        </div>
    </div>

    <!-- Pending Orders -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="flex-shrink-0">
                        <span class="d-flex align-items-center justify-content-center bg-warning bg-opacity-10 text-warning rounded-3" style="width: 48px; height: 48px;">
                            <i class="fas fa-clock fa-lg"></i>
                        </span>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-0">{{ __('Pending') }}</h6>
                    </div>
                </div>
                <h3 class="fw-bold mb-0">{{ $pendingOrders }}</h3>
            </div>
        </div>
    </div>

    <!-- Processing Orders -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="flex-shrink-0">
                        <span class="d-flex align-items-center justify-content-center bg-info bg-opacity-10 text-info rounded-3" style="width: 48px; height: 48px;">
                            <i class="fas fa-cog fa-spin fa-lg"></i>
                        </span>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-0">{{ __('Processing') }}</h6>
                    </div>
                </div>
                <h3 class="fw-bold mb-0">{{ $processingOrders }}</h3>
            </div>
        </div>
    </div>

    <!-- Completed Orders -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="flex-shrink-0">
                        <span class="d-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success rounded-3" style="width: 48px; height: 48px;">
                            <i class="fas fa-check-circle fa-lg"></i>
                        </span>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-0">{{ __('Completed') }}</h6>
                    </div>
                </div>
                <h3 class="fw-bold mb-0">{{ $completedOrders }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- Performance Chart -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold">{{ __('Monthly Performance') }}</h5>
    </div>
    <div class="card-body">
        <div id="performanceChart"></div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold">{{ __('Recent Assigned Orders') }}</h5>
        <a href="{{ route('staff.orders.index') }}" class="btn btn-sm btn-secondary-soft">{{ __('View All') }}</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">{{ __('Order ID') }}</th>
                        <th>{{ __('Customer') }}</th>
                        <th>{{ __('Total') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Date') }}</th>
                        <th class="text-end pe-4">{{ __('Action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentOrders as $order)
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
                                @php
                                    $statusClass = match($order->status) {
                                        'completed' => 'success',
                                        'pending' => 'warning',
                                        'processing' => 'info',
                                        'cancelled' => 'danger',
                                        'refunded' => 'danger',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge bg-{{ $statusClass }}-subtle text-{{ $statusClass }} border border-{{ $statusClass }}-subtle">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td class="text-muted small">{{ $order->created_at->format('M d, Y') }}</td>
                            <td class="text-end">
                                        <a href="{{ route('staff.orders.show', $order->id) }}" class="btn btn-sm btn-secondary-soft">
                                            {{ __('Details') }}
                                        </a>
                                    </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-clipboard-list fa-2x mb-3 opacity-50"></i>
                                <p class="mb-0">{{ __('No orders assigned yet.') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var options = {
            series: [{
                name: 'Completed Orders',
                data: @json($chartData)
            }],
            chart: {
                height: 350,
                type: 'bar',
                toolbar: { show: false },
                fontFamily: 'Inter, sans-serif'
            },
            colors: ['#4f46e5'],
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    columnWidth: '45%',
                }
            },
            dataLabels: { enabled: false },
            xaxis: {
                categories: @json($allMonths),
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                title: { text: 'Orders' }
            },
            grid: {
                borderColor: '#f1f5f9',
                strokeDashArray: 4,
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return val + " Orders"
                    }
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("#performanceChart"), options);
        chart.render();
    });
</script>
@endpush
