@extends('layouts.frontend')

@section('title', $product->meta_title ?: $product->translate('name'))

@push('meta')
    @php
        $metaDesc = $product->meta_description
            ? Str::limit(strip_tags($product->meta_description), 160)
            : Str::limit(strip_tags($product->translate('description') ?? ''), 160);
    @endphp
    <meta name="description" content="{{ e($metaDesc) }}">
    @if(!empty($product->meta_keywords))
    <meta name="keywords" content="{{ e($product->meta_keywords) }}">
    @endif
@endpush

    @push('styles')
        <link rel="stylesheet" href="{{ asset('frontend/css/product-details.css') }}">
        <link rel="stylesheet" href="{{ asset('frontend/css/shop.css') }}">
    @endpush

@section('content')

    <div class="product-page-wrapper">
        <div class="container">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb mb-0 small text-muted">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted">{{ __('common.home') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('shop.index') }}" class="text-decoration-none text-muted">{{ __('common.shop') }}</a></li>
                    @if($product->category)
                        <li class="breadcrumb-item"><a href="{{ route('shop.category', $product->category->slug) }}" class="text-decoration-none text-muted">{{ $product->category->name }}</a></li>
                    @endif
                    <li class="breadcrumb-item active text-dark" aria-current="page">{{ $product->translate('name') }}</li>
                </ol>
            </nav>

            <div class="bg-white rounded-4 shadow-sm overflow-hidden">
                <div class="row g-0">
                    <!-- Product Gallery -->
                    <div class="col-lg-6 border-end-lg">
                        <div class="p-4 p-xl-5 h-100 d-flex flex-column position-relative">
                            @php
                                $galleryImages = collect();
                                $galleryImages->push(['url' => $product->image_url]);
                                if($product->images) {
                                    foreach ($product->images as $image) {
                                        $galleryImages->push(['url' => $image->image_url]);
                                    }
                                }
                                $mainImage = $galleryImages->first()['url'];
                            @endphp

                            <div class="product-badges position-absolute top-0 start-0 m-4 z-2 d-flex flex-column gap-2">
                                @if($product->stock <= 0)
                                     <span class="badge bg-secondary text-white px-3 py-2 rounded-pill text-uppercase shadow-sm">{{ __('common.out_of_stock') }}</span>
                                @elseif($product->is_flash_sale)
                                    <span class="badge bg-danger text-white px-3 py-2 rounded-pill text-uppercase shadow-sm">{{ __('common.flash_sale') }}</span>
                                @elseif($product->has_discount)
                                    <span class="badge bg-primary text-white px-3 py-2 rounded-pill text-uppercase shadow-sm">{{ __('common.sale') }} -{{ $product->discount_percentage }}%</span>
                                @elseif($product->is_featured)
                                    <span class="badge bg-info text-white px-3 py-2 rounded-pill text-uppercase shadow-sm">{{ __('common.hot') }}</span>
                                @elseif($product->created_at->gt(now()->subDays(30)))
                                    <span class="badge bg-success text-white px-3 py-2 rounded-pill text-uppercase shadow-sm">{{ __('common.new') }}</span>
                                @endif
                            </div>

                            <div class="main-image-wrapper mb-4 d-flex align-items-center justify-content-center flex-grow-1" id="zoomContainer">
                                <img src="{{ getImageOrPlaceholder($mainImage, '800x800') }}" 
                                     alt="{{ $product->translate('name') }}" 
                                     id="mainProductImage" 
                                     class="img-fluid rounded-3 main-product-image"
                                     onerror="this.src='{{ asset('backend/images/placeholder.svg') }}'">
                            </div>

                            <!-- Controls -->
                            <div class="position-absolute bottom-0 end-0 m-4 z-2 d-flex gap-2">
                                <button class="btn btn-light rounded-circle shadow-sm p-2 d-flex align-items-center justify-content-center btn-zoom-toggle" id="toggleZoomBtn" title="{{ __('common.toggle_zoom') }}">
                                    <i class="fas fa-search-plus text-muted fs-5"></i>
                                </button>
                                @if($galleryImages->count() > 1)
                                    <button class="btn btn-light rounded-pill shadow-sm px-3 py-2 d-flex align-items-center gap-2" id="toggle360Btn" title="{{ __('common.360_view') }}">
                                        <i class="fas fa-cube text-muted"></i> <span class="small fw-bold text-muted">{{ __('common.360_deg') }}</span>
                                    </button>
                                @endif
                            </div>

                            @if($galleryImages->count() > 1)
                                <div class="gallery-thumbs d-flex gap-2 justify-content-center mt-auto overflow-auto py-2">
                                    @foreach($galleryImages as $index => $image)
                                        <button class="thumb-btn border-0 p-1 rounded-3 bg-white shadow-sm {{ $loop->first ? 'active ring-2 ring-primary' : '' }}" 
                                                onclick="updateMainImage(this, '{{ $image['url'] }}')"
                                                data-index="{{ $index }}"
                                                data-image-url="{{ $image['url'] }}">
                                            <img src="{{ getImageOrPlaceholder($image['url'], '150x150') }}" 
                                                 alt="{{ __('common.product_thumbnail') }} {{ $index + 1 }}"
                                                 class="w-100 h-100 rounded-2 thumb-img"
                                                 onerror="this.src='{{ asset('backend/images/placeholder.svg') }}'">
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Product Info -->
                    <div class="col-lg-6">
                        <div class="p-4 p-xl-5 d-flex flex-column h-100">
                            <div class="mb-2">
                                @if($product->brand)
                                    <a href="{{ route('shop.index', ['brand' => $product->brand->slug]) }}" class="text-uppercase fw-bold text-primary text-decoration-none small tracking-wide">
                                        {{ $product->brand->name }}
                                    </a>
                                @endif
                            </div>

                            <h1 class="fw-bold text-dark mb-3 display-6 lh-sm">{{ $product->translate('name') }}</h1>

                            <div class="d-flex align-items-center gap-3 mb-4 border-bottom pb-4">
                                <div class="d-flex align-items-center gap-1 text-warning">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= round($product->approved_reviews_avg_rating ?? 0))
                                            <i class="fas fa-star"></i>
                                        @else
                                            <i class="far fa-star text-muted opacity-25"></i>
                                        @endif
                                    @endfor
                                    <a href="#reviews-tab" onclick="document.getElementById('reviews-tab').click()" class="ms-2 text-muted small text-decoration-none hover-primary">
                                        ({{ $product->approved_reviews_count ?? 0 }} {{ __('common.reviews') }})
                                    </a>
                                </div>
                                
                                <span class="text-muted opacity-25">|</span>
                                
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fas fa-check-circle {{ $product->stock > 0 ? 'text-success' : 'text-danger' }}"></i>
                                    <span class="{{ $product->stock > 0 ? 'text-success' : 'text-danger' }} fw-medium small text-uppercase">
                                        @if($product->stock > 0)
                                            {{ __('common.in_stock_with_count', ['count' => $product->stock]) }}
                                        @else
                                            {{ __('common.out_of_stock') }}
                                        @endif
                                    </span>
                                </div>
                            </div>

                            <div class="mb-4">
                                @if($product->has_discount)
                                    <div class="d-flex align-items-end gap-3">
                                        <h2 class="mb-0 text-primary fw-bold display-5">{{ format_price((float) $product->final_price) }}</h2>
                                        <span class="text-muted text-decoration-line-through fs-4 mb-1">{{ format_price((float) $product->price) }}</span>
                                    </div>
                                    <p class="text-danger small mt-1 mb-0 font-monospace">
                                        <i class="fas fa-tag me-1"></i> {{ __('common.save') }} {{ $product->discount_percentage }}%
                                    </p>
                                @else
                                    <h2 class="mb-0 text-primary fw-bold display-5">{{ format_price((float) $product->price) }}</h2>
                                @endif
                            </div>

                            <div class="product-short-desc text-muted mb-4 leading-relaxed">
                                {{ Str::limit(strip_tags($product->short_description ?? $product->translate('description')), 250) }}
                            </div>

                            <div class="mt-auto">
                                <form action="{{ route('cart.store', $product->id) }}" method="POST" class="add-to-cart-form">
                                    @csrf
                                    
                                    @if($product->color_objects->isNotEmpty())
                                    <div class="mb-4">
                                        <label class="form-label fw-bold d-block mb-2 text-uppercase small text-muted">{{ __('common.colors') }}</label>
                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach($product->color_objects as $color)
                                                <input type="radio" class="btn-check" name="color" id="color_{{ $color->id }}" value="{{ $color->name }}" required>
                                                <label class="btn btn-outline-light d-flex align-items-center gap-2 p-1 pe-3 rounded-pill border color-option-label" 
                                                       for="color_{{ $color->id }}">
                                                    <span class="rounded-circle shadow-sm border color-swatch" style="background-color: {{ $color->code }};"></span>
                                                    <span class="small fw-bold text-dark">{{ $color->name }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                    @elseif(!empty($product->colors) && is_array($product->colors))
                                    <div class="mb-4">
                                        <label class="form-label fw-bold d-block mb-2 text-uppercase small text-muted">{{ __('common.colors') }}</label>
                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach($product->colors as $index => $color)
                                                <input type="radio" class="btn-check" name="color" id="color_idx_{{ $index }}" value="{{ $color }}" required>
                                                <label class="btn btn-outline-light border d-flex align-items-center gap-2 p-2 rounded-3 cursor-pointer" for="color_idx_{{ $index }}">
                                                    <span class="rounded-circle shadow-sm border color-swatch-sm" style="background-color: {{ $color }};"></span>
                                                    <span class="small fw-medium text-dark">{{ $color }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif

                                    @if($product->size_objects->isNotEmpty())
                                    <div class="mb-4">
                                        <label class="form-label fw-bold d-block mb-2 text-uppercase small text-muted">{{ __('common.sizes') }}</label>
                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach($product->size_objects as $size)
                                                <input type="radio" class="btn-check" name="size" id="size_{{ $size->id }}" value="{{ $size->name }}" required>
                                                <label class="btn btn-outline-light d-flex align-items-center gap-2 p-2 pe-3 rounded-pill border size-option-label" for="size_{{ $size->id }}">
                                                    <span class="small fw-bold text-dark">{{ $size->name }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                    @elseif(!empty($product->sizes) && is_array($product->sizes))
                                    <div class="mb-4">
                                        <label class="form-label fw-bold d-block mb-2 text-uppercase small text-muted">{{ __('common.sizes') }}</label>
                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach($product->sizes as $index => $sizeName)
                                                <input type="radio" class="btn-check" name="size" id="size_idx_{{ $index }}" value="{{ $sizeName }}" required>
                                                <label class="btn btn-outline-light border d-flex align-items-center gap-2 p-2 rounded-3 cursor-pointer" for="size_idx_{{ $index }}">
                                                    <span class="small fw-medium text-dark">{{ $sizeName }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif

                                    <div class="action-group mb-4">
                                        <div class="d-flex flex-wrap gap-3">
                                            <div class="qty-control d-flex align-items-center border border-2 rounded-pill overflow-hidden bg-light qty-control-wrapper">
                                                <button type="button" class="btn border-0 px-3 text-muted hover-dark" onclick="decreaseQty()">
                                                    <i class="fas fa-minus small"></i>
                                                </button>
                                                <input type="number" name="quantity" value="1" min="1" max="{{ $product->stock }}" class="form-control border-0 bg-transparent text-center fw-bold p-0 fs-5" id="qtyInput">
                                                <button type="button" class="btn border-0 px-3 text-muted hover-dark" onclick="increaseQty()">
                                                    <i class="fas fa-plus small"></i>
                                                </button>
                                            </div>
        
                                            <button type="submit" class="btn btn-primary rounded-pill px-5 py-3 fw-bold shadow-sm flex-grow-1 text-uppercase tracking-wide" {{ $product->stock <= 0 ? 'disabled' : '' }}>
                                                <i class="fas fa-shopping-bag me-2"></i>
                                                {{ __('common.add_to_cart') }}
                                            </button>
                                            
                                            <button type="button" class="btn btn-outline-secondary rounded-circle shadow-sm d-flex align-items-center justify-content-center btn-wishlist" onclick="document.getElementById('wishlistForm').requestSubmit()" title="{{ __('common.add_to_wishlist') }}">
                                                <i class="far fa-heart fs-5"></i>
                                            </button>
                                            @if($product->is_tryable)
                                            <button type="button" class="btn btn-outline-primary rounded-pill px-4 py-3 fw-bold shadow-sm try-on-btn" data-id="{{ $product->id }}" data-image="{{ $product->image_url }}" title="{{ __('common.virtual_try_on') }}">
                                                <i class="fas fa-user-check me-2"></i>
                                                <span>{{ __('common.try_now') }}</span>
                                            </button>
                                            @endif
                                        </div>
                                    </div>
                                </form>
                                
                                <form id="wishlistForm" action="{{ route('wishlist.toggle', $product->id) }}" method="POST" class="d-none">
                                    @csrf
                                </form>

                                    <div class="p-3 bg-light rounded-3">
                                        <div class="mb-3">
                                            <span class="d-block text-muted text-uppercase fw-bold mb-1 meta-label">{{ __('common.sku') }}</span>
                                            <span class="fw-bold text-dark font-monospace">{{ $product->sku }}</span>
                                        </div>
                                        <div class="mb-3">
                                            <span class="d-block text-muted text-uppercase fw-bold mb-1 meta-label">{{ __('common.category') }}</span>
                                            @if($product->category)
                                                <a href="{{ route('shop.category', $product->category->slug) }}" class="text-decoration-none text-primary fw-medium">{{ $product->category->name }}</a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </div>
                                        @if($product->unit)
                                        <div class="mb-3">
                                            <span class="d-block text-muted text-uppercase fw-bold mb-1 meta-label">{{ __('common.unit') }}</span>
                                            <span class="text-dark fw-medium">{{ $product->unit->name }}</span>
                                        </div>
                                        @endif
                                        <div>
                                            <span class="d-block text-muted text-uppercase fw-bold mb-1 meta-label">{{ __('common.share') }}</span>
                                            <div class="d-flex gap-2">
                                                <a href="#" class="btn btn-sm btn-white border shadow-sm rounded-circle d-flex align-items-center justify-content-center btn-share"><i class="fab fa-facebook-f text-primary small"></i></a>
                                                <a href="#" class="btn btn-sm btn-white border shadow-sm rounded-circle d-flex align-items-center justify-content-center btn-share"><i class="fab fa-twitter text-info small"></i></a>
                                                <a href="#" class="btn btn-sm btn-white border shadow-sm rounded-circle d-flex align-items-center justify-content-center btn-share"><i class="fab fa-whatsapp text-success small"></i></a>
                                            </div>
                                        </div>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Tabs Section -->
            <div class="product-tabs-wrapper mt-4 bg-white rounded-4 shadow-sm p-4 p-lg-5">
                <ul class="nav nav-tabs-custom border-bottom mb-4" id="productTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link-custom active" id="desc-tab" data-bs-toggle="tab" data-bs-target="#desc" type="button" role="tab">{{ __('common.description') }}</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link-custom" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button" role="tab">{{ __('common.additional_info') }}</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link-custom" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button" role="tab">{{ __('common.reviews') }} ({{ $product->approved_reviews_count ?? 0 }})</button>
                    </li>
                </ul>

                <div class="tab-content tab-content-custom" id="productTabsContent">
                    <div class="tab-pane fade show active" id="desc" role="tabpanel">
                        <div class="prose max-w-none">
                            {!! $product->translate('description') !!}
                        </div>
                    </div>
                    
                    <div class="tab-pane fade" id="info" role="tabpanel">
                        <table class="table table-striped table-hover">
                            <tbody>
                                @if($product->brand)
                                <tr>
                                    <th>{{ __('common.brand') }}</th>
                                    <td>{{ $product->brand->name }}</td>
                                </tr>
                                @endif
                                @if($product->unit)
                                <tr>
                                    <th>{{ __('common.unit') }}</th>
                                    <td>{{ $product->unit->name }}</td>
                                </tr>
                                @endif
                                @if($product->color_objects->isNotEmpty())
                                <tr>
                                    <th>{{ __('common.colors') }}</th>
                                    <td>
                                        <div class="d-flex gap-2 align-items-center">
                                            @foreach($product->color_objects as $color)
                                                <div class="d-flex align-items-center gap-2 border rounded-pill pe-3 ps-1 py-1">
                                                    <span class="d-inline-block rounded-circle border shadow-sm color-swatch-sm" 
                                                          style="background-color: {{ $color->code }};"></span>
                                                    <span class="small text-muted">{{ $color->name }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>
                                </tr>
                                @elseif(!empty($product->colors) && is_array($product->colors))
                                <tr>
                                    <th>{{ __('common.colors') }}</th>
                                    <td>
                                        <div class="d-flex gap-2 align-items-center">
                                            @foreach($product->colors as $color)
                                                <div class="d-flex align-items-center gap-2 border rounded-pill pe-3 ps-1 py-1">
                                                    <span class="d-inline-block rounded-circle border shadow-sm color-swatch-sm" 
                                                          style="background-color: {{ $color }};"></span>
                                                    <span class="small text-muted">{{ $color }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>
                                </tr>
                                @endif
                                @if($product->size_objects->isNotEmpty())
                                <tr>
                                    <th>{{ __('common.sizes') }}</th>
                                    <td>
                                        <div class="d-flex flex-wrap gap-2 align-items-center">
                                            @foreach($product->size_objects as $size)
                                                <span class="badge bg-light text-dark border fw-normal">{{ $size->name }}</span>
                                            @endforeach
                                        </div>
                                    </td>
                                </tr>
                                @elseif(!empty($product->sizes) && is_array($product->sizes))
                                <tr>
                                    <th>{{ __('common.sizes') }}</th>
                                    <td>
                                        <div class="d-flex flex-wrap gap-2 align-items-center">
                                            @foreach($product->sizes as $sizeName)
                                                <span class="badge bg-light text-dark border fw-normal">{{ $sizeName }}</span>
                                            @endforeach
                                        </div>
                                    </td>
                                </tr>
                                @endif
                                @if(!empty($product->tags) && is_array($product->tags))
                                <tr>
                                    <th>{{ __('common.tags') }}</th>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($product->tags as $tag)
                                                <span class="badge bg-light text-secondary border fw-normal">{{ $tag }}</span>
                                            @endforeach
                                        </div>
                                    </td>
                                </tr>
                                @endif
                                @if($product->weight)
                                <tr>
                                    <th width="200">{{ __('common.weight') }}</th>
                                    <td>{{ $product->weight }}</td>
                                </tr>
                                @endif
                                @if($product->dimensions)
                                <tr>
                                    <th>{{ __('common.dimensions') }}</th>
                                    <td>{{ $product->dimensions }}</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <div class="tab-pane fade" id="reviews" role="tabpanel">
                        <div class="row">
                            <div class="col-lg-8">
                                <!-- Review Form -->
                                @auth
                                    <div class="review-form-card mb-5">
                                        <h5 class="mb-4">{{ __('common.write_review') }}</h5>
                                        <form action="{{ route('product.review.store', $product->id) }}" method="POST">
                                            @csrf
                                            <div class="mb-4">
                                                <label class="form-label fw-bold">{{ __('common.your_rating') }}</label>
                                                <div class="rating-select">
                                                    @for($i = 5; $i >= 1; $i--)
                                                        <input type="radio" id="star{{ $i }}" name="rating" value="{{ $i }}" required />
                                                        <label for="star{{ $i }}" title="{{ $i }} {{ __('common.stars') }}"><i class="fas fa-star"></i></label>
                                                    @endfor
                                                </div>
                                            </div>
                                            <div class="mb-4">
                                                <label class="form-label fw-bold">{{ __('common.your_review') }}</label>
                                                <textarea name="comment" class="form-control" rows="4" required placeholder="{{ __('common.review_placeholder') }}"></textarea>
                                            </div>
                                            <button type="submit" class="btn btn-dark rounded-pill px-5">{{ __('common.submit_review') }}</button>
                                        </form>
                                    </div>
                                @else
                                    <div class="alert alert-light border mb-4 p-4 text-center rounded-3">
                                        <i class="fas fa-lock mb-3 fs-3 text-muted"></i>
                                        <p class="mb-2">{{ __('common.login_to_review') }}</p>
                                        <a href="{{ route('login') }}" class="btn btn-outline-dark rounded-pill px-4 btn-sm">{{ __('common.login_now') }}</a>
                                    </div>
                                @endauth

                                <!-- Reviews List -->
                                <div class="reviews-list">
                                    @forelse($product->approvedReviews as $review)
                                        <div class="review-item d-flex gap-3 mb-4 pb-4 border-bottom">
                                            <div class="flex-shrink-0">
                                                <div class="avatar-circle">
                                                    {{ substr($review->user->name, 0, 1) }}
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="d-flex align-items-center justify-content-between mb-2">
                                                    <h6 class="mb-0 fw-bold">{{ $review->user->name }}</h6>
                                                    <span class="text-muted small">{{ $review->created_at->diffForHumans() }}</span>
                                                </div>
                                                <div class="text-warning mb-2 small">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <i class="fas fa-star{{ $i <= $review->rating ? '' : '-o' }}"></i>
                                                    @endfor
                                                </div>
                                                <p class="mb-0 text-muted">{{ $review->comment }}</p>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center py-5">
                                            <div class="mb-3 text-muted opacity-25">
                                                <i class="far fa-comment-alt fa-3x"></i>
                                            </div>
                                            <p class="text-muted">{{ __('common.no_reviews_yet') }}</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Products -->
            @if($relatedProducts && $relatedProducts->count() > 0)
                <div class="related-products-section mt-5 pt-5 border-top">
                    <h3 class="section-title mb-4 font-primary fw-bold text-center">{{ setting('product_related_title', __('common.you_may_also_like')) }}</h3>
                    <div class="row g-4">
                        @foreach($relatedProducts as $related)
                            @include('frontend.partials.product-card', ['product' => $related])
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Virtual Try-On modal is in layout (frontend.partials.tryon-modal) so it works from any page --}}

    @push('scripts')
    <script src="{{ asset('frontend/js/product-details.js') }}"></script>
    @endpush
@endsection
