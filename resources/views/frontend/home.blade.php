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
                            $heroImages = json_decode(setting('home_hero_gallery') ?? '[]', true);
                            if (empty($heroImages) || !is_array($heroImages)) {
                                $heroImages = [
                                    [
                                        ['image' => null, 'name' => __('Elegant Evening Gown'), 'badge' => __('common.new')],
                                        ['image' => null, 'name' => __('Velvet Blazer'), 'badge' => __('Trend')],
                                        ['image' => null, 'name' => __('Designer Outfit'), 'badge' => __('Premium')],
                                    ],
                                    [
                                        ['image' => null, 'name' => __('Floral Maxi Dress'), 'badge' => __('common.sale_badge')],
                                        ['image' => null, 'name' => __('Summer Dress'), 'badge' => __('Popular')],
                                        ['image' => null, 'name' => __('Casual Wear'), 'badge' => __('common.new')],
                                    ],
                                    [
                                        ['image' => null, 'name' => __('Vintage Collection'), 'badge' => __('common.hot')],
                                        ['image' => null, 'name' => __('Chic Top'), 'badge' => __('Limited')],
                                        ['image' => null, 'name' => __('Fashion Collection'), 'badge' => __('common.hot')],
                                    ]
                                ];
                            }
                        @endphp

                        @foreach($heroImages as $positionIndex => $images)
                            <div class="gallery-item" data-position="{{ $positionIndex }}">
                                <div class="image-wrapper">
                                    @foreach($images as $imgIndex => $item)
                                        <img src="{{ getImageOrPlaceholder($item['image'] ?? null, '600x800') }}" 
                                             alt="{{ $item['name'] ?? __('common.gallery_image') }}" 
                                             class="gallery-img {{ $imgIndex === 0 ? 'active' : '' }}"
                                             data-image-index="{{ $imgIndex }}"
                                             data-badge="{{ $item['badge'] ?? '' }}"
                                             loading="{{ $positionIndex === 0 && $imgIndex === 0 ? 'eager' : 'lazy' }}"
                                             onerror="this.src='{{ asset('backend/images/placeholder.svg') }}'">
                                    @endforeach
                                </div>
                                @if(count($images) > 0)
                                    <div class="gallery-badge">{{ $images[0]['badge'] ?? '' }}</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Categories -->
    <section class="category-section" id="shop">
        <div class="container">
            <div class="text-center fade-in" data-delay="0">
                <div class="section-badge section-badge-solid">{{ setting('home_category_badge', __('common.categories')) }}</div>
                <h2 class="section-title">{{ setting('home_category_title', __('common.shop_by_category')) }}</h2>
                <p class="section-subtitle">{{ setting('home_category_subtitle', __('common.categories_subtitle')) }}</p>
            </div>
            <div class="mt-5">
                @if(isset($categories) && $categories->count())
                    @php
                        $icons = [
                            'fas fa-hat-cowboy-side', 
                            'fas fa-female', 
                            'fas fa-child', 
                            'fas fa-tshirt',
                            'fas fa-shopping-bag',
                            'fas fa-gem'
                        ];
                    @endphp
                    
                    @if($categories->count() > 4)
                        <div class="marquee-wrapper">
                            <div class="marquee-content" style="animation-duration: {{ $categories->count() * 10 }}s">
                                {{-- Loop 4 times to ensure seamless infinite scroll --}}
                                @foreach(range(1, 4) as $i)
                                    @foreach($categories as $category)
                                        <div class="marquee-item">
                                            <a href="{{ route('shop.category', $category) }}" class="category-card fade-in text-decoration-none d-block">
                                                <div class="category-icon">
                                                    <i class="{{ $icons[$loop->index % count($icons)] }}"></i>
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
                                    <a href="{{ route('shop.category', $category) }}" class="category-card fade-in text-decoration-none d-block">
                                        <div class="category-icon">
                                            <i class="{{ $icons[$loop->index % count($icons)] }}"></i>
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
                            <a href="{{ route('shop.index') }}" class="category-card fade-in text-decoration-none d-block">
                                <div class="category-icon"><i class="fas fa-hat-wizard"></i></div>
                                <h3 class="category-name">{{ __('Accessories') }}</h3>
                                <p class="category-count">{{ __('common.explore') }}</p>
                            </a>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <a href="{{ route('shop.index') }}" class="category-card fade-in text-decoration-none d-block">
                                <div class="category-icon"><i class="fas fa-laptop"></i></div>
                                <h3 class="category-name">{{ __('Electronics') }}</h3>
                                <p class="category-count">{{ __('common.explore') }}</p>
                            </a>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <a href="{{ route('shop.index') }}" class="category-card fade-in text-decoration-none d-block">
                                <div class="category-icon"><i class="fas fa-gem"></i></div>
                                <h3 class="category-name">{{ __('Jewelry') }}</h3>
                                <p class="category-count">{{ __('common.explore') }}</p>
                            </a>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <a href="{{ route('shop.index') }}" class="category-card fade-in text-decoration-none d-block">
                                <div class="category-icon"><i class="fas fa-dumbbell"></i></div>
                                <h3 class="category-name">{{ __('Sports Fashion') }}</h3>
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
                <div class="col-lg-6 order-lg-2 promo-img-col promo-bg" style="background: url('{{ getImageOrPlaceholder(setting('home_promo_image'), '800x600') }}') center/cover; min-height: 400px;"></div>
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

    <!-- Flash Sale Products -->
    @if(isset($flashSaleProducts) && $flashSaleProducts->count())
    <section class="products-section bg-light" id="flash-sale">
        <div class="container">
            <div class="text-center fade-in" data-delay="0">
                <div class="section-badge section-badge-solid text-danger">{{ setting('home_flash_badge', __('common.offers')) }}</div>
                <h2 class="section-title">{{ setting('home_flash_title', __('common.limited_offers')) }}</h2>
                <p class="section-subtitle">{{ setting('home_flash_subtitle', __('common.limited_offers_subtitle')) }}</p>
            </div>
            <div class="row g-4 mt-5">
                @foreach($flashSaleProducts as $product)
                    @include('frontend.partials.product-card', ['product' => $product, 'badge' => 'SALE'])
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Brand / Testimonials Section (Dark) -->
    <section class="banner-section banner-section-dark py-5 my-5" id="about">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <div class="position-relative fade-in" data-delay="0">
                        <img src="{{ getImageOrPlaceholder(setting('home_banner_image'), '1200x600') }}" alt="{{ setting('home_banner_title', __('Fashion Collection')) }}" class="img-fluid rounded-4 shadow-lg">
                        <div class="position-absolute bottom-0 end-0 bg-white p-3 rounded-3 shadow mb-4 me-4">
                            <div class="d-flex align-items-center gap-3">
                                <div class="text-warning"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i></div>
                                <div><h6 class="mb-0 fw-bold">4.9/5</h6><small class="text-muted">{{ __('common.reviews_count', ['count' => '15K+']) }}</small></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="fade-in ps-lg-4" data-delay="1">
                        <span class="section-badge section-badge-outline">{{ setting('home_banner_badge', __('common.testimonials')) }}</span>
                        <h2 class="banner-dark-title mb-4 mt-2">{{ setting('home_banner_title', __('common.crafted_with_care')) }}</h2>
                        <p class="banner-dark-text mb-4">
                            {{ setting('home_banner_text', __('common.testimonials_paragraph')) }}
                        </p>
                        <div class="testimonial-avatars d-flex gap-3 mb-4">
                            <div class="testimonial-avatar">
                                <div class="avatar-circle bg-primary bg-opacity-25 text-white">JD</div>
                                <small class="d-block mt-1 fw-semibold">John D.</small>
                            </div>
                            <div class="testimonial-avatar">
                                <div class="avatar-circle bg-primary bg-opacity-25 text-white">SJ</div>
                                <small class="d-block mt-1 fw-semibold">Sarah J.</small>
                            </div>
                        </div>
                        <a href="{{ setting('home_banner_btn_link', route('shop.index')) }}" class="btn btn-primary btn-lg rounded-pill px-5">{{ setting('home_banner_btn_text', __('common.shop_now')) }} <i class="fas fa-arrow-right ms-2"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Products -->
    <section class="products-section" id="featured">
        <div class="container">
            <div class="text-center fade-in" data-delay="0">
                <div class="section-badge section-badge-solid">{{ setting('home_featured_badge', __('common.latest')) }}</div>
                <h2 class="section-title">{{ setting('home_featured_title', __('common.trending_products')) }}</h2>
                <p class="section-subtitle">{{ setting('home_featured_subtitle', __('common.trending_subtitle')) }}</p>
            </div>
            <div class="row g-4 mt-5">
                @if(isset($featuredProducts) && $featuredProducts->count())
                    @foreach($featuredProducts as $product)
                        @include('frontend.partials.product-card', ['product' => $product, 'badge' => 'HOT'])
                    @endforeach
                @elseif(isset($latestProducts) && $latestProducts->count())
                     @foreach($latestProducts as $product)
                        @include('frontend.partials.product-card', ['product' => $product, 'badge' => 'NEW'])
                    @endforeach
                @else
                    <div class="col-12 text-center py-5">
                        <p class="text-muted">{{ __('common.no_products_found') }}</p>
                    </div>
                @endif
                @if((isset($featuredProducts) && $featuredProducts->count()) || (isset($latestProducts) && $latestProducts->count()))
                    <div class="col-12 text-center mt-4">
                        <a href="{{ route('shop.index') }}" class="btn btn-outline-dark btn-lg rounded-pill px-4 py-2">
                            {{ __('common.view_all') }} <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </section>

