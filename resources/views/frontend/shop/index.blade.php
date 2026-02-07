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
            <aside class="filters-sidebar" id="desktopFilters">
                <div class="filter-card">
                    <div class="filter-header">
                        <h3>{{ __('common.filter_options') }}</h3>
                        @if(request()->anyFilled(['categories', 'min_price', 'max_price', 'colors', 'availability']))
                            <a href="{{ route('shop.index') }}" class="clear-filters">
                                <i class="fas fa-sync-alt"></i> {{ __('common.reset') }}
                            </a>
                        @endif
                    </div>
                    
                    <form method="GET" action="{{ route('shop.index') }}" id="filtersForm">
                        <!-- Category Filter -->
                        <div class="filter-section">
                            <div class="filter-title" onclick="toggleFilter(this)">
                                <span>{{ __('common.category') }}</span>
                                <i class="fas fa-chevron-down filter-toggle"></i>
                            </div>
                            <div class="filter-content">
                                @foreach($categories as $category)
                                    <label class="filter-option">
                                        <input type="checkbox" name="categories[]" value="{{ $category->id }}" 
                                               {{ in_array($category->id, (array)request('categories', [])) ? 'checked' : '' }}>
                                        <span class="filter-label">
                                            <span>{{ $category->name }}</span>
                                            <span class="filter-count">({{ ($category->products_count ?? 0) + ($category->sub_products_count ?? 0) }})</span>
                                        </span>
                                    </label>
                                    @if($category->children->isNotEmpty())
                                        @foreach($category->children as $child)
                                            <label class="filter-option sub-category-indent">
                                                <input type="checkbox" name="categories[]" value="{{ $child->id }}"
                                                       {{ in_array($child->id, (array)request('categories', [])) ? 'checked' : '' }}>
                                                <span class="filter-label">
                                                    <span>{{ $child->name }}</span>
                                                    <span class="filter-count">({{ ($child->products_count ?? 0) + ($child->sub_products_count ?? 0) }})</span>
                                                </span>
                                            </label>
                                        @endforeach
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        <!-- Price Filter -->
                        <div class="filter-section">
                            <div class="filter-title" onclick="toggleFilter(this)">
                                <span>{{ __('common.price_range') }}</span>
                                <i class="fas fa-chevron-down filter-toggle"></i>
                            </div>
                            <div class="filter-content">
                                <div class="price-range">
                                    <div class="price-inputs">
                                        <div class="price-input-group">
                                            <span class="price-currency">{{ current_currency()?->symbol ?? '$' }}</span>
                                            <input type="number" name="min_price" id="minPriceInput" class="price-input" 
                                                   placeholder="0" value="{{ request('min_price') }}" min="0">
                                        </div>
                                        <span class="price-separator">{{ __('common.to') }}</span>
                                        <div class="price-input-group">
                                            <span class="price-currency">{{ current_currency()?->symbol ?? '$' }}</span>
                                            <input type="number" name="max_price" id="maxPriceInput" class="price-input" 
                                                   placeholder="1000" value="{{ request('max_price') }}" min="0">
                                        </div>
                                    </div>
                                    <div class="price-slider-container">
                                        <input type="range" class="price-slider" id="priceRange" min="0" max="1000" 
                                               value="{{ request('max_price', 1000) }}">
                                    </div>
                                    <div class="d-flex justify-content-between mt-2 px-1">
                                        <span class="text-muted small">{{ __('common.max') }} {{ current_currency()?->symbol ?? '$' }}<span id="priceOutput">{{ request('max_price', 1000) }}</span></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Color Filter -->
                        <div class="filter-section">
                            <div class="filter-title" onclick="toggleFilter(this)">
                                <span>{{ __('common.color') }}</span>
                                <i class="fas fa-chevron-down filter-toggle"></i>
                            </div>
                            <div class="filter-content">
                                <div class="color-filters">
                                    @php
                                        $rawColors = request('colors', []);
                                        if (is_array($rawColors)) {
                                            $selectedColors = $rawColors;
                                        } else {
                                            $selectedColors = $rawColors ? explode(',', $rawColors) : [];
                                        }
                                    @endphp
                                    @if(isset($colors) && count($colors) > 0)
                                        @foreach($colors as $color)
                                            <div class="color-option {{ in_array($color->name, $selectedColors) ? 'active' : '' }}" 
                                                 style="background: {{ $color->code }};" 
                                                 data-color="{{ $color->name }}"
                                                 title="{{ $color->name }}"
                                                 onclick="toggleColor(this)"></div>
                                        @endforeach
                                    @else
                                        <p class="text-muted small ps-1">{{ __('common.no_colors_available') }}</p>
                                    @endif
                                </div>
                                <input type="hidden" name="colors" id="colorInput" value="{{ implode(',', $selectedColors) }}">
                            </div>
                        </div>

                        <!-- Size Filter -->
                        <div class="filter-section">
                            <div class="filter-title" onclick="toggleFilter(this)">
                                <span>{{ __('common.size') }}</span>
                                <i class="fas fa-chevron-down filter-toggle"></i>
                            </div>
                            <div class="filter-content">
                                @if(isset($sizes) && count($sizes) > 0)
                                    @foreach($sizes as $size)
                                        <label class="filter-option">
                                            <input type="checkbox" name="sizes[]" value="{{ $size->name }}"
                                                   {{ in_array($size->name, (array)request('sizes', [])) ? 'checked' : '' }}>
                                            <span class="filter-label">
                                                <span>{{ $size->name }}</span>
                                            </span>
                                        </label>
                                    @endforeach
                                @else
                                    <p class="text-muted small ps-1">{{ __('common.no_sizes_available') }}</p>
                                @endif
                            </div>
                        </div>

                        <!-- Unit Filter -->
                        <div class="filter-section">
                            <div class="filter-title" onclick="toggleFilter(this)">
                                <span>{{ __('common.unit') }}</span>
                                <i class="fas fa-chevron-down filter-toggle"></i>
                            </div>
                            <div class="filter-content">
                                @if(isset($units) && count($units) > 0)
                                    @foreach($units as $unit)
                                        <label class="filter-option">
                                            <input type="checkbox" name="unit_id[]" value="{{ $unit->id }}"
                                                   {{ in_array($unit->id, (array)request('unit_id', [])) ? 'checked' : '' }}>
                                            <span class="filter-label">
                                                <span>{{ $unit->name }}</span>
                                            </span>
                                        </label>
                                    @endforeach
                                @else
                                    <p class="text-muted small ps-1">{{ __('common.no_units_available') }}</p>
                                @endif
                            </div>
                        </div>

                        <!-- Tags Filter -->
                        <div class="filter-section">
                            <div class="filter-title" onclick="toggleFilter(this)">
                                <span>{{ __('common.tags') }}</span>
                                <i class="fas fa-chevron-down filter-toggle"></i>
                            </div>
                            <div class="filter-content">
                                @if(isset($tags) && count($tags) > 0)
                                    @foreach($tags as $tag)
                                        <label class="filter-option">
                                            <input type="checkbox" name="tags[]" value="{{ $tag }}"
                                                   {{ in_array($tag, (array)request('tags', [])) ? 'checked' : '' }}>
                                            <span class="filter-label">
                                                <span>{{ $tag }}</span>
                                            </span>
                                        </label>
                                    @endforeach
                                @else
                                    <p class="text-muted small ps-1">{{ __('common.no_tags_available') }}</p>
                                @endif
                            </div>
                        </div>

                        <!-- Rating Filter -->
                        <div class="filter-section">
                            <div class="filter-title" onclick="toggleFilter(this)">
                                <span>{{ __('common.rating') }}</span>
                                <i class="fas fa-chevron-down filter-toggle"></i>
                            </div>
                            <div class="filter-content">
                                @foreach(range(5, 1) as $rating)
                                    <label class="filter-option">
                                        <input type="radio" name="rating" value="{{ $rating }}"
                                               {{ request('rating') == $rating ? 'checked' : '' }}>
                                        <span class="filter-label">
                                            <span class="text-warning">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star {{ $i <= $rating ? '' : 'text-muted' }}"></i>
                                                @endfor
                                            </span>
                                            <span class="small text-muted ms-1">{{ $rating }} {{ __('common.and_up') }}</span>
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Virtual Try-On -->
                        <div class="filter-section">
                             <div class="filter-title" onclick="toggleFilter(this)">
                                <span>{{ __('common.features') }}</span>
                                <i class="fas fa-chevron-down filter-toggle"></i>
                            </div>
                            <div class="filter-content">
                                <label class="filter-option">
                                    <input type="checkbox" name="is_tryable" value="1"
                                           {{ request('is_tryable') ? 'checked' : '' }}>
                                    <span class="filter-label">
                                        <i class="fas fa-camera me-1"></i>
                                        <span>{{ __('common.virtual_try_on') }}</span>
                                    </span>
                                </label>
                            </div>
                        </div>

                        <!-- Availability Filter -->
                        <div class="filter-section">
                            <div class="filter-title" onclick="toggleFilter(this)">
                                <span>{{ __('common.availability') }}</span>
                                <i class="fas fa-chevron-down filter-toggle"></i>
                            </div>
                            <div class="filter-content">
                                @php
                                    $availability = (array)request('availability', []);
                                @endphp
                                <label class="filter-option">
                                    <input type="checkbox" name="availability[]" value="in_stock" 
                                           {{ in_array('in_stock', $availability) ? 'checked' : '' }}>
                                    <span class="filter-label">
                                        <span>{{ __('common.in_stock') }}</span>
                                    </span>
                                </label>
                                <label class="filter-option">
                                    <input type="checkbox" name="availability[]" value="on_sale" 
                                           {{ in_array('on_sale', $availability) ? 'checked' : '' }}>
                                    <span class="filter-label">
                                        <span>{{ __('common.on_sale') }}</span>
                                    </span>
                                </label>
                                <label class="filter-option">
                                    <input type="checkbox" name="availability[]" value="new" 
                                           {{ in_array('new', $availability) ? 'checked' : '' }}>
                                    <span class="filter-label">
                                        <span>{{ __('common.new_arrivals') }}</span>
                                    </span>
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="apply-filters-btn">
                            <i class="fas fa-filter"></i>
                            {{ __('common.apply_filters') }}
                        </button>
                    </form>
                </div>
            </aside>

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
                    <!-- Dynamic filter tags will appear here -->
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