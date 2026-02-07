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
                    <div class="mb-3">
                        <label for="copyright_text" class="form-label">{{ __('Copyright Text') }}</label>
                        <input type="text" class="form-control" id="copyright_text" name="copyright_text" value="{{ old('copyright_text', $settings['copyright_text'] ?? '') }}" placeholder="© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.">
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

            <h2 class="h6 mb-3">{{ __('Google Maps Settings') }}</h2>

            <div class="form-check form-switch mb-3">
                <input class="form-check-input" type="checkbox" role="switch" id="google_maps_enabled" name="google_maps_enabled" value="1" {{ old('google_maps_enabled', $settings['google_maps_enabled'] ?? '') ? 'checked' : '' }}>
                <label class="form-check-label" for="google_maps_enabled">{{ __('Enable Google Maps') }}</label>
            </div>

            <div class="mb-3">
                <label for="google_maps_api_key" class="form-label">{{ __('Google Maps API Key') }}</label>
                <input type="text" class="form-control @error('google_maps_api_key') is-invalid @enderror" id="google_maps_api_key" name="google_maps_api_key" value="{{ old('google_maps_api_key', $settings['google_maps_api_key'] ?? '') }}">
                @error('google_maps_api_key')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text text-muted">{{ __('Required for map functionality. Leave empty to use GOOGLE_MAPS_API_KEY from .env file.') }}</div>
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">{{ __('Save Settings') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
