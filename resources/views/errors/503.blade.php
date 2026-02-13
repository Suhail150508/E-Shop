<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('common.service_unavailable') }}</title>
    
    {{-- Fonts & Icons --}}
    <link rel="stylesheet" href="{{ asset('backend/css/all.min.css') }}">

    {{-- Bootstrap via Vite (RTL-aware) --}}
    @if(app()->getLocale() == 'ar')
        @vite(['resources/css/app-rtl.css', 'resources/js/app.js'])
    @else
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <style>
        body {
            font-family: 'DM Sans', sans-serif;
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }
        .maintenance-card {
            background: white;
            padding: 3rem;
            border-radius: 1rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            max-width: 600px;
            width: 90%;
            text-align: center;
        }
        .icon-wrapper {
            width: 80px;
            height: 80px;
            background: #e9ecef;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }
        .icon-wrapper i {
            font-size: 2.5rem;
            color: #6c757d;
        }
        h1 {
            color: #212529;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        p {
            color: #6c757d;
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 0;
        }
    </style>
</head>
<body>
    <div class="maintenance-card">
        <div class="icon-wrapper">
            <i class="fas fa-tools"></i>
        </div>
        <h1>{{ __('common.under_maintenance') }}</h1>
        <p>{{ __('common.maintenance_message') }}</p>
    </div>
</body>
</html>
