@extends('layouts.frontend')

@section('title', __('Page Not Found'))

@section('content')
<div class="container d-flex flex-column justify-content-center align-items-center min-vh-75 py-5">
    <div class="text-center">
        <div class="mb-4">
            <i class="fas fa-ghost fa-6x text-muted"></i>
        </div>
        <h1 class="display-1 fw-bold text-primary">404</h1>
        <h2 class="h4 mb-4 text-secondary">{{ __('Oops! The page you are looking for does not exist.') }}</h2>
        <p class="mb-5 text-muted">{{ __('It might have been moved or deleted.') }}</p>
        <a href="{{ route('home') }}" class="btn btn-primary btn-lg rounded-pill px-5">
            <i class="fas fa-home me-2"></i> {{ __('Back to Home') }}
        </a>
    </div>
</div>
@endsection

@push('styles')
<style>
    .min-vh-75 {
        min-height: 75vh;
    }
</style>
@endpush
