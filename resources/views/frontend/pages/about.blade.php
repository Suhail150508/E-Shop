@extends('layouts.frontend')

@push('styles')
    <link rel="stylesheet" href="{{ asset('frontend/css/about-us.css') }}">
@endpush

@section('content')
@php
    $meta = $page->getAboutMetaForLocale(app()->getLocale());
    $m = function($key, $default = '') use ($meta) { return $meta[$key] ?? $default; };
    $imgUrl = function($key, $size = '300x300') use ($meta) {
        $path = $meta[$key] ?? null;
        if ($path === null || $path === '' || (is_string($path) && trim($path) === '')) {
            return route('placeholder', ['size' => $size]) . '?v=2';
        }
        return getImageOrPlaceholder($path, $size);
    };
@endphp
@include('frontend.partials.breadcrumb', ['title' => $page->translate('title') ?? __('common.about'), 'bgImage' => $page->image ?? $imgUrl('about_hero_image', '1920x300')])

<div class="about-page py-5">
    <div class="container">

        {{-- Hero: About [App Name] --}}
        <section class="mb-5 pb-lg-5" aria-labelledby="about-hero-heading">
            <div class="row align-items-center g-4">
                <div class="col-lg-6">
                    <img src="{{ $imgUrl('about_hero_image', '600x400') }}" alt="{{ e($m('about_hero_title', config('app.name'))) }}" class="img-fluid rounded-3 shadow-sm" onerror="this.onerror=null; this.src='{{ route('placeholder', ['size' => '600x400']) }}?v=2';">
                </div>
                <div class="col-lg-6">
                    <h1 id="about-hero-heading" class="about-hero-title display-5 fw-bold mb-3">
                        {{ $m('about_hero_title', __('common.about') . ' ' . config('app.name')) }}
                    </h1>
                    <p class="lead text-muted mb-3">{{ $m('about_hero_subtitle', 'Where Innovation Meets Seamless Online Shopping') }}</p>
                    <p class="mb-0">{{ $m('about_hero_text', 'We are committed to redefining eCommerce with a seamless, secure, and user-friendly experience—connecting buyers and sellers through cutting-edge technology.') }}</p>
                </div>
            </div>
        </section>

        {{-- Our Story --}}
        <section class="mb-5 pb-lg-5" aria-labelledby="about-story-heading">
            <h2 id="about-story-heading" class="text-center about-section-title display-6 mb-2">{{ $m('about_story_title', 'Our Story') }}</h2>
            <p class="text-center text-muted mb-5 mx-auto" style="max-width: 720px;">{{ $m('about_story_subtitle', 'From a Bold Vision to a Trusted Marketplace: How we were built to transform the future of eCommerce.') }}</p>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="h-100">
                        <img src="{{ $imgUrl('about_story_1_image', '400x300') }}" alt="{{ e($m('about_story_1_heading')) }}" class="img-fluid rounded-3 w-100 mb-3 object-fit-cover" style="height: 220px;" onerror="this.onerror=null; this.src='{{ route('placeholder', ['size' => '400x300']) }}?v=2';">
                        <h3 class="h5 fw-bold mb-2">{{ $m('about_story_1_heading', 'Our Vision and Beginning') }}</h3>
                        <p class="text-muted small mb-0">{{ $m('about_story_1_text', 'Redefining the eCommerce landscape with a vision to connect buyers and sellers seamlessly.') }}</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="h-100">
                        <img src="{{ $imgUrl('about_story_2_image', '400x300') }}" alt="{{ e($m('about_story_2_heading')) }}" class="img-fluid rounded-3 w-100 mb-3 object-fit-cover" style="height: 220px;" onerror="this.onerror=null; this.src='{{ route('placeholder', ['size' => '400x300']) }}?v=2';">
                        <h3 class="h5 fw-bold mb-2">{{ $m('about_story_2_heading', 'Overcoming Challenges') }}</h3>
                        <p class="text-muted small mb-0">{{ $m('about_story_2_text', 'Navigating obstacles with innovation to build a reliable and secure marketplace.') }}</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="h-100">
                        <img src="{{ $imgUrl('about_story_3_image', '400x300') }}" alt="{{ e($m('about_story_3_heading')) }}" class="img-fluid rounded-3 w-100 mb-3 object-fit-cover" style="height: 220px;" onerror="this.onerror=null; this.src='{{ route('placeholder', ['size' => '400x300']) }}?v=2';">
                        <h3 class="h5 fw-bold mb-2">{{ $m('about_story_3_heading', 'Our Future Vision') }}</h3>
                        <p class="text-muted small mb-0">{{ $m('about_story_3_text', 'Continuing to innovate and grow, building a sustainable eCommerce ecosystem for the future.') }}</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- Our Mission & Vision --}}
        <section class="mb-5 pb-lg-5" aria-labelledby="about-mission-heading">
            <h2 id="about-mission-heading" class="text-center about-section-title display-6 mb-3">{{ $m('about_mission_title', 'Our Mission & Vision') }}</h2>
            <p class="text-center text-muted mb-5 mx-auto" style="max-width: 720px;">{{ $m('about_mission_intro', 'We are committed to revolutionizing eCommerce by creating a seamless, secure, and customer-centric marketplace.') }}</p>
            <div class="row g-4 mb-4">
                <div class="col-lg-6">
                    <div class="row align-items-center g-3">
                        <div class="col-md-5">
                            <img src="{{ $imgUrl('about_mission_1_image', '400x320') }}" alt="" class="img-fluid rounded-3 w-100 object-fit-cover" style="height: 240px;" onerror="this.onerror=null; this.src='{{ route('placeholder', ['size' => '400x320']) }}?v=2';">
                        </div>
                        <div class="col-md-7">
                            <p class="mb-0 text-muted">{{ $m('about_mission_1_text', 'We make online shopping easy and reliable, empower sellers, and foster a trusted marketplace for everyone.') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="row align-items-center g-3">
                        <div class="col-md-5">
                            <img src="{{ $imgUrl('about_mission_2_image', '400x320') }}" alt="" class="img-fluid rounded-3 w-100 object-fit-cover" style="height: 240px;" onerror="this.onerror=null; this.src='{{ route('placeholder', ['size' => '400x320']) }}?v=2';">
                        </div>
                        <div class="col-md-7">
                            <p class="mb-0 text-muted">{{ $m('about_mission_2_text', 'We revolutionize digital commerce by integrating cutting-edge technology, expanding globally, and connecting buyers and sellers worldwide.') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Testimonials & Success Stories --}}
        <section class="mb-4" aria-labelledby="about-testimonial-heading">
            <h2 id="about-testimonial-heading" class="text-center about-section-title display-6 mb-2">{{ $m('about_testimonial_title', 'Testimonials & Success Stories') }}</h2>
            <p class="text-center text-muted mb-4 mx-auto" style="max-width: 720px;">{{ $m('about_testimonial_subtitle', 'From a Bold Vision to a Trusted Marketplace: How we were built to transform the future of eCommerce.') }}</p>
            <div class="row g-4 justify-content-center">
                <div class="col-md-6 col-lg-5">
                    <div class="about-testimonial-card d-flex gap-3">
                        <img src="{{ $imgUrl('about_testimonial_1_avatar', '80x80') }}" alt="{{ e($m('about_testimonial_1_name')) }}" class="about-testimonial-avatar flex-shrink-0" onerror="this.onerror=null; this.src='{{ route('placeholder', ['size' => '80x80']) }}?v=2';">
                        <div class="flex-grow-1">
                            <h3 class="h6 fw-bold mb-1">{{ $m('about_testimonial_1_name', 'Bill Gates') }}</h3>
                            <p class="small text-muted mb-2">{{ $m('about_testimonial_1_role', 'CIO') }}</p>
                            <p class="small mb-0 fst-italic">"{{ $m('about_testimonial_1_quote', 'A game-changing platform for modern commerce.') }}"</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-5">
                    <div class="about-testimonial-card d-flex gap-3">
                        <img src="{{ $imgUrl('about_testimonial_2_avatar', '80x80') }}" alt="{{ e($m('about_testimonial_2_name')) }}" class="about-testimonial-avatar flex-shrink-0" onerror="this.onerror=null; this.src='{{ route('placeholder', ['size' => '80x80']) }}?v=2';">
                        <div class="flex-grow-1">
                            <h3 class="h6 fw-bold mb-1">{{ $m('about_testimonial_2_name', 'John Anderson') }}</h3>
                            <p class="small text-muted mb-2">{{ $m('about_testimonial_2_role', 'CTO') }}</p>
                            <p class="small mb-0 fst-italic">"{{ $m('about_testimonial_2_quote', 'Innovation and reliability at its best.') }}"</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </div>
</div>
@endsection
