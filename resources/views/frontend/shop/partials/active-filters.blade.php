@if(request()->anyFilled(['min_price', 'max_price', 'colors', 'sizes', 'unit_id', 'tags', 'rating', 'availability', 'is_tryable']))
    <div class="active-filters-list d-flex flex-wrap gap-2 mb-3">
        @if(request()->filled('min_price') || request()->filled('max_price'))
            <span class="badge bg-light text-dark border d-flex align-items-center gap-2 px-3 py-2">
                <span>
                    {{ __('common.price') }}: 
                    {{ current_currency()?->symbol ?? '$' }}{{ request('min_price', 0) }} - 
                    {{ current_currency()?->symbol ?? '$' }}{{ request('max_price', 1000) }}
                </span>
                <a href="javascript:void(0)" onclick="removeFilter('price')" class="text-danger"><i class="fas fa-times"></i></a>
            </span>
        @endif

        @php
            $colorRequest = request('colors');
            $activeColors = is_array($colorRequest) ? $colorRequest : ($colorRequest ? explode(',', $colorRequest) : []);
        @endphp
        @foreach($activeColors as $color)
            @if(!empty($color))
                <span class="badge bg-light text-dark border d-flex align-items-center gap-2 px-3 py-2">
                    <span>{{ __('common.color') }}: {{ $color }}</span>
                    <a href="javascript:void(0)" onclick="removeFilter('colors', '{{ $color }}')" class="text-danger"><i class="fas fa-times"></i></a>
                </span>
            @endif
        @endforeach

        @foreach((array)request('sizes', []) as $size)
            <span class="badge bg-light text-dark border d-flex align-items-center gap-2 px-3 py-2">
                <span>{{ __('common.size') }}: {{ $size }}</span>
                <a href="javascript:void(0)" onclick="removeFilter('sizes', '{{ $size }}')" class="text-danger"><i class="fas fa-times"></i></a>
            </span>
        @endforeach

        @foreach((array)request('unit_id', []) as $unitId)
            @php
                // Try to find unit name from the $units collection if available
                $unitName = $units->firstWhere('id', $unitId)?->name ?? $unitId;
            @endphp
            <span class="badge bg-light text-dark border d-flex align-items-center gap-2 px-3 py-2">
                <span>{{ __('common.unit') }}: {{ $unitName }}</span>
                <a href="javascript:void(0)" onclick="removeFilter('unit_id', '{{ $unitId }}')" class="text-danger"><i class="fas fa-times"></i></a>
            </span>
        @endforeach

        @foreach((array)request('tags', []) as $tag)
            <span class="badge bg-light text-dark border d-flex align-items-center gap-2 px-3 py-2">
                <span>{{ __('common.tag') }}: {{ $tag }}</span>
                <a href="javascript:void(0)" onclick="removeFilter('tags', '{{ $tag }}')" class="text-danger"><i class="fas fa-times"></i></a>
            </span>
        @endforeach

        @if(request('rating'))
            <span class="badge bg-light text-dark border d-flex align-items-center gap-2 px-3 py-2">
                <span>{{ request('rating') }} {{ __('common.stars_and_up') }}</span>
                <a href="javascript:void(0)" onclick="removeFilter('rating', '{{ request('rating') }}')" class="text-danger"><i class="fas fa-times"></i></a>
            </span>
        @endif

        @foreach((array)request('availability', []) as $avail)
            <span class="badge bg-light text-dark border d-flex align-items-center gap-2 px-3 py-2">
                <span>
                    @if($avail == 'in_stock') {{ __('common.in_stock') }}
                    @elseif($avail == 'on_sale') {{ __('common.on_sale') }}
                    @elseif($avail == 'new') {{ __('common.new_arrivals') }}
                    @else {{ $avail }}
                    @endif
                </span>
                <a href="javascript:void(0)" onclick="removeFilter('availability', '{{ $avail }}')" class="text-danger"><i class="fas fa-times"></i></a>
            </span>
        @endforeach

        @if(request('is_tryable'))
            <span class="badge bg-light text-dark border d-flex align-items-center gap-2 px-3 py-2">
                <span>{{ __('common.virtual_try_on') }}</span>
                <a href="javascript:void(0)" onclick="removeFilter('is_tryable')" class="text-danger"><i class="fas fa-times"></i></a>
            </span>
        @endif

        <a href="{{ route('shop.index') }}" class="btn btn-link text-danger text-decoration-none btn-sm align-self-center">
            {{ __('common.clear_all') }}
        </a>
    </div>

    @push('scripts')
    <script>
        function removeFilter(type, value) {
            const form = document.getElementById('filtersForm');
            if (!form) return;

            if (type === 'price') {
                const min = document.getElementById('minPriceInput');
                const max = document.getElementById('maxPriceInput');
                if(min) min.value = '';
                if(max) max.value = '';
                // Also reset slider if exists
                const slider = document.getElementById('priceRange');
                if(slider) slider.value = slider.max; 
            } else if (type === 'colors') {
                const input = document.getElementById('colorInput');
                if (input) {
                    let colors = input.value.split(',').filter(c => c !== value && c !== '');
                    input.value = colors.join(',');
                }
            } else if (['sizes', 'tags', 'availability', 'unit_id'].includes(type)) {
                const checkbox = form.querySelector(`input[name="${type}[]"][value="${value}"]`);
                if (checkbox) checkbox.checked = false;
            } else if (type === 'rating') {
                const radio = form.querySelector(`input[name="rating"][value="${value}"]`);
                if (radio) radio.checked = false;
            } else if (type === 'is_tryable') {
                const checkbox = form.querySelector('input[name="is_tryable"]');
                if (checkbox) checkbox.checked = false;
            }

            form.submit();
        }
    </script>
    @endpush
@endif
