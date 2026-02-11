@extends('layouts.admin')

@section('page_title', __('General Settings'))

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="app_name" class="form-label">{{ __('App Name') }}</label>
                <input type="text" class="form-control @error('app_name') is-invalid @enderror" id="app_name" name="app_name" value="{{ old('app_name', $settings['app_name'] ?? config('app.name')) }}">
                @error('app_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <label for="app_logo" class="form-label">{{ __('App Logo') }}</label>
                    <input type="file" class="form-control @error('app_logo') is-invalid @enderror" id="app_logo" name="app_logo" accept="image/*">
                    <div class="form-text">{{ __('Recommended size: 150x50px. Formats: PNG, JPG, SVG.') }}</div>
                    @error('app_logo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    @if(isset($settings['app_logo']))
                        <div class="mt-2 p-2 bg-light rounded d-inline-block">
                            <img src="{{ getImageOrPlaceholder($settings['app_logo'], '150x50') }}" alt="App Logo" style="max-height: 50px;">
                        </div>
                    @endif
                </div>
                <div class="col-md-6">
                    <label for="app_favicon" class="form-label">{{ __('App Favicon') }}</label>
                    <input type="file" class="form-control @error('app_favicon') is-invalid @enderror" id="app_favicon" name="app_favicon" accept="image/*">
                    <div class="form-text">{{ __('Recommended size: 32x32px. Formats: ICO, PNG.') }}</div>
                    @error('app_favicon')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    @if(isset($settings['app_favicon']))
                        <div class="mt-2 p-2 bg-light rounded d-inline-block">
                            <img src="{{ getImageOrPlaceholder($settings['app_favicon'], '32x32') }}" alt="App Favicon" style="max-height: 32px;">
                        </div>
                    @endif
                </div>
            </div>

            <div class="mb-3">
                <label for="app_currency" class="form-label">{{ __('Currency Symbol') }}</label>
                <input type="text" class="form-control @error('app_currency') is-invalid @enderror" id="app_currency" name="app_currency" value="{{ old('app_currency', $settings['app_currency'] ?? '$') }}">
                @error('app_currency')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <h2 class="h6 mb-3">{{ __('Shipping Settings') }}</h2>

            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0 text-dark"><i class="fas fa-truck me-2"></i>{{ __('Shipping Configuration') }}</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="shipping_inside_city_name" class="form-label">{{ __('common.shipping_inside_city_names') }}</label>
                            <input type="text" class="form-control" id="shipping_inside_city_name" name="shipping_inside_city_name" value="{{ old('shipping_inside_city_name', $settings['shipping_inside_city_name'] ?? 'Dhaka') }}" placeholder="e.g. Dhaka, Mirpur, Savar">
                            <div class="form-text">{{ __('common.shipping_inside_city_names_hint') }}</div>
                        </div>
                        <div class="col-md-6">
                            <label for="free_shipping_min_amount" class="form-label">{{ __('Free Shipping Minimum Amount') }}</label>
                            <div class="input-group">
                                <span class="input-group-text">{{ $settings['app_currency'] ?? '$' }}</span>
                                <input type="number" step="0.01" class="form-control" id="free_shipping_min_amount" name="free_shipping_min_amount" value="{{ old('free_shipping_min_amount', $settings['free_shipping_min_amount'] ?? '0') }}">
                            </div>
                            <div class="form-text">{{ __('Set 0 to disable free shipping.') }}</div>
                        </div>
                        <div class="col-md-6">
                            <label for="shipping_inside_city_cost" class="form-label">{{ __('Inside City Shipping Cost') }}</label>
                            <div class="input-group">
                                <span class="input-group-text">{{ $settings['app_currency'] ?? '$' }}</span>
                                <input type="number" step="0.01" class="form-control" id="shipping_inside_city_cost" name="shipping_inside_city_cost" value="{{ old('shipping_inside_city_cost', $settings['shipping_inside_city_cost'] ?? '60') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="shipping_outside_city_cost" class="form-label">{{ __('Outside City Shipping Cost') }}</label>
                            <div class="input-group">
                                <span class="input-group-text">{{ $settings['app_currency'] ?? '$' }}</span>
                                <input type="number" step="0.01" class="form-control" id="shipping_outside_city_cost" name="shipping_outside_city_cost" value="{{ old('shipping_outside_city_cost', $settings['shipping_outside_city_cost'] ?? '120') }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <h2 class="h6 mb-3">{{ __('Footer Settings') }}</h2>
            
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0 text-dark"><i class="fas fa-info-circle me-2"></i>{{ __('Footer Information') }}</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="footer_description" class="form-label">{{ __('About/Description') }}</label>
                        <textarea class="form-control" id="footer_description" name="footer_description" rows="3">{{ old('footer_description', $settings['footer_description'] ?? '') }}</textarea>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="footer_phone" class="form-label"><i class="fas fa-phone-alt me-1 text-muted"></i> {{ __('common.phone_number') }}</label>
                            <input type="text" class="form-control" id="footer_phone" name="footer_phone" value="{{ old('footer_phone', $settings['footer_phone'] ?? '') }}" placeholder="+1 (555) 123-4567">
                        </div>
                        <div class="col-md-6">
                            <label for="footer_email" class="form-label"><i class="fas fa-envelope me-1 text-muted"></i> {{ __('common.email_address') }}</label>
                            <input type="email" class="form-control" id="footer_email" name="footer_email" value="{{ old('footer_email', $settings['footer_email'] ?? '') }}" placeholder="support@example.com">
                        </div>
                        <div class="col-12">
                            <label for="footer_address" class="form-label"><i class="fas fa-map-marker-alt me-1 text-muted"></i> {{ __('common.address') }}</label>
                            <textarea class="form-control" id="footer_address" name="footer_address" rows="2" placeholder="123 Street Name, City, Country">{{ old('footer_address', $settings['footer_address'] ?? '') }}</textarea>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="copyright_text" class="form-label">{{ __('Copyright Text') }}</label>
                        <input type="text" class="form-control" id="copyright_text" name="copyright_text" value="{{ old('copyright_text', $settings['copyright_text'] ?? '') }}" placeholder="© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.">
                    </div>

                    <div class="mb-3">
                        <label for="payment_method_image" class="form-label">{{ __('common.payment_methods_image') }}</label>
                        <input type="file" class="form-control @error('payment_method_image') is-invalid @enderror" id="payment_method_image" name="payment_method_image" accept="image/*">
                        <div class="form-text">{{ __('Recommended size: 400x50px. Formats: PNG, JPG, SVG.') }}</div>
                        @error('payment_method_image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if(isset($settings['payment_method_image']))
                            <div class="mt-2 p-2 bg-dark rounded d-inline-block">
                                <img src="{{ getImageOrPlaceholder($settings['payment_method_image'], '400x50') }}" alt="Payment Methods" style="max-height: 40px;">
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0 text-dark"><i class="fas fa-share-alt me-2"></i>{{ __('Social Media Links') }}</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="social_facebook" class="form-label"><i class="fab fa-facebook text-primary me-1"></i> Facebook</label>
                            <input type="url" class="form-control" id="social_facebook" name="social_facebook" value="{{ old('social_facebook', $settings['social_facebook'] ?? '') }}" placeholder="https://facebook.com/yourpage">
                        </div>
                        <div class="col-md-6">
                            <label for="social_twitter" class="form-label"><i class="fab fa-twitter text-info me-1"></i> Twitter (X)</label>
                            <input type="url" class="form-control" id="social_twitter" name="social_twitter" value="{{ old('social_twitter', $settings['social_twitter'] ?? '') }}" placeholder="https://twitter.com/yourhandle">
                        </div>
                        <div class="col-md-6">
                            <label for="social_instagram" class="form-label"><i class="fab fa-instagram text-danger me-1"></i> Instagram</label>
                            <input type="url" class="form-control" id="social_instagram" name="social_instagram" value="{{ old('social_instagram', $settings['social_instagram'] ?? '') }}" placeholder="https://instagram.com/yourhandle">
                        </div>
                        <div class="col-md-6">
                            <label for="social_linkedin" class="form-label"><i class="fab fa-linkedin text-primary me-1"></i> LinkedIn</label>
                            <input type="url" class="form-control" id="social_linkedin" name="social_linkedin" value="{{ old('social_linkedin', $settings['social_linkedin'] ?? '') }}" placeholder="https://linkedin.com/company/yourpage">
                        </div>
                    </div>
                </div>
            </div>

            <hr class="my-4">

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">{{ __('Save Settings') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
