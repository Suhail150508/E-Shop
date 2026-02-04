@extends('layouts.frontend')

@section('page_title', setting('shop_page_title', __('common.shop_all')))

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
                                            <label class="filter-option" style="margin-left: 1.5rem;">
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
                                                 style="background: {{ $color->code }}; border: 1px solid #E5E5E5;" 
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
                if (index === 0) {
                    content.style.maxHeight = content.scrollHeight + 'px';
                    title.classList.remove('collapsed');
                } else {
                    content.style.maxHeight = '0px';
                    title.classList.add('collapsed');
                }
                
                content.style.overflow = 'hidden';
                // Use a slight timeout to ensure transitions work after load
                setTimeout(() => {
                    content.style.transition = 'max-height 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
                }, 100);
            });
        });
        
        function toggleColor(element) {
            element.classList.toggle('active');
            updateColorInput();
            updateActiveFilters();
        }

        function updateColorInput() {
            const colors = Array.from(document.querySelectorAll('.color-option.active'))
                .map(opt => opt.dataset.color);
            document.getElementById('colorInput').value = colors.join(',');
        }

        // Set grid/list view
        function setView(view, save = true) {
            const grid = document.getElementById('productsGrid');
            const buttons = document.querySelectorAll('.view-btn');
            
            buttons.forEach(btn => btn.classList.remove('active'));
            // Find the button for this view
            const activeBtn = document.querySelector(`.view-btn[onclick="setView('${view}')"]`);
            if (activeBtn) activeBtn.classList.add('active');
            
            if (view === 'list') {
                grid.classList.add('list-view');
            } else {
                grid.classList.remove('list-view');
            }
            
            if (save) {
                localStorage.setItem('shopView', view);
            }
        }

        // Mobile filters
        function handleResponsiveFilters() {
            const sidebar = document.getElementById('desktopFilters');
            const mobileContainer = document.getElementById('mobileFiltersContent');
            const content = document.querySelector('.filter-card');
            
            // Safety check
            if (!content || !sidebar || !mobileContainer) return;
            
            if (window.innerWidth <= 900) {
                // Move to mobile if not already there
                if (!mobileContainer.contains(content)) {
                    mobileContainer.appendChild(content);
                }
            } else {
                // Move to desktop if not already there
                if (!sidebar.contains(content)) {
                    sidebar.appendChild(content);
                }
            }
        }

        function openMobileFilters() {
            document.getElementById('mobileFilters').classList.add('show');
            document.getElementById('filterOverlay').classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function closeMobileFilters() {
            document.getElementById('mobileFilters').classList.remove('show');
            document.getElementById('filterOverlay').classList.remove('show');
            document.body.style.overflow = '';
        }

        const mobileToggle = document.getElementById('mobileFilterToggle');
        if(mobileToggle) mobileToggle.addEventListener('click', openMobileFilters);
        
        const filterOverlay = document.getElementById('filterOverlay');
        if(filterOverlay) filterOverlay.addEventListener('click', closeMobileFilters);

        // Clear all filters
        function clearAllFilters() {
            // Uncheck all checkboxes
            document.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
            
            // Remove active color options
            document.querySelectorAll('.color-option.active').forEach(opt => opt.classList.remove('active'));
            
            // Clear price inputs
            document.querySelectorAll('input[name="min_price"], input[name="max_price"]').forEach(input => {
                input.value = '';
            });
            
            // Update color input
            updateColorInput();
            
            // Update active filters display
            updateActiveFilters();
            
            // Submit form
            document.getElementById('filtersForm').submit();
        }

        // Update active filters display
        function updateActiveFilters() {
            const container = document.getElementById('activeFilters');
            const form = document.getElementById('filtersForm');
            if(!form || !container) return;
            
            const formData = new FormData(form);
            
            let activeFilters = [];
            
            // Check categories
            const categories = formData.getAll('categories[]');
            if (categories.length) {
                categories.forEach(id => {
                    const label = document.querySelector(`input[name="categories[]"][value="${id}"]`)
                        ?.closest('.filter-label')?.querySelector('span:first-child')?.textContent || `Category ${id}`;
                    activeFilters.push({
                        label: label,
                        remove: () => {
                            const checkbox = document.querySelector(`input[name="categories[]"][value="${id}"]`);
                            if (checkbox) checkbox.checked = false;
                            form.submit();
                        }
                    });
                });
            }
            
            // Check price range
            const minPrice = formData.get('min_price');
            const maxPrice = formData.get('max_price');
            if (minPrice || maxPrice) {
                const label = minPrice && maxPrice ? 
                    `$${minPrice} - $${maxPrice}` : 
                    minPrice ? `From $${minPrice}` : `Up to $${maxPrice}`;
                activeFilters.push({
                    label: label,
                    remove: () => {
                        document.querySelector('input[name="min_price"]').value = '';
                        document.querySelector('input[name="max_price"]').value = '';
                        form.submit();
                    }
                });
            }
            
            // Check colors
            const colors = formData.get('colors')?.split(',').filter(c => c) || [];
            if (colors.length) {
                colors.forEach(color => {
                    activeFilters.push({
                        label: color.charAt(0).toUpperCase() + color.slice(1),
                        remove: () => {
                            const colorOption = document.querySelector(`.color-option[data-color="${color}"]`);
                            if (colorOption) colorOption.classList.remove('active');
                            updateColorInput();
                            form.submit();
                        }
                    });
                });
            }
            
            // Check availability
            const availability = formData.getAll('availability[]');
            if (availability.length) {
                availability.forEach(value => {
                    const label = value.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
                    activeFilters.push({
                        label: label,
                        remove: () => {
                            const checkbox = document.querySelector(`input[name="availability[]"][value="${value}"]`);
                            if (checkbox) checkbox.checked = false;
                            form.submit();
                        }
                    });
                });
            }
            
            // Update display
            container.innerHTML = '';
            if (activeFilters.length > 0) {
                activeFilters.forEach(filter => {
                    const tag = document.createElement('div');
                    tag.className = 'filter-tag';
                    tag.innerHTML = `${filter.label} <i class="fas fa-times"></i>`;
                    tag.addEventListener('click', filter.remove);
                    container.appendChild(tag);
                });
                container.style.display = 'flex';
            } else {
                container.style.display = 'none';
            }
        }

        // Price Range Logic
        const priceRange = document.getElementById('priceRange');
        const maxPriceInput = document.getElementById('maxPriceInput');
        const priceOutput = document.getElementById('priceOutput');

        if (priceRange && maxPriceInput) {
            // Slider updates input and output
            priceRange.addEventListener('input', function() {
                maxPriceInput.value = this.value;
                if(priceOutput) priceOutput.textContent = this.value;
            });

            // Input updates slider and output
            maxPriceInput.addEventListener('input', function() {
                let val = parseInt(this.value);
                if (val > 1000) val = 1000; // Max limit
                // if (val < 0) val = 0; // Min limit (optional)
                
                priceRange.value = val;
                if(priceOutput) priceOutput.textContent = val;
            });
        }
    </script>
@endsection