@extends('layouts.customer')

@section('title', __('Edit Address'))

@section('account_content')
    <!-- intl-tel-input CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css" />

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white border-bottom-0 py-3 px-4">
            <h5 class="mb-0 fw-bold">{{ __('Address / Edit') }}</h5>
        </div>
        <div class="card-body p-4">
            <form method="POST" action="{{ route('customer.addresses.update', $address) }}">
                @csrf
                @method('PUT')
                
                <!-- Map Section -->
                <div class="mb-4 position-relative">
                    <div id="map" class="rounded-3 bg-light d-flex align-items-center justify-content-center text-danger fw-bold fs-5 address-map-container">
                        {{ __('Map') }}
                    </div>
                    <div class="position-absolute top-0 start-50 translate-middle-x mt-3 w-50">
                        <input type="text" class="form-control shadow-sm" placeholder="{{ __('Search for a location') }}">
                    </div>
                    <!-- Hidden fields for coordinates -->
                    <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', $address->latitude) }}">
                    <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', $address->longitude) }}">
                </div>

                <div class="row g-3">
                    <!-- Type and Contact Number -->
                    <div class="col-md-6">
                        <label for="type" class="form-label small text-muted">{{ __('Type') }}</label>
                        <select id="type" name="type" class="form-select @error('type') is-invalid @enderror">
                            <option value="Home" @selected(old('type', $address->type) === 'Home')>{{ __('Home') }}</option>
                            <option value="Office" @selected(old('type', $address->type) === 'Office')>{{ __('Office') }}</option>
                            <option value="Other" @selected(old('type', $address->type) === 'Other')>{{ __('Other') }}</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="phone" class="form-label small text-muted">{{ __('Contact Number') }}</label>
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
                        <label for="line1" class="form-label small text-muted">{{ __('Address') }}</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-location-dot text-primary"></i></span>
                            <input type="text" id="line1" name="line1" value="{{ old('line1', $address->line1) }}" class="form-control border-start-0 ps-0 @error('line1') is-invalid @enderror" placeholder="{{ __('Mirpur-10, Dhaka, Bangladesh') }}" required>
                        </div>
                        @error('line1')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Title and Email -->
                    <div class="col-md-6">
                        <label for="label" class="form-label small text-muted">{{ __('Title') }}</label>
                        <input type="text" id="label" name="label" value="{{ old('label', $address->label) }}" class="form-control @error('label') is-invalid @enderror" placeholder="{{ __('Mirpur') }}">
                        @error('label')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label small text-muted">{{ __('Email') }}</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $address->email) }}" class="form-control @error('email') is-invalid @enderror" placeholder="{{ __('customer@gmail.com') }}">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Road and House -->
                    <div class="col-md-6">
                        <label for="road" class="form-label small text-muted">{{ __('Road') }}</label>
                        <input type="text" id="road" name="road" value="{{ old('road', $address->road) }}" class="form-control @error('road') is-invalid @enderror" placeholder="{{ __('10') }}">
                        @error('road')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="house" class="form-label small text-muted">{{ __('House') }}</label>
                        <input type="text" id="house" name="house" value="{{ old('house', $address->house) }}" class="form-control @error('house') is-invalid @enderror" placeholder="{{ __('120') }}">
                        @error('house')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Floor and Postal Code -->
                    <div class="col-md-6">
                        <label for="floor" class="form-label small text-muted">{{ __('Floor') }}</label>
                        <input type="text" id="floor" name="floor" value="{{ old('floor', $address->floor) }}" class="form-control @error('floor') is-invalid @enderror" placeholder="{{ __('5') }}">
                        @error('floor')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="postal_code" class="form-label small text-muted">{{ __('Postal Code') }}</label>
                        <input type="text" id="postal_code" name="postal_code" value="{{ old('postal_code', $address->postal_code) }}" class="form-control @error('postal_code') is-invalid @enderror" placeholder="{{ __('1200') }}">
                        @error('postal_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Is Default -->
                    <div class="col-12 mt-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_default" name="is_default" value="1" @checked(old('is_default', $address->is_default))>
                            <label class="form-check-label" for="is_default">{{ __('Is Default') }}</label>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-primary px-4">
                            {{ __('Update Address') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@19.2.16/build/js/intlTelInput.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const phoneInput = document.querySelector("#phone");
            if (phoneInput) {
                const iti = window.intlTelInput(phoneInput, {
                    utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@19.2.16/build/js/utils.js",
                    preferredCountries: ['us', 'gb', 'bd'],
                    separateDialCode: true,
                    initialCountry: "auto",
                    geoIpLookup: function(callback) {
                        fetch("https://ipapi.co/json")
                            .then(function(res) { return res.json(); })
                            .then(function(data) { callback(data.country_code); })
                            .catch(function() { callback("us"); });
                    },
                });

                // Update input value with full number on form submit
                const form = phoneInput.closest('form');
                if (form) {
                    form.addEventListener('submit', function() {
                        if (iti.isValidNumber()) {
                            phoneInput.value = iti.getNumber();
                        }
                    });
                }
            }
        });
    </script>
@endsection
