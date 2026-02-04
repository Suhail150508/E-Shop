@extends('layouts.frontend')

@section('content')
@php
    $title = setting('auth_register_title', __('auth.create_account_title'));
    $subtitle = setting('auth_register_subtitle', __('auth.register_subtitle') . ' ' . config('app.name'));
    $image = setting('auth_register_image', 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
@endphp

<div class="container-fluid p-0">
    <div class="row g-0" style="min-height: 100vh;">
        <!-- Image Side (Left) -->
        <div class="col-lg-6 d-none d-lg-block position-relative overflow-hidden bg-light">
            <div class="position-absolute top-0 start-0 w-100 h-100 bg-cover animate-image" 
                 style="background-image: url('{{ $image }}'); background-position: center; background-size: cover;">
            </div>
            <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark opacity-25"></div>
            <div class="position-absolute bottom-0 start-0 p-5 text-white w-100" style="background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);">
                <h2 class="display-5 fw-bold mb-3">{{ __('Join Our Community') }}</h2>
                <p class="lead mb-0 text-white-50">{{ __('Experience the best shopping experience with exclusive benefits.') }}</p>
            </div>
        </div>
        
        <!-- Form Side (Right) -->
        <div class="col-lg-6 d-flex align-items-center justify-content-center bg-white">
            <div class="w-100 p-4 p-md-5" style="max-width: 550px;">
                <div class="text-center mb-5">
                    <a href="{{ route('home') }}" class="d-inline-block mb-4 text-decoration-none">
                        <img src="{{ asset('frontend/img/logo.png') }}" alt="{{ config('app.name') }}" height="40" onerror="this.onerror=null; this.src='https://placehold.co/150x40?text={{ config('app.name') }}';">
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

<style>
    @keyframes slowZoom {
        0% { transform: scale(1); }
        100% { transform: scale(1.1); }
    }
    .animate-image {
        animation: slowZoom 20s infinite alternate ease-in-out;
    }
    .form-floating > .form-control:focus ~ label,
    .form-floating > .form-control:not(:placeholder-shown) ~ label {
        color: var(--bs-primary);
        opacity: 0.8;
    }
    .form-floating > .form-control:focus {
        border-color: var(--bs-primary);
        box-shadow: 0 0 0 0.25rem rgba(var(--bs-primary-rgb), 0.1);
    }
</style>

<script>
    function togglePassword(inputId, iconId) {
        const passwordInput = document.getElementById(inputId);
        const toggleIcon = document.getElementById(iconId);
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        }
    }
</script>
@endsection
