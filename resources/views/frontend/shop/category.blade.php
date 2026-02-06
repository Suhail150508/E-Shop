@extends('layouts.frontend')

@section('page_title', isset($currentCategory) ? $currentCategory->name : __('common.shop_all'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('frontend/css/shop.css') }}">
@endpush

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="breadcrumb">
            <a href="{{ route('home') }}">{{ __('common.home') }}</a>
            <span class="separator">/</span>
            <a href="{{ route('shop.index') }}">{{ __('common.shop') }}</a>
            @if(isset($currentCategory))
                <span class="separator">/</span>
                <span>{{ $currentCategory->name }}</span>
            @endif
        </div>
        <h1 class="page-title">{{ isset($currentCategory) ? $currentCategory->name : setting('shop_header_title', __('common.all_products')) }}</h1>
        <p class="page-subtitle">{{ isset($currentCategory) && $currentCategory->description ? $currentCategory->description : setting('category_default_subtitle', __('common.shop_subtitle')) }}</p>
    </div>

    <!-- Main Container -->
    <div class="shop-container">
        <div class="shop-layout">
            <!-- Desktop Filters Sidebar -->
            <aside class="filters-sidebar" id="desktopFilters">
                <div class="filter-card">
                    <div class="filter-header">
                        <h3>{{ __('common.filter_options') }}</h3>
                        <button type="button" class="clear-filters" onclick="clearAllFilters()">
                            <i class="fas fa-sync-alt"></i>
                            {{ __('common.reset') }}
                        </button>
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
                                               {{ (isset($currentCategory) && $currentCategory->id == $category->id) || in_array($category->id, (array)request('categories', [])) ? 'checked' : '' }}>
                                        <span class="filter-label">
                                            <span>{{ $category->name }}</span>
                                            <span class="filter-count">({{ ($category->products_count ?? 0) + ($category->sub_products_count ?? 0) }})</span>
                                        </span>
                                    </label>
                                    @if($category->children->isNotEmpty())
                                        @foreach($category->children as $child)
                                            <label class="filter-option sub-category-indent">
                                                <input type="checkbox" name="categories[]" value="{{ $child->id }}"
                                                       {{ (isset($currentCategory) && $currentCategory->id == $child->id) || in_array($child->id, (array)request('categories', [])) ? 'checked' : '' }}>
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
                                        <span class="price-separator">-</span>
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
                                                 style="background: {{ $color->code }}; border: 1px solid #E5E5E5;" 
                                                 data-color="{{ $color->name }}"
                                                 title="{{ $color->name }}"
                                                 onclick="toggleColor(this)"></div>
                                        @endforeach
                                    @else
                                        <p class="text-muted small ps-1">{{ __('No color filters available') }}</p>
                                    @endif
                                </div>
                                <input type="hidden" name="colors" id="colorInput" value="{{ implode(',', $selectedColors) }}">
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize filters
            updateActiveFilters();
            handleResponsiveFilters();
            window.addEventListener('resize', handleResponsiveFilters);
            
            // Set initial view from localStorage
            const savedView = localStorage.getItem('shopView') || 'grid';
            setView(savedView, false);
        });

        // Toggle filter sections
        function toggleFilter(element) {
            const content = element.nextElementSibling;
            
            // Toggle class
            element.classList.toggle('collapsed');
            
            // Update height based on class state
            if (element.classList.contains('collapsed')) {
                content.style.maxHeight = '0px';
            } else {
                content.style.maxHeight = content.scrollHeight + 'px';
            }
        }

        // Initialize all filter sections
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.filter-title').forEach((title, index) => {
                const content = title.nextElementSibling;
                const toggle = title.querySelector('.filter-toggle');
                
                // Keep the first section (Category) open by default, collapse others
                if (index > 0) {
                    title.classList.add('collapsed');
                    content.style.maxHeight = '0px';
                } else {
                    content.style.maxHeight = content.scrollHeight + 'px';
                }
            });
        });

        // Price Range Slider
        const priceRange = document.getElementById('priceRange');
        const priceOutput = document.getElementById('priceOutput');
        const maxPriceInput = document.getElementById('maxPriceInput');
        const minPriceInput = document.getElementById('minPriceInput');

        if (priceRange) {
            priceRange.oninput = function() {
                priceOutput.innerHTML = this.value;
                maxPriceInput.value = this.value;
            }
            
            maxPriceInput.oninput = function() {
                priceRange.value = this.value;
                priceOutput.innerHTML = this.value;
            }
        }

        // Color Selection
        function toggleColor(element) {
            element.classList.toggle('active');
            updateColorInput();
        }

        function updateColorInput() {
            const selectedColors = Array.from(document.querySelectorAll('.color-option.active'))
                .map(el => el.dataset.color);
            document.getElementById('colorInput').value = selectedColors.join(',');
        }

        // View Toggle
        function setView(view, save = true) {
            const grid = document.getElementById('productsGrid');
            const btns = document.querySelectorAll('.view-btn');
            
            if (view === 'list') {
                grid.classList.add('list-view');
                btns[0].classList.remove('active');
                btns[1].classList.add('active');
                // Change col classes for list view
                const cards = grid.querySelectorAll('.col-12.col-sm-6.col-md-4.col-lg-3');
                cards.forEach(card => {
                    card.classList.remove('col-sm-6', 'col-md-4', 'col-lg-3');
                });
            } else {
                grid.classList.remove('list-view');
                btns[0].classList.add('active');
                btns[1].classList.remove('active');
                // Restore col classes for grid view
                const cards = grid.querySelectorAll('.col-12');
                cards.forEach(card => {
                    card.classList.add('col-sm-6', 'col-md-4', 'col-lg-3');
                });
            }
            
            if (save) {
                localStorage.setItem('shopView', view);
            }
        }

        // Active Filters
        function updateActiveFilters() {
            const container = document.getElementById('activeFilters');
            container.innerHTML = '';
            let hasFilters = false;

            // Categories
            const checkedCategories = document.querySelectorAll('input[name="categories[]"]:checked');
            checkedCategories.forEach(input => {
                hasFilters = true;
                const label = input.closest('.filter-option').querySelector('.filter-label span').textContent;
                addFilterTag(label, () => {
                    input.checked = false;
                    document.getElementById('filtersForm').submit();
                });
            });

            // Colors
            const activeColors = document.querySelectorAll('.color-option.active');
            activeColors.forEach(color => {
                hasFilters = true;
                const colorName = color.dataset.color;
                addFilterTag(colorName.charAt(0).toUpperCase() + colorName.slice(1), () => {
                    color.classList.remove('active');
                    updateColorInput();
                    document.getElementById('filtersForm').submit();
                });
            });

            // Price
            const minPrice = document.getElementById('minPriceInput').value;
            const maxPrice = document.getElementById('maxPriceInput').value;
            if (minPrice > 0 || maxPrice < 1000) {
                hasFilters = true;
                addFilterTag(`$${minPrice} - $${maxPrice}`, () => {
                    document.getElementById('minPriceInput').value = 0;
                    document.getElementById('maxPriceInput').value = 1000;
                    document.getElementById('filtersForm').submit();
                });
            }

            if (hasFilters) {
                container.style.display = 'flex';
                // Add Clear All button
                const clearAll = document.createElement('button');
                clearAll.className = 'clear-filters ms-auto';
                clearAll.innerHTML = '<i class="fas fa-times"></i> Clear All';
                clearAll.onclick = clearAllFilters;
                container.appendChild(clearAll);
            } else {
                container.style.display = 'none';
            }
        }

        function addFilterTag(text, onRemove) {
            const tag = document.createElement('div');
            tag.className = 'filter-tag';
            tag.innerHTML = `
                ${text}
                <i class="fas fa-times"></i>
            `;
            tag.onclick = onRemove;
            document.getElementById('activeFilters').appendChild(tag);
        }

        function clearAllFilters() {
            window.location.href = "{{ route('shop.index') }}";
        }

        // Mobile Filters
        const mobileToggle = document.getElementById('mobileFilterToggle');
        const mobileFilters = document.getElementById('mobileFilters');
        const filterOverlay = document.getElementById('filterOverlay');
        const mobileContent = document.getElementById('mobileFiltersContent');
        const desktopFilters = document.getElementById('desktopFilters');

        if (mobileToggle) {
            mobileToggle.addEventListener('click', function() {
                mobileFilters.classList.add('show');
                filterOverlay.classList.add('show');
                document.body.style.overflow = 'hidden';
            });
        }

        function closeMobileFilters() {
            mobileFilters.classList.remove('show');
            filterOverlay.classList.remove('show');
            document.body.style.overflow = '';
        }

        if (filterOverlay) {
            filterOverlay.addEventListener('click', closeMobileFilters);
        }

        function handleResponsiveFilters() {
            if (window.innerWidth <= 900) {
                // Move filters to mobile drawer if empty
                if (mobileContent.children.length === 0 && desktopFilters) {
                    const filterContent = desktopFilters.querySelector('.filter-card').cloneNode(true);
                    mobileContent.appendChild(filterContent);
                }
            } else {
                // Clear mobile drawer on desktop
                mobileContent.innerHTML = '';
            }
        }
    </script>
@endpush