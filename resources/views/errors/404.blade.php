@extends('layouts.frontend')

@section('title', __('common.page_not_found_title') . ' - ' . config('app.name'))

@section('meta')
    <meta name="robots" content="noindex, follow">
    <meta name="description" content="{{ __('common.page_not_found_message') }}">
@endsection

@section('content')
<section class="error-404-section py-5">
    <div class="container">
        <nav aria-label="{{ __('common.breadcrumb') }}">
            <ol class="breadcrumb mb-4 small text-muted">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">{{ __('common.home') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ __('common.page_not_found') }}</li>
            </ol>
        </nav>

        <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-6">
                <div class="text-center py-5 px-3">
                    <div class="error-404-code mb-3" aria-hidden="true">404</div>
                    <div class="error-404-icon mb-4">
                        <i class="fas fa-exclamation-circle text-muted" aria-hidden="true"></i>
                    </div>
                    <h1 class="h2 fw-bold text-dark mb-3">{{ __('common.page_not_found_title') }}</h1>
                    <p class="text-muted mb-4 mx-auto" style="max-width: 420px;">
                        {{ __('common.page_not_found_message') }}
                    </p>
                    <p class="small text-muted mb-4">{{ __('common.page_not_found_help') }}</p>
                    <div class="d-flex flex-wrap gap-3 justify-content-center">
                        <a href="{{ route('home') }}" class="btn btn-primary rounded-pill px-4 py-3 fw-semibold">
                            <i class="fas fa-home me-2" aria-hidden="true"></i>{{ __('common.back_to_home') }}
                        </a>
                        <a href="{{ route('shop.index') }}" class="btn btn-outline-primary rounded-pill px-4 py-3 fw-semibold">
                            <i class="fas fa-shopping-bag me-2" aria-hidden="true"></i>{{ __('common.browse_shop') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@push('styles')
<style>
    .error-404-section { min-height: 60vh; display: flex; align-items: center; }
    .error-404-code { font-size: clamp(4rem, 15vw, 8rem); font-weight: 800; color: var(--bs-gray-200, #e9ecef); line-height: 1; }
    .error-404-icon { font-size: 3rem; }
    @media (max-width: 575.98px) {
        .error-404-section .d-flex { flex-direction: column; }
        .error-404-section .btn { width: 100%; max-width: 280px; }
    }
</style>
@endpush
@endsection
