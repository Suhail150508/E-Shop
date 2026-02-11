<aside class="filters-sidebar" id="desktopFilters">
    <div class="filter-card">
        <div class="filter-header">
            <h3>{{ __('common.filter_options') }}</h3>
            @if(request()->anyFilled(['categories', 'min_price', 'max_price', 'colors', 'availability', 'sizes', 'unit_id', 'tags', 'rating', 'is_tryable']))
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
                                   {{ (isset($currentCategory) && $currentCategory->id == $category->id) || in_array($category->id, (array)request('categories', [])) ? 'checked' : '' }}
                                   onchange="this.form.submit()">
                            <span class="filter-label">
                                <span>{{ $category->name }}</span>
                                <span class="filter-count">({{ ($category->products_count ?? 0) + ($category->sub_products_count ?? 0) }})</span>
                            </span>
                        </label>
                        @if($category->children->isNotEmpty())
                            @foreach($category->children as $child)
                                <label class="filter-option sub-category-indent">
                                    <input type="checkbox" name="categories[]" value="{{ $child->id }}"
                                           {{ (isset($currentCategory) && $currentCategory->id == $child->id) || in_array($child->id, (array)request('categories', [])) ? 'checked' : '' }}
                                           onchange="this.form.submit()">
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
                        <div class="d-flex justify-content-between mt-2 px-1 align-items-center">
                            <span class="text-muted small">{{ __('common.max') }} {{ current_currency()?->symbol ?? '$' }}<span id="priceOutput">{{ request('max_price', 1000) }}</span></span>
                            <button type="submit" class="btn btn-sm btn-primary py-0 px-2" style="font-size: 12px;">{{ __('common.filter') ?? 'Filter' }}</button>
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
                                       {{ in_array($size->name, (array)request('sizes', [])) ? 'checked' : '' }}
                                       onchange="this.form.submit()">
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
                                       {{ in_array($unit->id, (array)request('unit_id', [])) ? 'checked' : '' }}
                                       onchange="this.form.submit()">
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
                                       {{ in_array($tag, (array)request('tags', [])) ? 'checked' : '' }}
                                       onchange="this.form.submit()">
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
                                   {{ request('rating') == $rating ? 'checked' : '' }}
                                   onchange="this.form.submit()">
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
