@extends('layouts.frontend')

 @push('styles')
    <link rel="stylesheet" href="{{ asset('frontend/css/auth.css') }}">
@endpush

@section('content')
@php
    $page = \App\Models\Page::where('slug', 'auth-forgot-password')->first();
    $title = $page ? $page->title : __('Forgot Password?');
    $subtitle = $page ? $page->content : __('Enter your email address and we will send you a link to reset your password.');
    $image = $page && $page->image ? (filter_var($page->image, FILTER_VALIDATE_URL) ? $page->image : asset($page->image)) : null;
@endphp

<div class="container-fluid p-0">
    <div class="row g-0" style="min-height: 100vh;">
        <!-- Image Side (Left) -->
        <div class="col-lg-6 d-none d-lg-block position-relative overflow-hidden bg-light">
            <div class="position-absolute top-0 start-0 w-100 h-100 bg-cover animate-image" 
                 style="background-image: url('{{ getImageOrPlaceholder($image, '800x600') }}'); background-position: center; background-size: cover;">
            </div>
            <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark opacity-25"></div>
            <div class="position-absolute bottom-0 start-0 p-5 text-white w-100" style="background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);">
                <h2 class="display-5 fw-bold mb-3">{{ __('Secure Recovery') }}</h2>
                <p class="lead mb-0 text-white-50">{{ __('We are here to help you get back to your account safely.') }}</p>
            </div>
        </div>
        
        <!-- Form Side (Right) -->
        <div class="col-lg-6 d-flex align-items-center justify-content-center bg-white">
            <div class="w-100 p-4 p-md-5" style="max-width: 550px;">
                <div class="text-center mb-5">
                    <a href="{{ route('home') }}" class="d-inline-block mb-4 text-decoration-none">
                        <img src="{{ asset('frontend/img/logo.png') }}" alt="{{ config('app.name') }}" height="40" onerror="this.onerror=null; this.src='{{ getImageOrPlaceholder(null, '150x40') }}';">
                    </a>
                    <h1 class="h2 fw-bold mb-2">{{ $title }}</h1>
                    <p class="text-muted">{{ $subtitle }}</p>
                </div>

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf
                    
                    <div class="form-floating mb-4">
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="{{ __('common.enter_email') }}">
                        <label for="email">{{ __('auth.email') }}</label>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <button type="submit" class="btn btn-dark w-100 py-3 fw-bold shadow-sm mb-4">
                        {{ __('Send Reset Link') }}
                    </button>
                    
                    <div class="text-center">
                        <a href="{{ route('login') }}" class="text-primary fw-bold text-decoration-none">
                            <i class="fas fa-arrow-left me-1"></i> {{ __('Back to Login') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
