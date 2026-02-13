@extends('layouts.frontend')

@section('title', __('common.shipping_information'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('frontend/css/checkout.css') }}">
    <link rel="stylesheet" href="{{ asset('global/leaflet/leaflet.css') }}">
@endpush

@section('content')
<div class="checkout-page-wrap">
<div class="container py-5">
    @include('frontend.checkout.steps', ['currentStep' => 2])

    <div class="row g-4">
        <div class="col-lg-8">
            <form id="shipping-form" action="{{ route('checkout.shipping.store') }}" method="POST">
                @csrf
                
                <!-- Pickup and Delivery Options -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold">{{ setting('checkout_shipping_title', __('common.pickup_and_delivery_options')) }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="btn-group w-100 mb-3 checkout-delivery-btns" role="group">
                            <input type="radio" class="btn-check" name="delivery_type" id="delivery_home" value="home_delivery" {{ (old('delivery_type', $checkoutState['delivery_type'] ?? 'home_delivery') == 'home_delivery') ? 'checked' : '' }}>
                            <label class="btn btn-outline-checkout py-3" for="delivery_home">
                                <i class="fas fa-home me-2"></i> {{ __('common.home_delivery') }}
                            </label>

                            <input type="radio" class="btn-check" name="delivery_type" id="delivery_pickup" value="pickup" {{ (old('delivery_type', $checkoutState['delivery_type'] ?? '') == 'pickup') ? 'checked' : '' }}>
                            <label class="btn btn-outline-checkout py-3" for="delivery_pickup">
                                <i class="fas fa-store me-2"></i> {{ __('common.pick_up_in_person') }}
                            </label>
                        </div>

                        <!-- Address Section -->
                        <div id="address-section" class="{{ (old('delivery_type', $checkoutState['delivery_type'] ?? 'home_delivery') == 'pickup') ? 'd-none' : '' }}">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="fw-bold mb-0">{{ __('common.delivery_address') }}</h6>
                                <a href="{{ route('customer.addresses.create') }}" class="text-decoration-none small fw-bold">
                                    <i class="fas fa-plus-circle"></i> {{ __('common.add_address') }}
                                </a>
                            </div>

                            @if($addresses->isEmpty())
                                <div class="alert alert-warning">
                                    {{ __('common.no_saved_addresses') }}
                                </div>
                            @else
                                <div class="list-group" id="address-list">
                                    @foreach($addresses as $address)
                                        <label class="list-group-item list-group-item-action d-flex align-items-start gap-3 p-3 border rounded mb-2" 
                                            data-address-id="{{ $address->id }}" 
                                            data-city="{{ strtolower($address->city ?? '') }}" 
                                            data-state="{{ strtolower($address->state ?? '') }}" 
                                            data-line1="{{ strtolower($address->line1 ?? '') }}"
                                            data-line2="{{ strtolower($address->line2 ?? '') }}"
                                            data-lat="{{ $address->latitude ?? '' }}" 
                                            data-lng="{{ $address->longitude ?? '' }}">
                                            <input class="form-check-input flex-shrink-0 mt-1 address-radio" type="radio" name="address_id" value="{{ $address->id }}" {{ (old('address_id', $checkoutState['address_id'] ?? '') == $address->id || ($loop->first && !isset($checkoutState['address_id']))) ? 'checked' : '' }}>
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between">
                                                    <span class="fw-bold">{{ $address->label ?? __('common.address_label') }}</span>
                                                    <a href="{{ route('customer.addresses.edit', $address->id) }}" class="text-muted small"><i class="fas fa-pencil-alt"></i> {{ __('common.edit') }}</a>
                                                </div>
                                                <p class="mb-0 text-muted small">
                                                    {{ $address->line1 }}<br>
                                                    @if($address->line2) {{ $address->line2 }}<br> @endif
                                                    {{ trim(implode(', ', array_filter([$address->city, $address->state, $address->postal_code]))) ?: __('common.na') }}<br>
                                                    {{ $address->country ?? __('common.na') }}<br>
                                                    {{ $address->phone ?? '' }}
                                                </p>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            @endif
                            @error('address_id')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Delivery location map (Leaflet – no API key required) -->
                        <div id="map-section" class="mt-4 mb-3 {{ (old('delivery_type', $checkoutState['delivery_type'] ?? 'home_delivery') == 'pickup') ? 'd-none' : '' }}">
                            <label class="form-label fw-bold mb-2">{{ __('common.pin_delivery_location') }}</label>
                            <p class="text-muted small mb-2">{{ __('common.pin_location_hint') }}</p>
                            <div id="checkout-delivery-map" class="rounded border" style="height: 280px;"></div>
                            <input type="hidden" name="shipping_latitude" id="shipping_latitude" value="{{ old('shipping_latitude', $checkoutState['shipping_latitude'] ?? '') }}">
                            <input type="hidden" name="shipping_longitude" id="shipping_longitude" value="{{ old('shipping_longitude', $checkoutState['shipping_longitude'] ?? '') }}">
                        </div>

                        <!-- Pickup Info -->
                        <div id="pickup-info" class="{{ (old('delivery_type', $checkoutState['delivery_type'] ?? 'home_delivery') == 'home_delivery') ? 'd-none' : '' }}">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i> {{ __('common.pickup_info_message') }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Note -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold">{{ __('common.order_note') }}</h5>
                    </div>
                    <div class="card-body">
                        <textarea name="order_note" class="form-control" rows="3" placeholder="{{ __('common.order_note_placeholder') }}">{{ old('order_note', $checkoutState['order_note'] ?? '') }}</textarea>
                    </div>
                </div>
            </form>
        </div>

        <!-- Order Summary Sidebar -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="fw-bold mb-4">{{ __('common.order_summary') }}</h5>
                    
                    @if($freeShippingMin > 0)
                        <div class="mb-3">
                            @if($subtotal >= $freeShippingMin)
                                <div class="alert alert-success py-2 small">
                                    <i class="fas fa-check-circle me-1"></i> {{ __('common.free_shipping_eligible') }}
                                </div>
                            @else
                                <div class="alert alert-info py-2 small">
                                    <i class="fas fa-info-circle me-1"></i> {{ __('common.spend_more_for_free_shipping', ['amount' => format_price($freeShippingMin - $subtotal)]) }}
                                </div>
                            @endif
                        </div>
                    @endif

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">{{ __('common.subtotal') }}</span>
                        <span class="fw-bold">{{ format_price((float) $subtotal) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">{{ __('common.tax') }} ({{ $taxPercent }}%)</span>
                        <span class="fw-bold">{{ format_price((float) ($subtotal * ($taxPercent / 100))) }}</span>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">{{ __('common.delivery_charge') }}</span>
                        <span class="fw-bold" id="shipping-cost-display">--</span>
                    </div>

                    <div class="d-flex justify-content-between mb-4">
                        <span class="fw-bold fs-5">{{ __('common.total_amount') }}</span>
                        <span class="fw-bold fs-5 text-primary" id="total-amount-display">{{ format_price((float) ($subtotal * (1 + $taxPercent / 100))) }}</span>
                    </div>

                    <button type="submit" form="shipping-form" class="btn btn-primary w-100 py-2 fw-bold">
                        {{ __('common.proceed_to_payment') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

@push('scripts')
<script src="{{ asset('global/leaflet/leaflet.js') }}"></script>
<script src="{{ asset('frontend/js/checkout-delivery-map.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var deliveryRadios = document.querySelectorAll('input[name="delivery_type"]');
        var addressSection = document.getElementById('address-section');
        var pickupInfo = document.getElementById('pickup-info');

        // Shipping Calculation Variables
        const insideCityNames = @json($insideCityNames ?? ['dhaka']);
        const insideCityCost = {{ $insideCityCost ?? 0 }};
        const outsideCityCost = {{ $outsideCityCost ?? 0 }};
        const freeShippingMin = {{ $freeShippingMin ?? 0 }};
        const subtotal = {{ $subtotal ?? 0 }};
        const taxPercent = {{ $taxPercent ?? 0 }};
        const currencySymbol = "{{ $currencySymbol ?? '$' }}";

        function updateShipping() {
            let shippingCost = 0;
            const deliveryTypeInput = document.querySelector('input[name="delivery_type"]:checked');
            
            if (deliveryTypeInput && deliveryTypeInput.value === 'home_delivery') {
                if (freeShippingMin > 0 && subtotal >= freeShippingMin) {
                    shippingCost = 0;
                } else {
                    const selectedAddress = document.querySelector('input[name="address_id"]:checked');
                    if (selectedAddress) {
                        const label = selectedAddress.closest('label');
                        const city = (label.getAttribute('data-city') || '').toLowerCase();
                        const state = (label.getAttribute('data-state') || '').toLowerCase();
                        const line1 = (label.getAttribute('data-line1') || '').toLowerCase();
                        const line2 = (label.getAttribute('data-line2') || '').toLowerCase();
                        
                        // Check if city, state, line1 OR line2 contains any of the inside city names
                        let isInside = false;
                        insideCityNames.forEach(name => {
                            if (name) {
                                const n = name.toLowerCase();
                                if (
                                    (city && city.includes(n)) || 
                                    (state && state.includes(n)) ||
                                    (line1 && line1.includes(n)) ||
                                    (line2 && line2.includes(n))
                                ) {
                                    isInside = true;
                                }
                            }
                        });

                        if (isInside) {
                            shippingCost = insideCityCost;
                        } else {
                            shippingCost = outsideCityCost;
                        }
                    } else {
                        // Default to outside city if no address selected yet, or 0 if preferred
                        // Ideally 0 until address selected, but let's assume worst case or 0
                        shippingCost = 0; 
                    }
                }
            } else {
                // Pickup
                shippingCost = 0;
            }
            
            // Update Display
            const shippingDisplay = document.getElementById('shipping-cost-display');
            if (shippingDisplay) {
                shippingDisplay.innerText = currencySymbol + shippingCost.toFixed(2);
            }
            
            const tax = subtotal * (taxPercent / 100);
            const total = subtotal + tax + shippingCost;
            
            const totalDisplay = document.getElementById('total-amount-display');
            if (totalDisplay) {
                totalDisplay.innerText = currencySymbol + total.toFixed(2);
            }
        }

        deliveryRadios.forEach(function(radio) {
            radio.addEventListener('change', function() {
                if (this.value === 'home_delivery') {
                    if (addressSection) addressSection.classList.remove('d-none');
                    if (pickupInfo) pickupInfo.classList.add('d-none');
                } else {
                    if (addressSection) addressSection.classList.add('d-none');
                    if (pickupInfo) pickupInfo.classList.remove('d-none');
                }
                updateShipping();
            });
        });

        const addressRadios = document.querySelectorAll('input[name="address_id"]');
        addressRadios.forEach(function(radio) {
            radio.addEventListener('change', updateShipping);
        });

        // Initial call
        updateShipping();
    });
</script>
@endpush
@endsection
