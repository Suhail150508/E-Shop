@extends('layouts.customer')

@section('title', __('Order Details'))

@push('styles')
    @if($order->shipping_latitude && $order->shipping_longitude)
    <link rel="stylesheet" href="{{ asset('global/leaflet/leaflet.css') }}">
    @endif
@endpush

@section('account_content')
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h4 class="fw-bold mb-1">{{ __('Order') }} #{{ $order->order_number }}</h4>
            <div class="text-muted small">
                <i class="fa-regular fa-calendar me-1"></i> {{ $order->created_at?->format('M d, Y h:i A') }}
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            @if($order->refunds->count() > 0)
                <div class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-3 py-2 d-flex align-items-center">
                    <i class="fa-solid fa-circle-info me-2"></i>
                    {{ __('Refund:') }} <span class="ms-1 fw-bold text-capitalize">{{ __('common.' . $order->refunds->first()->status) }}</span>
                </div>
            @elseif($order->status === 'delivered')
                <button type="button" class="btn btn-outline-danger rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#refundModal">
                    <i class="fa-solid fa-arrow-rotate-left me-2"></i> {{ __('Request Refund') }}
                </button>
            @elseif($order->status === 'pending')
                <button type="button" class="btn btn-outline-danger rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#cancelOrderModal">
                    <i class="fa-solid fa-ban me-2"></i> {{ __('Cancel Order') }}
                </button>
            @endif

            <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm text-white" data-bs-toggle="modal" data-bs-target="#trackOrderModal">
                <i class="fa-solid fa-truck-fast me-2"></i> {{ __('Track Order') }}
            </button>
            @if(Route::has('customer.orders.invoice'))
                <a href="{{ route('customer.orders.invoice', $order) }}" class="btn btn-outline-primary rounded-pill px-4 shadow-sm">
                    <i class="fa-solid fa-file-invoice me-2"></i> {{ __('Invoice') }}
                </a>
            @endif
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white border-bottom-0 py-3 px-4">
                    <h5 class="mb-0 fw-bold">{{ __('Order Items') }}</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 border-0 text-muted small text-uppercase fw-semibold">{{ __('Product') }}</th>
                                    <th class="text-center border-0 text-muted small text-uppercase fw-semibold">{{ __('Qty') }}</th>
                                    <th class="text-end border-0 text-muted small text-uppercase fw-semibold">{{ __('Price') }}</th>
                                    <th class="text-end pe-4 border-0 text-muted small text-uppercase fw-semibold">{{ __('Total') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0 me-3">
                                                    @if($item->product && $item->product->image)
                                                        <img src="{{ getImageOrPlaceholder($item->product->image, '100x100') }}" alt="{{ $item->product_name }}" class="rounded product-thumb-50" onerror="this.src='{{ asset('backend/images/placeholder.svg') }}'">
                                                    @else
                                                        <div class="bg-light rounded d-flex align-items-center justify-content-center text-muted product-thumb-placeholder-50">
                                                            <i class="fa-solid fa-image"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 text-dark fw-semibold">{{ $item->product_name }}</h6>
                                                    @if($item->product_sku)
                                                        <small class="text-muted">{{ __('SKU') }}: {{ $item->product_sku }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">{{ $item->quantity }}</td>
                                        <td class="text-end">
                                            {{ $order->formatPrice($item->unit_price) }}
                                            @if($item->discount > 0)
                                                <div class="text-danger small">-{{ $order->formatPrice($item->discount) }}</div>
                                            @endif
                                        </td>
                                        <td class="text-end pe-4 fw-semibold">{{ $order->formatPrice($item->total) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">{{ __('Order Timeline') }}</h5>
                    <div class="position-relative ps-4 border-start border-2">
                        <div class="mb-4 position-relative">
                            <div class="position-absolute top-0 start-0 translate-middle bg-primary rounded-circle timeline-dot"></div>
                            <h6 class="fw-bold mb-1">{{ __('Order Placed') }}</h6>
                            <p class="text-muted small mb-0">{{ $order->created_at?->format('M d, Y h:i A') }}</p>
                        </div>
                        @if($order->status === 'processing' || $order->status === 'shipped' || $order->status === 'delivered')
                            <div class="mb-4 position-relative">
                                <div class="position-absolute top-0 start-0 translate-middle bg-{{ $order->status_color }} rounded-circle" style="width: 12px; height: 12px; left: -1px !important;"></div>
                                <h6 class="fw-bold mb-1 text-capitalize">{{ __('common.' . $order->status) }}</h6>
                                <p class="text-muted small mb-0">{{ __('Current Status') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">{{ __('Order Summary') }}</h5>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">{{ __('Subtotal') }}</span>
                        <span class="fw-semibold">{{ $order->formatPrice($order->subtotal) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">{{ __('Shipping') }}</span>
                        <span class="fw-semibold">{{ $order->formatPrice($order->shipping_total) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">{{ __('Tax') }}</span>
                        <span class="fw-semibold">{{ $order->formatPrice($order->tax_total) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">{{ __('Discount') }}</span>
                        <span class="text-danger fw-semibold">-{{ $order->formatPrice($order->discount_total) }}</span>
                    </div>
                    <div class="border-top pt-3 d-flex justify-content-between align-items-center">
                        <span class="fw-bold fs-5">{{ __('Total') }}</span>
                        <span class="fw-bold fs-5 text-primary">{{ $order->formatPrice($order->total) }}</span>
                    </div>
                    
                    <div class="mt-4 pt-4 border-top">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted">{{ __('Payment Status') }}</span>
                            <span class="badge rounded-pill bg-{{ $order->payment_status_color }} bg-opacity-10 text-{{ $order->payment_status_color }} px-3">
                                {{ __('common.' . $order->payment_status) }}
                            </span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="text-muted">{{ __('Payment Method') }}</span>
                            <span class="fw-semibold">{{ $order->payment_method ?: __('common.na') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">{{ __('Shipping Details') }}</h5>
                    <div class="mb-4">
                        <div class="text-muted small text-uppercase fw-semibold mb-2">{{ __('Shipping Address') }}</div>
                        <p class="mb-0 text-dark">
                            {{ $order->shipping_address ?: __('common.na') }}
                        </p>
                    </div>

                    @if($order->shipping_latitude && $order->shipping_longitude)
                        <div class="mb-4">
                            <div class="text-muted small text-uppercase fw-semibold mb-2">{{ __('Delivery Location') }}</div>
                            <div id="tracking-map" class="rounded border" style="height: 200px; width: 100%;"></div>
                            <a href="https://www.openstreetmap.org/?mlat={{ $order->shipping_latitude }}&mlon={{ $order->shipping_longitude }}&zoom=16" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary mt-2">
                                <i class="fas fa-external-link-alt me-1"></i> {{ __('common.view_on_map') }}
                            </a>
                        </div>
                    @endif

                    <div>
                        <div class="text-muted small text-uppercase fw-semibold mb-2">{{ __('Contact Info') }}</div>
                        <p class="mb-1"><i class="fa-regular fa-user me-2 text-muted"></i> {{ $order->customer_name ?? $order->user?->name }}</p>
                        <p class="mb-1"><i class="fa-regular fa-envelope me-2 text-muted"></i> {{ $order->customer_email ?? $order->user?->email }}</p>
                        @if($order->customer_phone)
                            <p class="mb-0"><i class="fa-solid fa-phone me-2 text-muted"></i> {{ $order->customer_phone }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Track Order Modal -->
    <div class="modal fade" id="trackOrderModal" tabindex="-1" aria-labelledby="trackOrderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header border-bottom py-3 px-4">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 p-2 rounded-circle me-3 text-primary">
                            <i class="fa-solid fa-truck-fast fa-lg"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold" id="trackOrderModalLabel">{{ __('Track Order Status') }}</h5>
                            <p class="text-muted small mb-0">{{ __('Real-time updates for your order') }}</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <!-- Order Info Bar -->
                    <div class="bg-light py-3 px-4 border-bottom">
                        <div class="row align-items-center g-3">
                            <div class="col-md-3 col-6">
                                <small class="text-muted text-uppercase fw-bold" style="font-size: 10px;">{{ __('Order ID') }}</small>
                                <h6 class="mb-0 fw-bold">#{{ $order->order_number }}</h6>
                            </div>
                            <div class="col-md-3 col-6">
                                <small class="text-muted text-uppercase fw-bold" style="font-size: 10px;">{{ __('Placed On') }}</small>
                                <h6 class="mb-0 fw-bold">{{ $order->created_at?->format('M d, Y') }}</h6>
                            </div>
                            <div class="col-md-3 col-6">
                                <small class="text-muted text-uppercase fw-bold" style="font-size: 10px;">{{ __('Total Amount') }}</small>
                                <h6 class="mb-0 fw-bold text-primary">{{ $order->formatPrice($order->total) }}</h6>
                            </div>
                            <div class="col-md-3 col-6 text-md-end">
                                <button class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="window.location.reload()">
                                    <i class="fa-solid fa-rotate-right me-1"></i> {{ __('Refresh') }}
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Stepper Section -->
                    <div class="py-5 px-3 px-md-5">
                        <div class="stepper-wrapper">
                            @php
                                $status = $order->status;
                                $steps = [
                                    'pending' => ['label' => __('common.pending'), 'icon' => 'fa-clipboard-list'],
                                    'confirmed' => ['label' => __('common.confirmed'), 'icon' => 'fa-check-circle'],
                                    'processing' => ['label' => __('common.processing'), 'icon' => 'fa-boxes-packing'],
                                    'pickup' => ['label' => __('common.pickup'), 'icon' => 'fa-truck-pickup'],
                                    'shipped' => ['label' => __('common.shipped'), 'icon' => 'fa-truck-fast'],
                                    'delivered' => ['label' => __('common.delivered'), 'icon' => 'fa-box-open']
                                ];
                                
                                // Logic to determine current step index based on order status
                                // 0: Pending, 1: Confirmed, 2: Processing, 3: Pick-Up, 4: Shipped, 5: Delivered
                                
                                $activeStep = 0;
                                if($status == 'pending') $activeStep = 0;
                                if($status == 'processing') $activeStep = 2; // Skip confirmed (1) -> auto completed
                                if($status == 'shipped') $activeStep = 4;    // Skip pick-up (3) -> auto completed
                                if($status == 'delivered') $activeStep = 6;  // All completed
                            @endphp

                            @foreach($steps as $key => $step)
                                @php
                                    $stepClass = '';
                                    $index = array_search($key, array_keys($steps));
                                    
                                    if ($status == 'cancelled') {
                                        $stepClass = 'cancelled';
                                    } elseif ($index < $activeStep) {
                                        $stepClass = 'completed';
                                    } elseif ($index == $activeStep) {
                                        $stepClass = 'active';
                                    }
                                @endphp
                                <div class="stepper-item {{ $stepClass }}">
                                    <div class="step-counter">
                                        @if($index < $activeStep)
                                            <i class="fa-solid fa-check"></i>
                                        @elseif($index == $activeStep && $status != 'cancelled')
                                            <i class="fa-solid {{ $step['icon'] }}"></i>
                                        @else
                                            <i class="fa-solid {{ $step['icon'] }}"></i>
                                        @endif
                                    </div>
                                    <div class="step-name">{{ $step['label'] }}</div>
                                    @if($index == $activeStep && $status != 'cancelled')
                                        <div class="text-primary fw-bold small mt-1 animate__animated animate__fadeIn" style="font-size: 11px;">
                                            {{ $order->updated_at?->format('h:i A') }}
                                            <div class="badge bg-primary bg-opacity-10 text-primary mt-1 px-2">{{ __('Current') }}</div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        
                        @if($status == 'cancelled')
                            <div class="alert alert-danger mt-5 text-center border-0 shadow-sm rounded-3">
                                <div class="d-flex align-items-center justify-content-center">
                                    <i class="fa-solid fa-circle-xmark fa-2x me-3"></i> 
                                    <div class="text-start">
                                        <h6 class="fw-bold mb-0">{{ __('Order Cancelled') }}</h6>
                                        <p class="mb-0 small">{{ __('This order has been cancelled and will not be processed further.') }}</p>
                                    </div>
                                </div>
                            </div>
                        @else
                            <!-- Estimated Delivery or Message -->
                            <div class="mt-5 text-center">
                                @if($status == 'delivered')
                                    <div class="d-inline-block bg-success bg-opacity-10 text-success px-4 py-3 rounded-3">
                                        <i class="fa-solid fa-gift me-2"></i> {{ __('Your order has been delivered successfully. Thank you for shopping with us!') }}
                                    </div>
                                @else
                                    <p class="text-muted mb-0">
                                        <i class="fa-solid fa-info-circle me-1"></i> {{ __('Status updates may take a few hours to reflect.') }}
                                    </p>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
                <div class="modal-footer border-top justify-content-center py-3 bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill px-5 fw-medium" data-bs-dismiss="modal">{{ __('Close') }}</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Refund Request Modal -->
    <div class="modal fade" id="refundModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold">{{ __('Request Refund') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('customer.orders.refund.store', $order->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-light border-0 mb-4 rounded-3 text-muted small">
                            <i class="fa-solid fa-circle-info me-2 text-primary"></i>
                            {{ __('Please provide a clear reason and evidence for your refund request. Our team will review it shortly.') }}
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">{{ __('Reason') }} <span class="text-danger">*</span></label>
                            <select name="reason" class="form-select rounded-3 bg-light border-0" required>
                                <option value="">{{ __('Select a reason') }}</option>
                                @foreach($refundReasons as $reason)
                                    <option value="{{ $reason->reason }}">{{ $reason->reason }}</option>
                                @endforeach
                                <option value="Other">{{ __('Other') }}</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">{{ __('Additional Details') }}</label>
                            <textarea name="details" class="form-control rounded-3 bg-light border-0" rows="3" placeholder="{{ __('Describe the issue in detail...') }}"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">{{ __('Attachments (Optional)') }}</label>
                            <input type="file" name="images[]" class="form-control rounded-3 bg-light border-0" multiple accept="image/*">
                            <div class="form-text text-muted small">{{ __('You can upload multiple images (JPG, PNG). Max 2MB each.') }}</div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 text-white">{{ __('Submit Request') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Cancel Order Modal -->
    <div class="modal fade" id="cancelOrderModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold text-danger">{{ __('Cancel Order') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('customer.orders.cancel', $order->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="text-center py-4">
                            <i class="fa-solid fa-circle-exclamation text-danger fa-3x mb-3"></i>
                            <h5 class="fw-bold mb-2">{{ __('Are you sure?') }}</h5>
                            <p class="text-muted mb-0">{{ __('Do you really want to cancel this order? This action cannot be undone.') }}</p>
                            @if($order->payment_status === 'paid')
                                <div class="alert alert-info mt-3 mb-0 text-start small">
                                    <i class="fa-solid fa-wallet me-2"></i> {{ __('The paid amount will be refunded to your wallet immediately.') }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0 justify-content-center">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">{{ __('Keep Order') }}</button>
                        <button type="submit" class="btn btn-danger rounded-pill px-4">{{ __('Yes, Cancel Order') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @if($order->shipping_latitude && $order->shipping_longitude)
    <script src="{{ asset('global/leaflet/leaflet.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var el = document.getElementById('tracking-map');
            if (el && typeof L !== 'undefined') {
                var map = L.map(el).setView([{{ $order->shipping_latitude }}, {{ $order->shipping_longitude }}], 15);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OpenStreetMap' }).addTo(map);
                L.marker([{{ $order->shipping_latitude }}, {{ $order->shipping_longitude }}]).addTo(map);
            }
        });
    </script>
    @endif
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('trigger_refund')) {
            const refundModalEl = document.getElementById('refundModal');
            if (refundModalEl) {
                const refundModal = new bootstrap.Modal(refundModalEl);
                refundModal.show();
                
                // Remove the query parameter from URL without reloading
                const newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
                window.history.replaceState({path: newUrl}, '', newUrl);
            }
        }
    });
</script>
@endpush
