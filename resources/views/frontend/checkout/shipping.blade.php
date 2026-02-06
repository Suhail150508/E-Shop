@extends('layouts.frontend')

@push('styles')
    <link rel="stylesheet" href="{{ asset('frontend/css/checkout.css') }}">
@endpush

@section('content')
<div class="container py-5">
    @include('frontend.checkout.steps', ['currentStep' => 2])

    <div class="row g-4">
        <div class="col-lg-8">
            <form id="shipping-form" action="{{ route('checkout.shipping.store') }}" method="POST">
                @csrf
                
                <!-- Pickup and Delivery Options -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold">{{ setting('checkout_shipping_title', __('Pickup and delivery options')) }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="btn-group w-100 mb-3" role="group">
                            <input type="radio" class="btn-check" name="delivery_type" id="delivery_home" value="home_delivery" {{ (old('delivery_type', $checkoutState['delivery_type'] ?? 'home_delivery') == 'home_delivery') ? 'checked' : '' }}>
                            <label class="btn btn-outline-primary py-3" for="delivery_home">
                                <i class="fas fa-home me-2"></i> {{ __('Home Delivery') }}
                            </label>

                            <input type="radio" class="btn-check" name="delivery_type" id="delivery_pickup" value="pickup" {{ (old('delivery_type', $checkoutState['delivery_type'] ?? '') == 'pickup') ? 'checked' : '' }}>
                            <label class="btn btn-outline-primary py-3" for="delivery_pickup">
                                <i class="fas fa-store me-2"></i> {{ __('Pick Up in Person') }}
                            </label>
                        </div>

                        <!-- Address Section -->
                        <div id="address-section" class="{{ (old('delivery_type', $checkoutState['delivery_type'] ?? 'home_delivery') == 'pickup') ? 'd-none' : '' }}">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="fw-bold mb-0">{{ __('Delivery Address') }}</h6>
                                <a href="{{ route('customer.addresses.create') }}" class="text-decoration-none small fw-bold">
                                    <i class="fas fa-plus-circle"></i> {{ __('Add Address') }}
                                </a>
                            </div>

                            @if($addresses->isEmpty())
                                <div class="alert alert-warning">
                                    {{ __('No saved addresses found. Please add an address.') }}
                                </div>
                            @else
                                <div class="list-group">
                                    @foreach($addresses as $address)
                                        <label class="list-group-item list-group-item-action d-flex align-items-start gap-3 p-3 border rounded mb-2">
                                            <input class="form-check-input flex-shrink-0 mt-1" type="radio" name="address_id" value="{{ $address->id }}" {{ (old('address_id', $checkoutState['address_id'] ?? '') == $address->id || ($loop->first && !isset($checkoutState['address_id']))) ? 'checked' : '' }}>
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between">
                                                    <span class="fw-bold">{{ $address->label ?? __('Address') }}</span>
                                                    <a href="{{ route('customer.addresses.edit', $address->id) }}" class="text-muted small"><i class="fas fa-pencil-alt"></i> {{ __('Edit') }}</a>
                                                </div>
                                                <p class="mb-0 text-muted small">
                                                    {{ $address->line1 }}<br>
                                                    @if($address->line2) {{ $address->line2 }}<br> @endif
                                                    {{ trim(implode(', ', array_filter([$address->city, $address->state, $address->postal_code]))) ?: __('N/A') }}<br>
                                                    {{ $address->country ?? __('N/A') }}<br>
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

                        <!-- Google Map Section -->
                        @if($googleMapsEnabled && $googleMapsApiKey)
                        <div id="map-section" class="mt-4 mb-3 {{ (old('delivery_type', $checkoutState['delivery_type'] ?? 'home_delivery') == 'pickup') ? 'd-none' : '' }}">
                            <label class="form-label fw-bold mb-2">{{ __('Pin Delivery Location (Optional)') }}</label>
                            <div class="card border p-1">
                                <div id="google-map" class="google-map-container"></div>
                            </div>
                            <input type="hidden" name="shipping_latitude" id="shipping_latitude" value="{{ old('shipping_latitude', $checkoutState['shipping_latitude'] ?? '') }}">
                            <input type="hidden" name="shipping_longitude" id="shipping_longitude" value="{{ old('shipping_longitude', $checkoutState['shipping_longitude'] ?? '') }}">
                            <p class="text-muted small mt-2 mb-0">
                                <i class="fas fa-map-marker-alt text-danger me-1"></i> 
                                {{ __('Click on the map or drag the pin to set your exact location for better delivery accuracy.') }}
                            </p>
                        </div>

                        @push('scripts')
                        <script>
                            window.deliveryLocationTitle = "{{ __('Delivery Location') }}";
                        </script>
                        <script src="{{ asset('frontend/js/checkout-map.js') }}"></script>
                        <script src="https://maps.googleapis.com/maps/api/js?key={{ $googleMapsApiKey }}&callback=initMap&libraries=places" async defer></script>
                        @endpush
                        @endif

                        <!-- Pickup Info -->
                        <div id="pickup-info" class="{{ (old('delivery_type', $checkoutState['delivery_type'] ?? 'home_delivery') == 'home_delivery') ? 'd-none' : '' }}">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i> {{ __('You can pick up your order from our store location.') }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Note -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold">{{ __('Order Note') }}</h5>
                    </div>
                    <div class="card-body">
                        <textarea name="order_note" class="form-control" rows="3" placeholder="{{ __('Add any special instructions for your order here...') }}">{{ old('order_note', $checkoutState['order_note'] ?? '') }}</textarea>
                    </div>
                </div>
            </form>
        </div>

        <!-- Order Summary Sidebar -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="fw-bold mb-4">{{ __('Order Summary') }}</h5>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">{{ __('Sub Total') }}</span>
                        <span class="fw-bold">{{ format_price((float) $subtotal) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">{{ __('Tax') }} ({{ $taxPercent }}%)</span>
                        <span class="fw-bold">{{ format_price((float) ($subtotal * ($taxPercent / 100))) }}</span>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between mb-4">
                        <span class="fw-bold fs-5">{{ __('Total Amount') }}</span>
                        <span class="fw-bold fs-5 text-primary">{{ format_price((float) ($subtotal * (1 + $taxPercent / 100))) }}</span>
                    </div>

                    <button type="submit" form="shipping-form" class="btn btn-primary w-100 py-2 fw-bold">
                        {{ __('Proceed to Payment') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const deliveryRadios = document.querySelectorAll('input[name="delivery_type"]');
        const addressSection = document.getElementById('address-section');
        const pickupInfo = document.getElementById('pickup-info');

        deliveryRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'home_delivery') {
                    addressSection.classList.remove('d-none');
                    pickupInfo.classList.add('d-none');
                } else {
                    addressSection.classList.add('d-none');
                    pickupInfo.classList.remove('d-none');
                }
            });
        });
    });
</script>
@endpush
@endsection
