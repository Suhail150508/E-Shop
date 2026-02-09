@extends('layouts.frontend')

@section('content')
@include('frontend.partials.breadcrumb', ['title' => (isset($page) && $page ? $page->translate('title') : null) ?? __('common.coupons'), 'bgImage' => optional($page)->image ?? getImageOrPlaceholder(null, '1920x400')])

@if(isset($page) && ($page->translate('content') ?? $page->content))
    {!! $page->translate('content') ?? $page->content !!}
@else
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">

            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card h-100 border-2 border-dashed border-primary bg-light">
                        <div class="card-body text-center p-5">
                            <h3 class="display-4 fw-bold text-primary mb-3">20% OFF</h3>
                            <p class="lead mb-4">On your first order over $50</p>
                            <div class="d-inline-block bg-white px-4 py-2 rounded-3 border mb-3">
                                <code class="fs-4 text-dark fw-bold">WELCOME20</code>
                            </div>
                            <p class="text-muted small mb-0">Valid for new customers only.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100 border-2 border-dashed border-success bg-light">
                        <div class="card-body text-center p-5">
                            <h3 class="display-4 fw-bold text-success mb-3">FREE SHIP</h3>
                            <p class="lead mb-4">Free shipping on all orders</p>
                            <div class="d-inline-block bg-white px-4 py-2 rounded-3 border mb-3">
                                <code class="fs-4 text-dark fw-bold">FREESHIP</code>
                            </div>
                            <p class="text-muted small mb-0">Min. order value $100.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