<script>
(function() {
    'use strict';
    
    /**
     * Hero Gallery Slider - One-by-One Image Transition
     * Changes one image at a time every 6 seconds with smooth animation
     * Cycles through 9 images (3 positions × 3 images each)
     */
    
    function initHeroSlider() {
        const galleryItems = document.querySelectorAll('.gallery-item');
        
        if (galleryItems.length === 0) {
            return;
        }
        
        // Track current image index for each position (0, 1, 2)
        const imageIndices = [0, 0, 0];
        const totalImagesPerPosition = 3;
        let currentPositionIndex = 0; // Which position to change next (0, 1, 2)
        let slideInterval = null;
        const intervalDuration = 6000; // 6 seconds
        
        // Initialize: Activate first image in each position
        function initializeImages() {
            galleryItems.forEach(function(item, positionIndex) {
                const images = item.querySelectorAll('.gallery-img');
                const badge = item.querySelector('.gallery-badge');
                
                images.forEach(function(img, imgIndex) {
                    if (imgIndex === 0) {
                        img.classList.add('active');
                        // Update badge text
                        if (badge && img.dataset.badge) {
                            badge.textContent = img.dataset.badge;
                        }
                    } else {
                        img.classList.remove('active');
                    }
                });
            });
        }
        
        // Change image in a specific position
        function changeImageInPosition(positionIndex) {
            const item = galleryItems[positionIndex];
            if (!item) return;
            
            const images = item.querySelectorAll('.gallery-img');
            const badge = item.querySelector('.gallery-badge');
            const currentImgIndex = imageIndices[positionIndex];
            const nextImgIndex = (currentImgIndex + 1) % totalImagesPerPosition;
            
            // Get current and next images
            const currentImg = images[currentImgIndex];
            const nextImg = images[nextImgIndex];
            
            if (!currentImg || !nextImg) return;
            
            // Remove active class from current image (triggers fade out)
            currentImg.classList.remove('active');
            
            // Update badge
            if (badge && nextImg.dataset.badge) {
                badge.textContent = nextImg.dataset.badge;
            }
            
            // Add active class to next image (triggers fade in)
            requestAnimationFrame(function() {
                nextImg.classList.add('active');
            });
            
            // Update index for this position
            imageIndices[positionIndex] = nextImgIndex;
        }
        
        // Rotate to next image (one position at a time)
        function rotateNextImage() {
            // Change image in current position
            changeImageInPosition(currentPositionIndex);
            
            // Move to next position (with wrapping)
            currentPositionIndex = (currentPositionIndex + 1) % galleryItems.length;
        }
        
        // Start auto-rotation
        function startSlider() {
            if (slideInterval) {
                clearInterval(slideInterval);
            }
            slideInterval = setInterval(rotateNextImage, intervalDuration);
        }
        
        // Pause slider on hover (better UX)
        function setupHoverPause() {
            const gallery = document.querySelector('.hero-gallery');
            if (!gallery) return;
            
            gallery.addEventListener('mouseenter', function() {
                if (slideInterval) {
                    clearInterval(slideInterval);
                    slideInterval = null;
                }
            });
            
            gallery.addEventListener('mouseleave', function() {
                startSlider();
            });
        }
        
        // Initialize everything
        initializeImages();
        startSlider();
        setupHoverPause();
        
        // Cleanup on page unload
        window.addEventListener('beforeunload', function() {
            if (slideInterval) {
                clearInterval(slideInterval);
            }
        });
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initHeroSlider);
    } else {
        initHeroSlider();
    }
})();

    /**
     * Scroll-triggered animations: add .in-view when .fade-in enters viewport
     */
    (function() {
        var fadeEls = document.querySelectorAll('.home-page .fade-in');
        if (!fadeEls.length) return;
        var io = typeof IntersectionObserver !== 'undefined' ? new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('in-view');
                }
            });
        }, { rootMargin: '0px 0px -40px 0px', threshold: 0.05 }) : null;
        if (io) {
            fadeEls.forEach(function(el) { io.observe(el); });
        } else {
            fadeEls.forEach(function(el) { el.classList.add('in-view'); });
        }
    })();
</script>
</div>
@endsection