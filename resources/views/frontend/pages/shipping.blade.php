@extends('layouts.frontend')

@section('content')
@include('frontend.partials.breadcrumb', ['title' => $page->title ?? 'Shipping Information', 'bgImage' => $page->image ?? null])

<div class="container py-5">
    @if(isset($page) && $page->content)
        {!! $page->content !!}
    @else
        <div class="alert alert-info">{{ __('Shipping Information content is coming soon.') }}</div>
    @endif
</div>

@endsection
