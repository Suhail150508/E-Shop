@extends('layouts.frontend')



@section('content')
    <div class="cart-page-wrapper">
        <div class="container cart-page">
            @php
                $codGateway = app(\Modules\PaymentGateway\App\Services\Payments\CodPaymentService::class);
                $stripeGateway = app(\Modules\PaymentGateway\App\Services\Payments\StripePaymentService::class);
                $walletGateway = app(\Modules\PaymentGateway\App\Services\Payments\WalletPaymentService::class);
                
                $codEnabled = $codGateway->isEnabled();
                $stripeEnabled = $stripeGateway->isEnabled();
                $walletEnabled = $walletGateway->isEnabled();
                
                $defaultGateway = $codEnabled ? 'cod' : ($stripeEnabled ? 'stripe' : ($walletEnabled ? 'wallet' : null));
            @endphp
            <div class="cart-page-header">
                <div>
                    <div class="cart-breadcrumb">
                        <a href="{{ route('home') }}">{{ __('Home') }}</a>
                        <span> / </span>
                        <span>{{ setting('cart_breadcrumb', __('cart.cart')) }}</span>
                    </div>
                    <h1 class="cart-title">{{ setting('cart_title', __('cart.shopping_cart')) }}</h1>
                    <p class="cart-subtitle">{{ setting('cart_subtitle', __('Review your items and proceed to secure checkout.')) }}</p>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card cart-items-card border-0 shadow-sm">
                        <div class="card-body">
                            @if($items->isEmpty())
                                <div class="cart-empty-message">
                                    <p class="mb-3">{{ __('Your cart is empty.') }}</p>
                                    <a href="{{ route('shop.index') }}" class="btn btn-outline-secondary">
                                        {{ __('Browse Products') }}
                                    </a>
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table align-middle cart-items-table mb-0">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Product') }}</th>
                                                <th class="text-center">{{ __('Price') }}</th>
                                                <th class="text-center">{{ __('Quantity') }}</th>
                                                <th class="text-end">{{ __('Subtotal') }}</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($items as $item)
                                                @php
                                                    $unit = $item['discount_price'] !== null && $item['discount_price'] > 0 && $item['discount_price'] < $item['price']
                                                        ? $item['discount_price']
                                                        : $item['price'];
                                                    $lineTotal = $unit * $item['quantity'];
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center cart-product-info">
                                                            <img src="{{ getImageOrPlaceholder($item['image'], '80x80') }}" alt="{{ $item['name'] }}" class="rounded me-3 object-fit-cover">
                                                            <div class="cart-product-name">
                                                                <a href="{{ route('shop.product.show', $item['slug']) }}">
                                                                    {{ $item['name'] }}
                                                                </a>
                                                                @if(isset($item['options']['color']))
                                                                    <div class="small text-muted mt-1">
                                                                        {{ __('Color') }}: {{ $item['options']['color'] }}
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        @if($item['discount_price'] !== null && $item['discount_price'] > 0 && $item['discount_price'] < $item['price'])
                                                            <div class="cart-price-current">{{ format_price((float) $item['discount_price']) }}</div>
                                                            <div class="text-muted text-decoration-line-through small cart-price-old">{{ format_price((float) $item['price']) }}</div>
                                                        @else
                                                            <div class="fw-semibold">{{ format_price((float) $item['price']) }}</div>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        <form action="{{ route('cart.update', $item['row_id']) }}" method="POST" class="d-inline-flex align-items-center justify-content-center update-cart-form">
                                                            @csrf
                                                            @method('PUT')
                                                            <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="0" class="form-control form-control-sm cart-quantity-input">
                                                            <button type="submit" class="btn btn-sm btn-outline-secondary ms-2">
                                                                {{ __('Update') }}
                                                            </button>
                                                        </form>
                                                    </td>
                                                    <td class="text-end">
                                                        {{ format_price((float) $lineTotal) }}
                                                    </td>
                                                    <td class="text-end">
                                                        <form action="{{ route('cart.destroy', $item['row_id']) }}" method="POST" class="d-inline remove-from-cart-form">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                                {{ __('Remove') }}
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="cart-footer-actions">
                                    <form action="{{ route('cart.clear') }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                            {{ __('Clear Cart') }}
                                        </button>
                                    </form>
                                    <a href="{{ route('shop.index') }}" class="btn btn-outline-secondary btn-sm">
                                        {{ __('Continue Shopping') }}
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm order-summary-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="order-summary-title">{{ __('Order Summary') }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="order-summary-label">{{ __('Subtotal') }}</span>
                                <span class="fw-semibold">{{ format_price((float) ($subtotal ?? 0)) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-4">
                                <span class="order-summary-label">{{ __('Total') }}</span>
                                <span class="order-summary-total">{{ format_price((float) ($subtotal ?? 0)) }}</span>
                            </div>
                            @if($codEnabled || $stripeEnabled || $walletEnabled)
                                <a href="{{ route('checkout.shipping') }}" class="btn btn-primary w-100 py-2 fw-bold">
                                    {{ __('checkout.checkout') }}
                                </a>
                            @else
                                <div class="alert alert-warning mb-0 text-center">
                                    {{ __('No payment methods available.') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
