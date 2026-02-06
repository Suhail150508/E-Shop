@extends('layouts.frontend')

@section('content')
@include('frontend.partials.breadcrumb', ['title' => $page->title ?? 'About Us', 'bgImage' => $page->image ?? null])

@if(isset($page) && $page->content)
    {!! $page->content !!}
@else
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <img src="{{ getImageOrPlaceholder(null, '600x400') }}" alt="About Us" class="img-fluid rounded shadow-sm" onerror="this.src='{{ asset('backend/images/placeholder.svg') }}'">
            </div>
            <div class="col-lg-6 ps-lg-5">
                <h2 class="mb-4">Our Story</h2>
                <p class="lead mb-4">We are a team of passionate individuals dedicated to bringing you the best products.</p>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
            </div>
        </div>
    </div>
@endif

@endsection