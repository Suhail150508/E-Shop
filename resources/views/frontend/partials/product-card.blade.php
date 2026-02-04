<div class="col-6 col-md-4 col-lg-3">
    <div class="product-card h-100 position-relative">
        @if(isset($badge) && $badge)
            <div class="product-badge {{ $badge === 'SALE' ? 'bg-danger' : ($badge === 'NEW' ? 'bg-success' : 'bg-warning text-dark') }}">
                @if($badge === 'SALE' && $product->discount_price && $product->price > 0 && $product->discount_price < $product->price)
                    -{{ round((($product->price - $product->discount_price) / $product->price) * 100) }}%
                @else
                    {{ __($badge) }}
                @endif
            </div>
        @elseif($product->is_flash_sale)
            <div class="product-badge bg-danger">
                @if($product->discount_price && $product->price > 0 && $product->discount_price < $product->price)
                    -{{ round((($product->price - $product->discount_price) / $product->price) * 100) }}%
                @else
                    {{ __('common.sale_badge') }}
                @endif
            </div>
        @elseif($product->is_featured)
            <div class="product-badge bg-warning text-dark">{{ __('common.hot') }}</div>
        @endif

        <div class="product-image-wrapper">
            <a href="{{ route('shop.product.show', $product->slug) }}">
                <img src="{{ $product->image_url ?? 'https://placehold.co/500x450' }}" 
                     alt="{{ $product->name }}" 
                     class="product-img"
                     onerror="this.src='https://placehold.co/500x450'">
            </a>
            <div class="product-actions">
                <form action="{{ route('wishlist.toggle', $product->id) }}" method="POST" class="d-block add-to-wishlist-form">
                    @csrf
                    <button type="submit" class="btn-action" title="{{ __('common.add_to_wishlist') }}">
                        <i class="far fa-heart"></i>
                    </button>
                </form>
                <a href="{{ route('shop.product.show', $product->slug) }}" class="btn-action" title="{{ __('common.view_details') }}">
                    <i class="far fa-eye"></i>
                </a>
            </div>
        </div>
        
        <div class="product-info p-3">
            @if($product->category)
                <div class="product-category text-muted small mb-1">{{ $product->category->name }}</div>
            @endif
            
            <h3 class="product-title h6 mb-2">
                <a href="{{ route('shop.product.show', $product->slug) }}" class="text-decoration-none text-dark">
                    {{ Str::limit($product->name, 40) }}
                </a>
            </h3>
            
            <div class="product-rating mb-2">
                @php
                    $rating = $product->rating ?? 0; // Assuming rating accessor or column exists
                    $fullStars = floor($rating);
                    $halfStar = $rating - $fullStars >= 0.5;
                    $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
                @endphp
                <span class="text-warning">
                    @for($i = 0; $i < $fullStars; $i++) <i class="fas fa-star"></i> @endfor
                    @if($halfStar) <i class="fas fa-star-half-alt"></i> @endif
                    @for($i = 0; $i < $emptyStars; $i++) <i class="far fa-star"></i> @endfor
                </span>
                <span class="text-muted small ms-1">({{ $product->approved_reviews_count ?? 0 }})</span>
            </div>
            
            <div class="product-price d-flex align-items-center gap-2">
                @if($product->discount_price && $product->discount_price < $product->price)
                    <span class="fw-bold text-danger">{{ format_price((float) $product->discount_price) }}</span>
                    <span class="text-muted text-decoration-line-through small">{{ format_price((float) $product->price) }}</span>
                @else
                    <span class="fw-bold">{{ format_price((float) $product->price) }}</span>
                @endif
                @if($product->unit)
                    <span class="text-muted small">/ {{ $product->unit->name }}</span>
                @endif
            </div>
            
            <div class="mt-3">
                <form action="{{ route('cart.store', $product->id) }}" method="POST" class="add-to-cart-form">
                    @csrf
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" class="btn btn-sm btn-outline-primary w-100">
                        <i class="fas fa-shopping-cart me-1"></i> {{ __('common.add_to_cart') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

