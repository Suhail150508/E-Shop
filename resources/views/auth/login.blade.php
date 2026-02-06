@extends('layouts.frontend')

@section('content')
@push('styles')
    <link rel="stylesheet" href="{{ asset('frontend/css/auth.css') }}">
@endpush
@php
    $title = setting('auth_login_title', __('auth.login'));
    $subtitle = setting('auth_login_subtitle', __('auth.welcome_back') . ' ' . config('app.name'));
    $image = setting('auth_login_image', null);
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
                <h2 class="display-5 fw-bold mb-3">{{ config('app.name') }}</h2>
                <p class="lead mb-0 text-white-50">Discover a world of elegance and style.</p>
            </div>
        </div>
        
        <!-- Form Side (Right) -->
        <div class="col-lg-6 d-flex align-items-center justify-content-center bg-white">
            <div class="w-100 p-4 p-md-5 auth-form-wrapper">
                <div class="text-center mb-5">
                    <a href="{{ route('home') }}" class="d-inline-block mb-4 text-decoration-none">
                        <img src="{{ asset('frontend/img/logo.png') }}" alt="{{ config('app.name') }}" height="40" onerror="this.onerror=null; this.src='{{ getImageOrPlaceholder(null, '150x40') }}';">
                    </a>
                    <h1 class="h2 fw-bold mb-2">{{ $title }}</h1>
                    <p class="text-muted">{{ $subtitle }}</p>
                </div>
                
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    
                    <div class="form-floating mb-3">
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="{{ __('common.enter_email') }}">
                        <label for="email">{{ __('auth.email') }}</label>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-floating mb-3 position-relative">
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required placeholder="{{ __('auth.enter_password') }}">
                        <label for="password">{{ __('auth.password') }}</label>
                        <button type="button" class="btn btn-link text-decoration-none position-absolute top-50 end-0 translate-middle-y me-2 text-muted" onclick="togglePassword()">
                            <i class="far fa-eye" id="toggleIcon"></i>
                        </button>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember" @checked(old('remember'))>
                            <label class="form-check-label text-muted" for="remember">
                                {{ __('auth.remember_me') }}
                            </label>
                        </div>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-decoration-none text-primary small fw-semibold">
                                {{ __('auth.forgot_password') }}
                            </a>
                        @endif
                    </div>
                    
                    <button type="submit" class="btn btn-dark w-100 py-3 fw-bold shadow-sm mb-4">
                        {{ __('auth.login') }}
                    </button>
                    
                    <div class="text-center">
                        <p class="text-muted mb-0">
                            {{ __("Don't have an account?") }} 
                            <a href="{{ route('register') }}" class="text-primary fw-bold text-decoration-none ms-1">
                                {{ __('auth.register') }}
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
