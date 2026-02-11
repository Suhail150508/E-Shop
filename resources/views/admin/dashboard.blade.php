@extends('layouts.admin')

@section('page_title', __('Dashboard'))

@section('content')
    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="avatar-lg bg-primary-subtle rounded-3 d-flex align-items-center justify-content-center stats-icon-container">
                            <i class="fas fa-dollar-sign text-primary fs-4"></i>
                        </div>
                    </div>
                    <h5 class="text-muted fw-normal mb-1">{{ __('Total Revenue') }}</h5>
                    <h2 class="fw-bold mb-0">{{ format_price($totalRevenue ?? 0) }}</h2>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="avatar-lg bg-info-subtle rounded-3 d-flex align-items-center justify-content-center stats-icon-container">
                            <i class="fas fa-shopping-bag text-info fs-4"></i>
                        </div>
                    </div>
                    <h5 class="text-muted fw-normal mb-1">{{ __('Total Orders') }}</h5>
                    <h2 class="fw-bold mb-0">{{ number_format($totalOrders ?? 0) }}</h2>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="avatar-lg bg-warning-subtle rounded-3 d-flex align-items-center justify-content-center stats-icon-container">
                            <i class="fas fa-users text-warning fs-4"></i>
                        </div>
                    </div>
                    <h5 class="text-muted fw-normal mb-1">{{ __('Total Customers') }}</h5>
                    <h2 class="fw-bold mb-0">{{ number_format($totalCustomers ?? 0) }}</h2>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="avatar-lg bg-purple-subtle rounded-3 d-flex align-items-center justify-content-center stats-icon-container">
                            <i class="fas fa-box text-purple fs-4"></i>
                        </div>
                    </div>
                    <h5 class="text-muted fw-normal mb-1">{{ __('Total Products') }}</h5>
                    <h2 class="fw-bold mb-0">{{ number_format($totalProducts ?? 0) }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mb-4">
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0 fw-bold">{{ __('Revenue Analytics') }} ({{ date('Y') }})</h5>
                </div>
                <div class="card-body">
                    <div id="revenueChart" class="chart-container-revenue"></div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0 fw-bold">{{ __('Order Status') }}</h5>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <div id="orderStatusChart" class="chart-container-status"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0 fw-bold">{{ __('Recent Orders') }}</h5>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-light text-primary fw-medium">{{ __('View All') }}</a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 border-0">{{ __('Order ID') }}</th>
                        <th class="py-3 border-0">{{ __('Customer') }}</th>
                        <th class="py-3 border-0">{{ __('Date') }}</th>
                        <th class="py-3 border-0">{{ __('Amount') }}</th>
                        <th class="py-3 border-0">{{ __('Status') }}</th>
                        <th class="pe-4 py-3 border-0 text-end">{{ __('Action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentOrders as $order)
                        <tr>
                            <td class="ps-4 fw-medium text-primary">#{{ $order->order_number }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm rounded-circle bg-light d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                        <span class="fw-bold text-primary" style="font-size: 12px;">
                                            {{ substr($order->user?->name ?? $order->customer_name, 0, 2) }}
                                        </span>
                                    </div>
                                    <span>{{ $order->user?->name ?? $order->customer_name }}</span>
                                </div>
                            </td>
                            <td class="text-muted">{{ $order->created_at?->format('M d, Y') }}</td>
                            <td class="fw-bold">{{ format_price($order->total ?? 0) }}</td>
                            <td>
                                <span class="badge bg-{{ $order->status_color }}-subtle text-{{ $order->status_color }} px-3 py-2 rounded-pill text-capitalize">
                                    {{ __(ucfirst($order->status)) }}
                                </span>
                            </td>
                            <td class="pe-4 text-end">
                                <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-white border shadow-sm text-primary hover-scale">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">{{ __('No recent orders found.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Revenue Chart
        var optionsRevenue = {
            series: [{
                name: 'Revenue',
                data: @json($chartRevenue)
            }],
            chart: {
                height: 350,
                type: 'area',
                toolbar: { show: false },
                fontFamily: 'Inter, sans-serif',
                zoom: { enabled: false }
            },
            colors: ['#4f46e5'],
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 2 },
            xaxis: {
                categories: @json($allMonths),
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                labels: {
                    formatter: function (value) { return "{{ \App\Models\Currency::getDefaultSymbol() }}" + value.toFixed(2); }
                }
            },
            grid: {
                borderColor: '#f1f5f9',
                strokeDashArray: 4,
                padding: { top: 0, right: 0, bottom: 0, left: 10 }
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.4,
                    opacityTo: 0.05,
                    stops: [0, 90, 100]
                }
            },
            tooltip: {
                y: {
                    formatter: function (value) { return "{{ \App\Models\Currency::getDefaultSymbol() }}" + value.toFixed(2); }
                }
            }
        };

        var chartRevenue = new ApexCharts(document.querySelector("#revenueChart"), optionsRevenue);
        chartRevenue.render();

        // Order Status Chart
        var statusLabels = @json($chartStatusLabels);
        var statusSeries = @json($chartStatusSeries);

        if (statusSeries.length === 0) {
            statusLabels = ['{{ __("common.no_orders") }}'];
            statusSeries = [1]; // Dummy data for visual
            var colors = ['#e2e8f0']; // Grey for empty
        } else {
            var colors = ['#10b981', '#f59e0b', '#ef4444', '#3b82f6', '#6366f1'];
        }

        var optionsStatus = {
            series: statusSeries,
            labels: statusLabels,
            chart: {
                type: 'donut',
                height: 320,
                fontFamily: 'Inter, sans-serif'
            },
            colors: colors,
            legend: {
                position: 'bottom',
                offsetY: 0
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: '75%',
                        labels: {
                            show: true,
                            name: {
                                show: true,
                                fontSize: '14px',
                                fontFamily: 'Inter, sans-serif',
                                fontWeight: 600,
                                color: undefined,
                                offsetY: -10
                            },
                            value: {
                                show: true,
                                fontSize: '20px',
                                fontFamily: 'Inter, sans-serif',
                                fontWeight: 700,
                                color: undefined,
                                offsetY: 10,
                                formatter: function (val) {
                                    return val;
                                }
                            },
                            total: {
                                show: true,
                                showAlways: true,
                                label: '{{ __("common.total") }}',
                                fontSize: '14px',
                                fontFamily: 'Inter, sans-serif',
                                fontWeight: 600,
                                color: '#64748b',
                                formatter: function (w) {
                                    // If using dummy data, show 0
                                    if (w.config.colors[0] === '#e2e8f0' && w.globals.seriesTotals.length === 1 && w.globals.seriesTotals[0] === 1) {
                                        return "0";
                                    }
                                    return w.globals.seriesTotals.reduce((a, b) => {
                                        return a + b
                                    }, 0)
                                }
                            }
                        }
                    }
                }
            },
            dataLabels: { enabled: false },
            tooltip: {
                enabled: true,
                y: {
                    formatter: function(val) {
                        // If using dummy data, show 0 in tooltip
                        if (statusSeries.length === 1 && statusSeries[0] === 1 && statusLabels[0] === '{{ __("common.no_orders") }}') {
                            return "0";
                        }
                        return val;
                    }
                }
            }
        };

        var chartStatus = new ApexCharts(document.querySelector("#orderStatusChart"), optionsStatus);
        chartStatus.render();
    });
</script>
@endpush