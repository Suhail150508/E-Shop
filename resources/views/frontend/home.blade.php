@extends('layouts.frontend')
@section('header')
@endsection
@push('styles')
<link rel="stylesheet" href="{{ asset('frontend/css/home.css') }}">
@endpush
@section('content')
<div class="home-page">
  <!-- Hero Section -->
    <section class="hero-section" id="home" role="banner" aria-labelledby="hero-heading">
        <div class="hero-texture" aria-hidden="true"></div>
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-1 mb-lg-0">
                    <div class="fade-in hero-content" data-delay="0">
                        <span class="hero-badge">{{ __('common.new_collection', ['year' => date('Y')]) }}</span>
                        <h1 id="hero-heading" class="hero-title">
                            {{ setting('home_hero_title', __('common.hero_title')) }}
                        </h1>
                        <p class="hero-subtitle">
                            {{ setting('home_hero_subtitle', __('common.hero_subtitle')) }}
                        </p>
                        <div class="hero-cta d-flex flex-wrap gap-3">
                            <a href="{{ route('shop.index') }}" class="btn-custom btn-primary-custom">
                                {{ __('common.shop_now') }}
                                <i class="fas fa-arrow-right"></i>
                            </a>
                            <a href="#about" class="btn-custom btn-secondary-custom">
                                {{ __('common.explore_more') }}
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="hero-gallery position-relative">
                        @php
                            // Hero images are now passed from controller via $heroImages variable
                            // Fallback is handled in controller to keep view clean
                        @endphp

                        @foreach($heroImages as $positionIndex => $images)
                            <div class="gallery-item" data-position="{{ $positionIndex }}">
                                <div class="image-wrapper">
                                    @foreach(is_array($images) ? $images : [] as $imgIndex => $item)
                                        @php
                                            $imgSrc = getImageOrPlaceholder(is_array($item) ? ($item['image'] ?? null) : null, '600x800');
                                            $imgAlt = isset($item['name']) ? __('common.'.$item['name']) : __('common.gallery_image');
                                        @endphp
                                        <img src="{{ $imgSrc }}" 
                                             alt="{{ $imgAlt }}" 
                                             class="gallery-img {{ $imgIndex === 0 ? 'active' : '' }}"
                                             data-image-index="{{ $imgIndex }}"
                                             data-badge="{{ is_array($item) && isset($item['badge']) ? __('common.'.$item['badge']) : '' }}"
                                             loading="{{ $positionIndex === 0 && $imgIndex === 0 ? 'eager' : 'lazy' }}"
                                             onerror="this.onerror=null; this.src='{{ route('placeholder', ['size' => '600x800']) }}';">
                                    @endforeach
                                </div>
                                @if(count($images) > 0)
                                    <div class="gallery-badge">{{ isset($images[0]['badge']) ? __('common.'.$images[0]['badge']) : '' }}</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Categories -->
    <section class="category-section" id="shop" aria-labelledby="category-section-heading">
        <div class="container">
            <div class="text-center fade-in" data-delay="0">
                <div class="section-badge section-badge-solid">{{ setting('home_category_badge', __('common.categories')) }}</div>
                <h2 id="category-section-heading" class="section-title">{{ setting('home_category_title', __('common.shop_by_category')) }}</h2>
                <p class="section-subtitle">{{ setting('home_category_subtitle', __('common.categories_subtitle')) }}</p>
            </div>
            <div class="mt-5">
                @if(isset($categories) && $categories->count())
                    @if($categories->count() > 4)
                        <div class="marquee-wrapper">
                            <div class="marquee-content" style="animation-duration: {{ $categories->count() * 10 }}s;">
                                {{-- Loop 4 times to ensure seamless infinite scroll --}}
                                @foreach(range(1, 4) as $i)
                                    @foreach($categories as $category)
                                        <div class="marquee-item">
                                            <a href="{{ route('shop.category', $category->slug) }}" class="category-card fade-in text-decoration-none">
                                                <div class="category-image">
                                                    <img src="{{ getImageOrPlaceholder($category->image ?? null, '300x300') }}" alt="{{ e($category->name) }}" class="img-fluid rounded-circle" onerror="this.onerror=null; this.src='{{ route('placeholder', ['size' => '300x300']) }}';">
                                                </div>
                                                <h3 class="category-name">{{ $category->name }}</h3>
                                                <p class="category-count">
                                                    @if((($category->products_count ?? 0) + ($category->sub_products_count ?? 0)) > 0)
                                                        {{ ($category->products_count ?? 0) + ($category->sub_products_count ?? 0) }}+ {{ __('common.items') }}
                                                    @else
                                                        {{ __('common.no_items') }}
                                                    @endif
                                                </p>
                                            </a>
                                        </div>
                                    @endforeach
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="row g-4 justify-content-center">
                            @foreach($categories as $category)
                                <div class="col-md-6 col-lg-3">
                                    <a href="{{ route('shop.category', $category->slug) }}" class="category-card fade-in text-decoration-none">
                                        <div class="category-image">
                                            <img src="{{ getImageOrPlaceholder($category->image ?? null, '300x300') }}" alt="{{ e($category->name) }}" class="img-fluid rounded-circle" onerror="this.onerror=null; this.src='{{ route('placeholder', ['size' => '300x300']) }}';">
                                        </div>
                                        <h3 class="category-name">{{ $category->name }}</h3>
                                        <p class="category-count">
                                            @if((($category->products_count ?? 0) + ($category->sub_products_count ?? 0)) > 0)
                                                {{ ($category->products_count ?? 0) + ($category->sub_products_count ?? 0) }}+ {{ __('common.items') }}
                                            @else
                                                {{ __('common.no_items') }}
                                            @endif
                                        </p>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endif
                @else
                    <div class="row g-4 justify-content-center">
                        <div class="col-md-6 col-lg-3">
                            <a href="{{ route('shop.index') }}" class="category-card fade-in text-decoration-none">
                                <div class="category-icon"><i class="fas fa-hat-wizard"></i></div>
                                <h3 class="category-name">{{ __('common.accessories') }}</h3>
                                <p class="category-count">{{ __('common.explore') }}</p>
                            </a>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <a href="{{ route('shop.index') }}" class="category-card fade-in text-decoration-none">
                                <div class="category-icon"><i class="fas fa-laptop"></i></div>
                                <h3 class="category-name">{{ __('common.electronics') }}</h3>
                                <p class="category-count">{{ __('common.explore') }}</p>
                            </a>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <a href="{{ route('shop.index') }}" class="category-card fade-in text-decoration-none">
                                <div class="category-icon"><i class="fas fa-gem"></i></div>
                                <h3 class="category-name">{{ __('common.jewelry') }}</h3>
                                <p class="category-count">{{ __('common.explore') }}</p>
                            </a>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <a href="{{ route('shop.index') }}" class="category-card fade-in text-decoration-none">
                                <div class="category-icon"><i class="fas fa-dumbbell"></i></div>
                                <h3 class="category-name">{{ __('common.sports_fashion') }}</h3>
                                <p class="category-count">{{ __('common.explore') }}</p>
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <!-- Mid-Page Fashion Banner -->
    <section class="promo-section py-5 my-3">
        <div class="container">
            <div class="row g-0 rounded-4 overflow-hidden shadow-sm fade-in" data-delay="0">
                <div class="col-lg-6 order-lg-2 promo-img-col promo-bg" style="background-image: url('{{ getImageOrPlaceholder(setting('home_promo_image'), '800x600') }}'); background-size: cover; background-position: center; min-height: 400px;"></div>
                <div class="col-lg-6 order-lg-1 bg-white d-flex align-items-center">
                    <div class="p-5 promo-content">
                        <span class="section-badge section-badge-solid promo-badge">{{ setting('home_promo_badge', __('common.hot_topic')) }}</span>
                        <h2 class="display-5 fw-bold mb-4">{{ setting('home_promo_title', __('common.promo_title')) }}</h2>
                        <p class="text-muted mb-4 lead">
                            {{ setting('home_promo_subtitle', __('common.promo_subtitle')) }}
                        </p>
                        <div class="d-flex flex-wrap gap-3">
                            <a href="{{ setting('home_promo_btn1_link', route('shop.index')) }}" class="btn btn-dark btn-lg rounded-0 px-4">{{ setting('home_promo_btn1_text', __('common.shop_now')) }}</a>
                            <a href="{{ setting('home_promo_btn2_link', route('shop.index')) }}" class="btn btn-outline-dark btn-lg rounded-0 px-4">{{ setting('home_promo_btn2_text', __('common.view_collections')) }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Products -->
    <section class="products-section" id="featured" aria-labelledby="featured-heading">
        <div class="container">
            <div class="products-section-header fade-in" data-delay="0">
                <h2 id="featured-heading" class="section-title-inline">{{ setting('home_featured_title', __('common.trending_products')) }}</h2>
                <a href="{{ route('shop.index') }}" class="section-view-all">{{ __('common.view_all') }} <i class="fas fa-arrow-right ms-1"></i></a>
            </div>
            <div class="row g-4 mt-4">
                @if(isset($featuredProducts) && $featuredProducts->count())
                    @foreach($featuredProducts as $product)
                        @include('frontend.partials.product-card', ['product' => $product, 'badge' => 'HOT'])
                    @endforeach
                @else
                    <div class="col-12 text-center py-5">
                        <p class="text-muted mb-0">{{ __('common.no_products_found') }}</p>
                        <a href="{{ route('shop.index') }}" class="btn btn-primary btn-lg rounded-pill mt-3">{{ __('common.shop_now') }}</a>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <!-- Flash Sale Products -->
    <section class="products-section products-section-flash" id="flash-sale" aria-labelledby="flash-sale-heading">
        <div class="container">
            <div class="products-section-header fade-in" data-delay="0">
                <h2 id="flash-sale-heading" class="section-title-inline">{{ setting('home_flash_title', __('common.limited_offers')) }}</h2>
                <a href="{{ route('shop.index') }}" class="section-view-all">{{ __('common.view_all') }} <i class="fas fa-arrow-right ms-1"></i></a>
            </div>
            <div class="row g-4 mt-4">
                @if(isset($flashSaleProducts) && $flashSaleProducts->count())
                    @foreach($flashSaleProducts as $product)
                        @include('frontend.partials.product-card', ['product' => $product, 'badge' => 'SALE'])
                    @endforeach
                @else
                    <div class="col-12 text-center py-5">
                        <p class="text-muted mb-0">{{ __('common.no_products_found') }}</p>
                        <a href="{{ route('shop.index') }}" class="btn btn-primary btn-lg rounded-pill mt-3">{{ __('common.shop_now') }}</a>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <!-- Fashion Collection / Testimonials Section (above Latest Products) -->
    <section class="banner-section banner-section-dark" id="about" aria-labelledby="banner-heading">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <div class="position-relative fade-in" data-delay="0">
                        <img src="{{ getImageOrPlaceholder(setting('home_banner_image'), '1200x600') }}" alt="{{ e(setting('home_banner_title', __('common.crafted_with_care'))) }}" class="img-fluid rounded-4 shadow-lg banner-section-img" onerror="this.onerror=null; this.src='{{ route('placeholder', ['size' => '1200x600']) }}';">
                        <div class="position-absolute bottom-0 end-0 bg-white p-3 rounded-3 shadow mb-4 me-4">
                            <div class="d-flex align-items-center gap-3">
                                <div class="text-warning"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i></div>
                                <div><h6 class="mb-0 fw-bold">{{ setting('home_banner_rating', '4.9') }}/5</h6><small class="text-muted">{{ __('common.reviews_count', ['count' => setting('home_banner_review_count', '15K+')]) }}</small></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="fade-in ps-lg-4" data-delay="1">
                        <span class="section-badge section-badge-outline">{{ setting('home_banner_badge', __('common.testimonials')) }}</span>
                        <h2 id="banner-heading" class="banner-dark-title mb-4 mt-2">{{ setting('home_banner_title', __('common.crafted_with_care')) }}</h2>
                        <p class="banner-dark-text mb-4">
                            {{ setting('home_banner_text', __('common.testimonials_paragraph')) }}
                        </p>
                        @php
                            $t1 = setting('home_banner_testimonial_1_name', __('common.john_doe'));
                            $t2 = setting('home_banner_testimonial_2_name', __('common.sarah_j'));
                            $parts1 = preg_split('/\s+/', trim($t1), 2);
                            $parts2 = preg_split('/\s+/', trim($t2), 2);
                            $init1 = $parts1 ? strtoupper(mb_substr($parts1[0], 0, 1) . (isset($parts1[1]) ? mb_substr($parts1[1], 0, 1) : '')) : '?';
                            $init2 = $parts2 ? strtoupper(mb_substr($parts2[0], 0, 1) . (isset($parts2[1]) ? mb_substr($parts2[1], 0, 1) : '')) : '?';
                        @endphp
                        <div class="testimonial-avatars d-flex gap-3 mb-4">
                            <div class="testimonial-avatar">
                                <div class="avatar-circle bg-primary bg-opacity-25 text-white">{{ $init1 }}</div>
                                <small class="d-block mt-1 fw-semibold">{{ e($t1) }}</small>
                            </div>
                            <div class="testimonial-avatar">
                                <div class="avatar-circle bg-primary bg-opacity-25 text-white">{{ $init2 }}</div>
                                <small class="d-block mt-1 fw-semibold">{{ e($t2) }}</small>
                            </div>
                        </div>
                        <a href="{{ setting('home_banner_btn_link', route('shop.index')) }}" class="btn btn-primary btn-lg rounded-pill px-5">{{ setting('home_banner_btn_text', __('common.shop_now')) }} <i class="fas fa-arrow-right ms-2"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Latest Products -->
    <section class="products-section products-section-latest" id="latest" aria-labelledby="latest-heading">
        <div class="container">
            <div class="products-section-header fade-in" data-delay="0">
                <h2 id="latest-heading" class="section-title-inline">{{ setting('home_latest_title', __('common.latest_products')) }}</h2>
                <a href="{{ route('shop.index') }}" class="section-view-all">{{ __('common.view_all') }} <i class="fas fa-arrow-right ms-1"></i></a>
            </div>
            <div class="row g-4 mt-4">
                @if(isset($latestProducts) && $latestProducts->count())
                    @foreach($latestProducts as $product)
                        @include('frontend.partials.product-card', ['product' => $product, 'badge' => 'NEW'])
                    @endforeach
                @else
                    <div class="col-12 text-center py-5">
                        <p class="text-muted mb-0">{{ __('common.no_products_found') }}</p>
                        <a href="{{ route('shop.index') }}" class="btn btn-primary btn-lg rounded-pill mt-3">{{ __('common.shop_now') }}</a>
                    </div>
                @endif
            </div>
        </div>
    </section>

@endsection

@push('scripts')
<script src="{{ asset('frontend/js/home.js') }}"></script>
@endpush