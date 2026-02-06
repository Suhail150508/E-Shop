@extends('layouts.frontend')



@section('content')
    <div class="wishlist-page-wrapper">
        <div class="container wishlist-page">
            <div class="wishlist-page-header">
                <div>
                    <div class="wishlist-breadcrumb">
                        <a href="{{ route('home') }}">{{ __('Home') }}</a>
                        <span> / </span>
                        <span>{{ __('Wishlist') }}</span>
                    </div>
                    <h1 class="wishlist-title">{{ __('Wishlist') }}</h1>
                    <p class="wishlist-subtitle">{{ __('Save your favorite items and move them to cart when ready.') }}</p>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            @if($products->isEmpty())
                                <div class="wishlist-empty-message">
                                    <p class="mb-3">{{ __('Your wishlist is empty.') }}</p>
                                    <a href="{{ route('shop.index') }}" class="btn btn-outline-secondary">
                                        {{ __('Browse Products') }}
                                    </a>
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table align-middle wishlist-table mb-0">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Product') }}</th>
                                                <th class="text-center">{{ __('Price') }}</th>
                                                <th class="text-center">{{ __('Stock') }}</th>
                                                <th class="text-end">{{ __('Actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($products as $product)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center wishlist-product-info">
                                                            <img src="{{ getImageOrPlaceholder($product->image_url, '80x80') }}" alt="{{ $product->name }}" class="rounded me-3 object-fit-cover" width="80" height="80" onerror="this.src='{{ asset('backend/images/placeholder.svg') }}'">
                                                            <div class="wishlist-product-name">
                                                                <a href="{{ route('shop.product.show', $product) }}">
                                                                    {{ $product->name }}
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        @if($product->has_discount)
                                                            <div class="wishlist-price-current">{{ format_price((float) $product->final_price) }}</div>
                                                            <div class="text-muted text-decoration-line-through small wishlist-price-old">{{ format_price((float) $product->price) }}</div>
                                                        @else
                                                            <div class="fw-semibold">{{ format_price((float) $product->price) }}</div>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @if($product->stock > 0)
                                                            <span class="text-success">{{ $product->stock }}</span>
                                                        @else
                                                            <span class="text-danger">{{ __('Out of stock') }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-end">
                                                        <form action="{{ route('wishlist.move-to-cart', $product) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-primary">
                                                                {{ __('Add to Cart') }}
                                                            </button>
                                                        </form>
                                                        <form action="{{ route('wishlist.toggle', $product) }}" method="POST" class="d-inline ms-1 remove-from-wishlist-form">
                                                            @csrf
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
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
