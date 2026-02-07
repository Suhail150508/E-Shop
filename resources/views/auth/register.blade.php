@extends('layouts.frontend')

@section('content')
@push('styles')
    <link rel="stylesheet" href="{{ asset('frontend/css/auth.css') }}">
@endpush
@php
    $title = setting('auth_register_title', __('auth.create_account_title'));
    $subtitle = setting('auth_register_subtitle', __('auth.register_subtitle') . ' ' . config('app.name'));
    $image = setting('auth_register_image', null);
@endphp

<div class="container-fluid p-0">
    <div class="row g-0 auth-container">
        <!-- Image Side (Left) -->
        <div class="col-lg-6 d-none d-lg-block position-relative overflow-hidden bg-light">
            <div class="position-absolute top-0 start-0 w-100 h-100 bg-cover animate-image" 
                 style="background-image: url('{{ getImageOrPlaceholder($image, '800x600') }}'); background-position: center; background-size: cover;">
            </div>
            <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark opacity-25"></div>
            <div class="position-absolute bottom-0 start-0 p-5 text-white w-100 auth-overlay">
                <h2 class="display-5 fw-bold mb-3">{{ __('Join Our Community') }}</h2>
                <p class="lead mb-0 text-white-50">{{ __('Experience the best shopping experience with exclusive benefits.') }}</p>
            </div>
        </div>
        
        <!-- Form Side (Right) -->
        <div class="col-lg-6 d-flex align-items-center justify-content-center bg-white">
            <div class="w-100 p-4 p-md-5 auth-form-wrapper">
                <div class="text-center mb-5">
                    <a href="{{ route('home') }}" class="d-inline-block mb-4 text-decoration-none">
                        <img src="{{ getImageOrPlaceholder(setting('app_logo'), '150x40') }}" alt="{{ config('app.name') }}" height="40">
                    </a>
                    <h1 class="h2 fw-bold mb-2">{{ $title }}</h1>
                    <p class="text-muted">{{ $subtitle }}</p>
                </div>
                
                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required autofocus placeholder="{{ __('auth.enter_name') }}">
                        <label for="name">{{ __('auth.full_name') }}</label>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-floating mb-3">
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required placeholder="{{ __('common.enter_email') }}">
                        <label for="email">{{ __('auth.email') }}</label>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-floating mb-3 position-relative">
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required placeholder="{{ __('auth.enter_password') }}">
                        <label for="password">{{ __('auth.password') }}</label>
                        <button type="button" class="btn btn-link text-decoration-none position-absolute top-50 end-0 translate-middle-y me-2 text-muted" onclick="togglePassword('password', 'toggleIcon1')">
                            <i class="far fa-eye" id="toggleIcon1"></i>
                        </button>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-floating mb-4 position-relative">
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required placeholder="{{ __('auth.confirm_password_placeholder') }}">
                        <label for="password_confirmation">{{ __('auth.confirm_password') }}</label>
                        <button type="button" class="btn btn-link text-decoration-none position-absolute top-50 end-0 translate-middle-y me-2 text-muted" onclick="togglePassword('password_confirmation', 'toggleIcon2')">
                            <i class="far fa-eye" id="toggleIcon2"></i>
                        </button>
                    </div>
                    
                    <button type="submit" class="btn btn-dark w-100 py-3 fw-bold shadow-sm mb-4">
                        {{ __('auth.register') }}
                    </button>
                    
                    <div class="text-center">
                        <p class="text-muted mb-0">
                            {{ __("Already have an account?") }} 
                            <a href="{{ route('login') }}" class="text-primary fw-bold text-decoration-none ms-1">
                                {{ __('auth.login') }}
                            </a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('frontend/js/auth.js') }}"></script>
@endpush
