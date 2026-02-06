@extends('layouts.frontend')

@section('content')
    <div class="account-layout bg-light position-relative">
        <div class="container">
            <div class="row g-4">
                <!-- Mobile Sidebar Toggle -->
                <div class="d-lg-none mb-4">
                    <button class="btn btn-white w-100 d-flex align-items-center justify-content-between shadow-sm border py-3 rounded-3" type="button" data-bs-toggle="collapse" data-bs-target="#accountSidebar" aria-expanded="false" aria-controls="accountSidebar">
                        <span class="fw-bold"><i class="fa-solid fa-bars me-2"></i> {{ __('Account Menu') }}</span>
                        <i class="fa-solid fa-chevron-down"></i>
                    </button>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-3 collapse d-lg-block" id="accountSidebar">
                    <div class="sticky-sidebar">
                        @include('frontend.account.partials.sidebar')
                    </div>
                </div>

                <!-- Main Content -->
                <div class="col-lg-9">
                    @yield('account_content')
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <link rel="stylesheet" href="{{ asset('frontend/css/customer-panel.css') }}">
        <link rel="stylesheet" href="{{ asset('frontend/css/customer.css') }}">
    @endpush
@endsection
