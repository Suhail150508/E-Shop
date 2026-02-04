@extends('layouts.customer')

@section('title', __('My Wishlist'))

@section('account_content')
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white border-bottom-0 py-3 px-4">
            <h5 class="mb-0 fw-bold">{{ __('My Wishlist') }}</h5>
        </div>
        <div class="card-body p-4">
            @if($wishlistItems->isEmpty())
                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="fa-regular fa-heart fa-3x text-muted opacity-25"></i>
                    </div>
                    <p class="text-muted mb-4">{{ __('Your wishlist is empty.') }}</p>
                    <a href="{{ route('shop.index') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">{{ __('Continue Shopping') }}</a>
                </div>
            @else
                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-4 g-4">
                    @foreach($wishlistItems as $product)
                        <div class="col">
                            <div class="card h-100 border-0 shadow-sm product-card position-relative overflow-hidden">
                                <!-- Discount Badge -->
                                @if($product->has_discount)
                                    @php
                                        $discountPercentage = round((($product->price - $product->final_price) / $product->price) * 100);
                                    @endphp
                                    <div class="position-absolute top-0 end-0 m-2 z-1">
                                        <span class="badge bg-danger rounded-2">{{ $discountPercentage }}%</span>
                                    </div>
                                @endif

                                <!-- Remove/Like Button -->
                                <div class="position-absolute top-0 start-0 m-2 z-1">
                                     <form action="{{ route('wishlist.toggle', $product->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-white btn-sm rounded-circle shadow-sm d-flex align-items-center justify-content-center text-danger bg-white wishlist-remove-btn" data-bs-toggle="tooltip" title="{{ __('Remove from Wishlist') }}">
                                            <i class="fa-regular fa-heart"></i>
                                        </button>
                                    </form>
                                </div>

                                <!-- Product Image -->
                                <a href="{{ route('shop.product.show', $product->slug) }}" class="d-block text-center p-4 bg-white">
                                    @if($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="img-fluid wishlist-img">
                                    @else
                                        <div class="d-flex align-items-center justify-content-center bg-light rounded wishlist-img-placeholder">
                                            <i class="fa-regular fa-image fa-3x text-muted opacity-50"></i>
                                        </div>
                                    @endif
                                </a>

                                <div class="card-body d-flex flex-column p-3 pt-0">
                                    <!-- Rating -->
                                    <div class="mb-2">
                                        @php
                                            $rating = $product->approvedReviews->avg('rating') ?? 0;
                                            $reviewCount = $product->approvedReviews->count() ?? 0;
                                        @endphp
                                        <div class="small text-warning d-flex align-items-center">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= round($rating))
                                                    <i class="fa-solid fa-star fa-xs"></i>
                                                @else
                                                    <i class="fa-regular fa-star fa-xs text-muted"></i>
                                                @endif
                                            @endfor
                                            <span class="text-muted ms-1 address-font-xs">({{ $reviewCount }})</span>
                                        </div>
                                    </div>

                                    <!-- Product Name -->
                                    <h6 class="card-title mb-1 wishlist-product-title">
                                        <a href="{{ route('shop.product.show', $product->slug) }}" class="text-decoration-none text-dark text-truncate-2">{{ $product->name }}</a>
                                    </h6>

                                    <!-- Stock Status -->
                                     <div class="mb-3">
                                        @if($product->stock > 0)
                                            <span class="text-success small fw-medium wishlist-stock-status">{{ __('In stock') }}</span>
                                        @else
                                            <span class="text-danger small fw-medium wishlist-stock-status">{{ __('Out of stock') }}</span>
                                        @endif
                                    </div>

                                    <!-- Price & Action -->
                                    <div class="mt-auto d-flex justify-content-between align-items-center">
                                        <div class="line-height-1">
                                            @if($product->has_discount)
                                                <div class="fw-bold text-primary mb-0">${{ number_format($product->final_price, 2) }}</div>
                                                <small class="text-muted text-decoration-line-through address-font-xs">${{ number_format($product->price, 2) }}</small>
                                            @else
                                                <div class="fw-bold text-primary mb-0">${{ number_format($product->price, 2) }}</div>
                                            @endif
                                        </div>
                                        
                                        <form action="{{ route('cart.store', $product->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-primary btn-sm rounded px-3" {{ $product->stock <= 0 ? 'disabled' : '' }}>
                                                <i class="fa-solid fa-cart-shopping me-1"></i> {{ __('Add') }}
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection
