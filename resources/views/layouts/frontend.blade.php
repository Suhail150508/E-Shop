<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-id" content="{{ auth()->id() }}">

    <title>@yield('title', config('app.name'))</title>
    @yield('meta') 

    <link rel="icon" href="{{ getImageOrPlaceholder(setting('app_favicon'), '32x32') }}">

    {{-- Bootstrap via Vite (RTL-aware) --}}
    @if(app()->getLocale() == 'ar')
        @vite(['resources/css/app-rtl.css', 'resources/js/app.js'])
    @else
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    {{-- Fonts & Icons --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display&family=DM+Sans&family=Syne&display=swap" rel="stylesheet">

    {{-- Shared Frontend CSS --}}
    <link rel="stylesheet" href="{{ asset('frontend/css/frontend-shared.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/css/tryon-modal.css') }}">

    {{-- Toastr --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="{{ asset('global/toastr/toastr.main.css') }}">
    
    @stack('style_section')
    @stack('styles')
</head>

<body id="top">

@include('layouts.partials.header')

<main>
    @yield('content')
</main>

@include('layouts.partials.footer')

{{-- Logout Modal --}}
@include('layouts.partials.logout-modal')

{{-- Scroll to Top --}}
<a href="#top" class="scroll-top" aria-label="{{ __('common.back_to_top') }}"><i class="fas fa-arrow-up"></i></a>

{{-- Floating Cart --}}
@php
    $allowedRoutes = ['home', 'shop.index', 'shop.category', 'shop.product.show'];
    $showFloatingCart = collect($allowedRoutes)->contains(Route::currentRouteName());
@endphp

@if($showFloatingCart)
<div id="floating-cart" class="floating-cart">
    <a href="{{ route('cart.index') }}">
        <i class="fas fa-shopping-bag"></i>
        <span id="floating-cart-count" aria-label="{{ __('common.cart') }}">{{ $cartCount ?? 0 }}</span>
    </a>
</div>
@endif

{{-- JS Libraries (Local) --}}
<script src="{{ asset('backend/js/jquery.min.js') }}"></script>
<script src="{{ asset('backend/js/popper.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="{{ asset('global/toastr/toastr.main.js') }}" defer></script>
<script src="{{ asset('backend/js/bootstrap.min.js') }}"></script>

{{-- Global Translations --}}
<script>
    window.translations = {
        common: @json(trans('common')),
        cart: @json(trans('cart')),
        checkout: @json(trans('checkout')),
    };
</script>

{{-- Frontend JS (Optimized & Modular) --}}
<script src="{{ asset('frontend/js/ui.js') }}" defer></script>
<script src="{{ asset('frontend/js/cart.js') }}" defer></script>
<script src="{{ asset('frontend/js/notifications.js') }}" defer></script>

@include('layouts.partials.toaster')

@include('frontend.partials.tryon-modal')
@stack('modals')
@stack('scripts')
</body>
</html>
