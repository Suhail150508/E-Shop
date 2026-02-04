@extends('layouts.frontend')

@section('content')
@include('frontend.partials.breadcrumb', ['title' => $page->title ?? 'Terms & Conditions', 'bgImage' => $page->image ?? null])

<div class="container py-5">
    @if(isset($page) && $page->content)
        {!! $page->content !!}
    @else
        <div class="alert alert-info">{{ __('Terms & Conditions content is coming soon.') }}</div>
    @endif
</div>

@endsection
