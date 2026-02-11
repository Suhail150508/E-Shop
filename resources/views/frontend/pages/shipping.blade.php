@extends('layouts.frontend')

@section('content')
@include('frontend.partials.breadcrumb', ['title' => $page->translate('title') ?? __('common.shipping'), 'bgImage' => $page->image ?? null])

<div class="container py-5">
@if(isset($page) && ($page->translate('content') ?? $page->content))
        {!! $page->translate('content') ?? $page->content !!}
    @else
        <div class="alert alert-info">{{ __('common.shipping_coming_soon') }}</div>
    @endif
</div>

@endsection
