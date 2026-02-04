@extends('layouts.frontend')

@section('content')
@include('frontend.partials.breadcrumb', ['title' => $page->title ?? 'Privacy Policy', 'bgImage' => $page->image ?? null])

<div class="container py-5">
    @if(isset($page) && $page->content)
        {!! $page->content !!}
    @else
        <div class="alert alert-info">{{ __('Privacy Policy content is coming soon.') }}</div>
    @endif
</div>

@endsection
