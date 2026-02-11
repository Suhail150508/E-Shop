@extends('layouts.frontend')

@section('page_title', setting('shop_page_title', __('common.shop_all')))

@push('styles')
    <link rel="stylesheet" href="{{ asset('frontend/css/shop.css') }}">
@endpush

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="breadcrumb">
            <a href="{{ route('home') }}">{{ __('common.home') }}</a>
            <span class="separator">/</span>
            <span>{{ setting('shop_breadcrumb', __('common.shop_all')) }}</span>
        </div>
        <h1 class="page-title">{{ setting('shop_header_title', __('common.all_products')) }}</h1>
        <p class="page-subtitle">{{ setting('shop_header_subtitle', __('common.shop_subtitle')) }}</p>
    </div>

    <!-- Main Container -->
    <div class="shop-container">
        <div class="shop-layout">
            <!-- Desktop Filters Sidebar -->
            @include('frontend.shop.partials.sidebar')

            <!-- Products Section -->
            <div class="products-section">
                <!-- Toolbar -->
                <div class="products-toolbar">
                    <div class="toolbar-left">
                        <span class="product-count">
                            @if(isset($products) && $products->total())
                                {{ __('common.showing_results', ['first' => $products->firstItem(), 'last' => $products->lastItem(), 'total' => $products->total()]) }}
                            @else
                                {{ __('common.no_products_found') }}
                            @endif
                        </span>
                        <div class="view-toggle">
                            <button class="view-btn active" onclick="setView('grid')" title="{{ __('common.grid_view') }}">
                                <i class="fas fa-th"></i>
                            </button>
                            <button class="view-btn" onclick="setView('list')" title="{{ __('common.list_view') }}">
                                <i class="fas fa-list"></i>
                            </button>
                        </div>
                    </div>
                    <div class="toolbar-right">
                        @php
                            $currentSort = request('sort', 'featured');
                        @endphp
                        <select class="sort-select" name="sort" form="filtersForm" onchange="this.form.submit()">
                            <option value="featured" {{ $currentSort === 'featured' ? 'selected' : '' }}>{{ __('common.featured') }}</option>
                            <option value="price-low" {{ $currentSort === 'price-low' ? 'selected' : '' }}>{{ __('common.price_low_high') }}</option>
                            <option value="price-high" {{ $currentSort === 'price-high' ? 'selected' : '' }}>{{ __('common.price_high_low') }}</option>
                            <option value="newest" {{ $currentSort === 'newest' ? 'selected' : '' }}>{{ __('common.newest_first') }}</option>
                            <option value="rating" {{ $currentSort === 'rating' ? 'selected' : '' }}>{{ __('common.highest_rated') }}</option>
                            <option value="popular" {{ $currentSort === 'popular' ? 'selected' : '' }}>{{ __('common.most_popular') }}</option>
                        </select>
                    </div>
                </div>

                <!-- Active Filters -->
                <div class="active-filters" id="activeFilters">
                    @include('frontend.shop.partials.active-filters')
                </div>

                <!-- Products Grid -->
                <!-- Changed to Bootstrap Row for compatibility with product-card partial -->
                <div class="row g-3 g-md-4" id="productsGrid">
                    @if(isset($products) && $products->count())
                        @foreach($products as $product)
                            @include('frontend.partials.product-card', ['product' => $product])
                        @endforeach
                    @else
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <i class="fas fa-search"></i>
                            </div>
                            <h3>{{ __('common.no_products_found') }}</h3>
                            <p>{{ __('common.try_adjusting_filters') }}</p>
                            <a href="{{ route('shop.index') }}" class="btn-clear">
                                <i class="fas fa-times"></i>
                                {{ __('common.clear_all_filters') }}
                            </a>
                        </div>
                    @endif
                </div>

                @if(isset($products) && $products->hasPages())
                    <div class="shop-pagination">
                        {{ $products->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Mobile Filter Toggle -->
    <button class="mobile-filter-toggle" id="mobileFilterToggle">
        <i class="fas fa-filter"></i>
    </button>

    <!-- Mobile Filters -->
    <div class="filter-overlay" id="filterOverlay"></div>
    <div class="mobile-filters" id="mobileFilters">
        <div class="mobile-filters-header">
            <h3>{{ __('common.filters') }}</h3>
            <button class="close-filters" onclick="closeMobileFilters()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div id="mobileFiltersContent">
            <!-- Mobile filters content loaded from desktop filters -->
        </div>
    </div>

@endsection

@push('scripts')
    <script src="{{ asset('frontend/js/shop.js') }}"></script>
@endpush