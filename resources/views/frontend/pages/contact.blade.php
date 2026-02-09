@extends('layouts.frontend')

@push('styles')
    <link rel="stylesheet" href="{{ asset('frontend/css/contact.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('frontend/js/contact.js') }}"></script>
@endpush

@section('content')
@include('frontend.partials.breadcrumb', ['title' => (isset($page) && $page ? $page->translate('title') : null) ?? __('common.contact'), 'bgImage' => optional($page)->image ?? getImageOrPlaceholder(null, '1920x400')])

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            @if(isset($page) && ($page->translate('content') ?? $page->content))
                {!! $page->translate('content') ?? $page->content !!}
            @else
                <p class="text-center mb-5 lead">{{ __('common.contact_intro') }}</p>

                <div class="row g-4 mb-5">
                    <div class="col-md-4 text-center">
                        <div class="mb-3">
                            <i class="fas fa-map-marker-alt fa-2x text-terracotta"></i>
                        </div>
                        <h5>{{ __('common.visit_us') }}</h5>
                        <p class="text-muted">123 Fashion Street,<br>New York, NY 10001</p>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="mb-3">
                            <i class="fas fa-envelope fa-2x text-terracotta"></i>
                        </div>
                        <h5>{{ __('common.email_us') }}</h5>
                        <p class="text-muted">support@luxe-store.com<br>info@luxe-store.com</p>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="mb-3">
                            <i class="fas fa-phone fa-2x text-terracotta"></i>
                        </div>
                        <h5>{{ __('common.call_us') }}</h5>
                        <p class="text-muted">+1 (555) 123-4567<br>Mon-Fri, 9am-6pm</p>
                    </div>
                </div>
            @endif

            <div class="card border-0 shadow-sm mt-5">
                <div class="card-body p-4">
                    <h4 class="mb-4">{{ __('common.send_message') }}</h4>
                    <form id="contactForm" action="{{ route('contact.submit') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">{{ __('common.name') }}</label>
                                <input type="text" name="name" class="form-control" placeholder="{{ __('common.your_name') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('common.email') }}</label>
                                <input type="email" name="email" class="form-control" placeholder="{{ __('common.your_email') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('common.phone') }}</label>
                                <input type="text" name="phone" class="form-control" placeholder="{{ __('common.your_phone') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('common.subject') }}</label>
                                <input type="text" name="subject" class="form-control" placeholder="{{ __('common.how_can_we_help') }}" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">{{ __('common.message') }}</label>
                                <textarea name="message" class="form-control" rows="5" placeholder="{{ __('common.your_message') }}" required></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-terracotta text-white position-relative" id="contactBtn">
                                    <span class="btn-text">{{ __('common.send_message_btn') }}</span>
                                    <span class="loader-inline d-none" role="status" aria-hidden="true"><span class="block"></span><span class="block"></span><span class="block"></span><span class="block"></span></span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
