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
    @php
        $favicon = ($settings ?? [])['favicon'] ?? null;
    @endphp
    @if($favicon)
        <link rel="shortcut icon" href="{{ asset($favicon) }}" type="image/x-icon">
    @endif

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

    {{-- Toastr --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="{{ asset('global/toastr/toastr.main.css') }}">
    
    <style>
    /* Navbar Dropdown Hover */
    @media (min-width: 992px) {
        .navbar .nav-item.dropdown:hover .dropdown-menu {
            display: block;
            margin-top: 0;
        }
    }
    </style>

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
    $hideRoutes = ['cart.*','checkout.*','login','register','admin.*','customer.*'];
    $showFloatingCart = !collect($hideRoutes)->contains(fn($r)=>Str::is($r, Route::currentRouteName()));
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

@stack('modals')
@stack('scripts')
</body>
</html>
