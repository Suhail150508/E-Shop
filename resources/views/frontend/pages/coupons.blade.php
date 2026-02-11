@extends('layouts.frontend')

@section('content')
@include('frontend.partials.breadcrumb', ['title' => __('common.coupons'), 'bgImage' => getImageOrPlaceholder(null, '1920x400')])

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            @if($coupons->isNotEmpty())
                <div class="row g-4">
                    @foreach($coupons as $coupon)
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 border-2 border-dashed {{ $coupon->type === 'percent' ? 'border-primary' : 'border-success' }} bg-light">
                                <div class="card-body text-center p-4">
                                    @if($coupon->type === 'percent')
                                        <h3 class="display-6 fw-bold text-primary mb-2">{{ number_format($coupon->value, 0) }}% {{ __('common.off') }}</h3>
                                    @else
                                        <h3 class="display-6 fw-bold text-success mb-2">{{ default_currency()?->symbol ?? '$' }}{{ number_format($coupon->value, 0) }} {{ __('common.off') }}</h3>
                                    @endif
                                    @if($coupon->min_spend > 0)
                                        <p class="text-muted small mb-2">{{ __('common.on_orders_over') }} {{ default_currency()?->symbol ?? '$' }}{{ number_format($coupon->min_spend, 0) }}</p>
                                    @endif
                                    <div class="d-inline-block bg-white px-3 py-2 rounded-3 border mb-2">
                                        <code class="fs-5 text-dark fw-bold">{{ $coupon->code }}</code>
                                    </div>
                                    @if($coupon->expiry_date)
                                        <p class="text-muted small mb-0">{{ __('common.valid_until') }} {{ $coupon->expiry_date->format('M d, Y') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <p class="lead text-muted mb-0">{{ __('common.no_coupons_available') }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
