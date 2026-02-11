@extends('layouts.customer')

@section('title', __('common.edit_address'))

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="anonymous">
@endpush

@section('account_content')
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white border-bottom-0 py-3 px-4">
            <h5 class="mb-0 fw-bold">{{ __('common.address_edit') }}</h5>
        </div>
        <div class="card-body p-4">
            <form method="POST" action="{{ route('customer.addresses.update', $address) }}">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="form-label small text-muted fw-semibold">{{ __('common.select_location_on_map') }}</label>
                    <p class="text-muted small mb-2">{{ __('common.map_click_hint') }}</p>
                    <div class="position-relative">
                        <input type="text" id="address-map-search" class="form-control form-control-sm mb-2" placeholder="{{ __('common.search_for_location') }}" autocomplete="off">
                        <div id="address-map" class="address-map-container rounded-3 border" style="height: 280px; min-height: 280px;"></div>
                    </div>
                    <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', $address->latitude) }}">
                    <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', $address->longitude) }}">
                </div>

                <div class="row g-3">
                    <!-- Type and Contact Number -->
                    <div class="col-md-6">
                        <label for="type" class="form-label small text-muted">{{ __('common.type') }}</label>
                        <select id="type" name="type" class="form-select @error('type') is-invalid @enderror">
                            <option value="Home" @selected(old('type', $address->type) === 'Home')>{{ __('common.home') }}</option>
                            <option value="Office" @selected(old('type', $address->type) === 'Office')>{{ __('common.office') }}</option>
                            <option value="Other" @selected(old('type', $address->type) === 'Other')>{{ __('common.other') }}</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="phone" class="form-label small text-muted">{{ __('common.contact_number') }}</label>
                        <div class="input-group">
                             <!-- Placeholder for country code -->
                             <input type="text" id="phone" name="phone" value="{{ old('phone', $address->phone) }}" class="form-control border-start-0 @error('phone') is-invalid @enderror">
                        </div>
                        @error('phone')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Address -->
                    <div class="col-12">
                        <label for="line1" class="form-label small text-muted">{{ __('common.address_label') }}</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-location-dot text-primary"></i></span>
                            <input type="text" id="line1" name="line1" value="{{ old('line1', $address->line1) }}" class="form-control border-start-0 ps-0 @error('line1') is-invalid @enderror" placeholder="{{ __('common.enter_address') }}" required>
                        </div>
                        @error('line1')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Title and Email -->
                    <div class="col-md-6">
                        <label for="label" class="form-label small text-muted">{{ __('common.title') }}</label>
                        <input type="text" id="label" name="label" value="{{ old('label', $address->label) }}" class="form-control @error('label') is-invalid @enderror" placeholder="{{ __('common.enter_title') }}">
                        @error('label')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label small text-muted">{{ __('common.email') }}</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $address->email) }}" class="form-control @error('email') is-invalid @enderror" placeholder="{{ __('common.enter_email') }}">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Road and House -->
                    <div class="col-md-6">
                        <label for="road" class="form-label small text-muted">{{ __('common.road') }}</label>
                        <input type="text" id="road" name="road" value="{{ old('road', $address->road) }}" class="form-control @error('road') is-invalid @enderror" placeholder="{{ __('common.enter_road') }}">
                        @error('road')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="house" class="form-label small text-muted">{{ __('common.house') }}</label>
                        <input type="text" id="house" name="house" value="{{ old('house', $address->house) }}" class="form-control @error('house') is-invalid @enderror" placeholder="{{ __('common.enter_house') }}">
                        @error('house')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Floor and Postal Code -->
                    <div class="col-md-6">
                        <label for="floor" class="form-label small text-muted">{{ __('common.floor') }}</label>
                        <input type="text" id="floor" name="floor" value="{{ old('floor', $address->floor) }}" class="form-control @error('floor') is-invalid @enderror" placeholder="{{ __('common.enter_floor') }}">
                        @error('floor')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="postal_code" class="form-label small text-muted">{{ __('common.postal_code') }}</label>
                        <input type="text" id="postal_code" name="postal_code" value="{{ old('postal_code', $address->postal_code) }}" class="form-control @error('postal_code') is-invalid @enderror" placeholder="{{ __('common.enter_postal_code') }}">
                        @error('postal_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Is Default -->
                    <div class="col-12 mt-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_default" name="is_default" value="1" @checked(old('is_default', $address->is_default))>
                            <label class="form-check-label" for="is_default">{{ __('common.is_default') }}</label>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-primary px-4">
                            {{ __('common.update_address') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin="anonymous"></script>
    <script src="{{ asset('frontend/js/address-map.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            function init() {
                if (typeof window.initAddressMap !== 'function') return;
                var mapEl = document.getElementById('address-map');
                if (!mapEl) return;
                window.initAddressMap({
                    mapId: 'address-map',
                    latId: 'latitude',
                    lngId: 'longitude',
                    searchId: 'address-map-search',
                    line1Id: 'line1',
                    postalCodeId: 'postal_code',
                    initialLat: @json(old('latitude', $address->latitude)),
                    initialLng: @json(old('longitude', $address->longitude))
                });
            }
            setTimeout(init, 80);
        });
    </script>
    @endpush
@endsection
