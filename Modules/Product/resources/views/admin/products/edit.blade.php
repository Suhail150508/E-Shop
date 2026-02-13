@extends('layouts.admin')

@section('page_title', __('Edit Product'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h4 fw-bold mb-1">{{ __('Edit Product') }}</h2>
        <p class="text-muted mb-0">{{ __('Update product information.') }}</p>
    </div>
    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary-soft">
        <i class="bi bi-arrow-left me-1"></i> {{ __('Back to List') }}
    </a>
</div>
<form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="row g-4">
        <div class="col-lg-8">
            @if($languages->count() > 1)
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">{{ __("common.switch_to_language_translation") }}</h5>
                    <ul class="nav nav-tabs nav-tabs-product-lang flex-nowrap overflow-auto pb-1" role="tablist">
                        @foreach($languages as $lang)
                        <li class="nav-item flex-shrink-0" role="presentation">
                            <button class="nav-link {{ $lang->code === $defaultLocale ? 'active' : '' }} product-lang-tab" type="button" data-bs-toggle="tab" data-bs-target="#product-lang-{{ $lang->code }}" data-locale="{{ $lang->code }}" aria-selected="{{ $lang->code === $defaultLocale ? 'true' : 'false' }}">
                                @if($lang->code === $defaultLocale)<i class="bi bi-eye me-1"></i>@else<i class="bi bi-pencil me-1"></i>@endif
                                {{ $lang->name }}
                            </button>
                        </li>
                        @endforeach
                    </ul>
                    <p class="small text-muted mb-0 mt-2"><span class="product-editing-mode-label">{{ __("common.your_editing_mode") }}: {{ $defaultLanguage?->name ?? $defaultLocale }}</span></p>
                </div>
            </div>
            @endif
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">{{ __('Product Information') }}</h5>
                    <div class="tab-content">
                        @foreach($languages as $lang)
                        @php $locale = $lang->code; $trans = $product->getTranslationValuesForLocale($locale); @endphp
                        <div class="tab-pane fade {{ $locale === $defaultLocale ? 'show active' : '' }}" id="product-lang-{{ $locale }}" role="tabpanel">
                            @if($locale === $defaultLocale)
                            <div class="mb-3">
                                <label for="name" class="form-label">{{ __('Product Name') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $product->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">{{ __('Description') }}</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="6">{{ old('description', $product->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            @else
                            <div class="mb-3">
                                <label for="name_{{ $locale }}" class="form-label">{{ __('Product Name') }} <span class="text-muted">({{ $lang->name }})</span></label>
                                <input type="text" class="form-control" id="name_{{ $locale }}" name="translations[{{ $locale }}][name]" value="{{ old('translations.'.$locale.'.name', $trans['name'] ?? '') }}" placeholder="{{ __("common.optional_translation") }}">
                            </div>
                            <div class="mb-3">
                                <label for="description_{{ $locale }}" class="form-label">{{ __('Description') }} <span class="text-muted">({{ $lang->name }})</span></label>
                                <textarea class="form-control" id="description_{{ $locale }}" name="translations[{{ $locale }}][description]" rows="6" placeholder="{{ __("common.optional_translation") }}">{{ old('translations.'.$locale.'.description', $trans['description'] ?? '') }}</textarea>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">{{ __('Pricing & Inventory') }}</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="price" class="form-label">{{ __('Price') }} <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">{{ default_currency()?->symbol ?? '$' }}</span>
                                    <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price', $product->price) }}" required>
                                </div>
                                @error('price')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="discount_price" class="form-label">{{ __('Discount Price') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text">{{ default_currency()?->symbol ?? '$' }}</span>
                                    <input type="number" step="0.01" class="form-control @error('discount_price') is-invalid @enderror" id="discount_price" name="discount_price" value="{{ old('discount_price', $product->discount_price) }}">
                                </div>
                                @error('discount_price')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sku" class="form-label">{{ __('SKU') }}</label>
                                <input type="text" class="form-control @error('sku') is-invalid @enderror" id="sku" name="sku" value="{{ old('sku', $product->sku) }}">
                                @error('sku')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="stock" class="form-label">{{ __('Stock Quantity') }} <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('stock') is-invalid @enderror" id="stock" name="stock" value="{{ old('stock', $product->stock) }}" required>
                                @error('stock')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="weight" class="form-label">{{ __('common.weight') }}</label>
                                <input type="text" class="form-control @error('weight') is-invalid @enderror" id="weight" name="weight" value="{{ old('weight', $product->weight) }}" placeholder="{{ __('e.g. 0.5 kg') }}">
                                @error('weight')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="dimensions" class="form-label">{{ __('common.dimensions') }}</label>
                                <input type="text" class="form-control @error('dimensions') is-invalid @enderror" id="dimensions" name="dimensions" value="{{ old('dimensions', $product->dimensions) }}" placeholder="{{ __('e.g. 10 x 20 x 5 cm') }}">
                                @error('dimensions')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">{{ __('Attributes') }}</h5>
                    <div class="mb-4">
                        <label class="form-label d-block">{{ __('Colors') }}</label>
                        <div class="d-flex flex-wrap gap-3">
                            @foreach($colors as $color)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="colors[]" id="color_{{ $loop->index }}" value="{{ $color->name }}" {{ in_array($color->name, old('colors', $product->colors ?? [])) ? 'checked' : '' }}>
                                    <label class="form-check-label d-flex align-items-center" for="color_{{ $loop->index }}">
                                        <span class="d-inline-block rounded-circle border me-2" style="width: 16px; height: 16px; background-color: {{ $color->code }};"></span>
                                        {{ $color->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        @error('colors')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-4">
                            <label class="form-label d-block">{{ __('common.sizes') }}</label>
                            <div class="d-flex flex-wrap gap-3">
                            @foreach($sizes as $size)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="sizes[]" id="size_{{ $loop->index }}" value="{{ $size->name }}" {{ in_array($size->name, old('sizes', $product->sizes ?? [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="size_{{ $loop->index }}">
                                        {{ $size->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        @error('sizes')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-2">
                        <label class="form-label d-block">{{ __('Tags') }}</label>
                        <div class="input-group mb-2">
                            <input type="text" id="tags-input" class="form-control" placeholder="{{ __('Type a tag and press Enter') }}">
                            <button type="button" id="add-tag-btn" class="btn btn-secondary-soft">{{ __('Add Tag') }}</button>
                        </div>
                        <div id="tags-container" class="d-flex flex-wrap gap-2"></div>
                        @error('tags')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">{{ __('Search Engine Optimization (SEO)') }}</h5>
                    <div class="mb-3">
                        <label for="meta_title" class="form-label">{{ __('Meta Title') }}</label>
                        <input type="text" class="form-control @error('meta_title') is-invalid @enderror" id="meta_title" name="meta_title" value="{{ old('meta_title', $product->meta_title) }}">
                        @error('meta_title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="meta_description" class="form-label">{{ __('Meta Description') }}</label>
                        <textarea class="form-control @error('meta_description') is-invalid @enderror" id="meta_description" name="meta_description" rows="3">{{ old('meta_description', $product->meta_description) }}</textarea>
                        @error('meta_description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="meta_keywords" class="form-label">{{ __('Meta Keywords') }}</label>
                        <input type="text" class="form-control @error('meta_keywords') is-invalid @enderror" id="meta_keywords" name="meta_keywords" value="{{ old('meta_keywords', $product->meta_keywords) }}">
                        @error('meta_keywords')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">{{ __('Status & Organization') }}</h5>
                    <div class="mb-3">
                        <label class="form-label d-block">{{ __('Product Status') }}</label>
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="is_active">{{ __('Active') }}</label>
                            <div class="form-text text-muted">{{ __('Enable to show this product on your store.') }}</div>
                        </div>
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="is_featured">{{ __('Featured') }}</label>
                            <div class="form-text text-muted">{{ __('Show in featured products section.') }}</div>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_flash_sale" name="is_flash_sale" value="1" {{ old('is_flash_sale', $product->is_flash_sale) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="is_flash_sale">{{ __('Flash Sale') }}</label>
                            <div class="form-text text-muted">{{ __('Include in flash sale promotions.') }}</div>
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="is_tryable" name="is_tryable" value="1" {{ old('is_tryable', $product->is_tryable ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="is_tryable">{{ __('common.enable_virtual_try_on') }}</label>
                            <div class="form-text text-muted">{{ __('common.tryon_product_hint') }}</div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="category_id" class="form-label">{{ __('Category') }} <span class="text-danger">*</span></label>
                        <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                            <option value="">{{ __('Select Category') }}</option>
                            @foreach($categories as $id => $name)
                                <option value="{{ $id }}" {{ old('category_id', $product->category_id) == $id ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="subcategory_id" class="form-label">{{ __('Sub Category') }}</label>
                        <select class="form-select @error('subcategory_id') is-invalid @enderror" id="subcategory_id" name="subcategory_id">
                            <option value="">{{ __('Select Sub Category') }}</option>
                        </select>
                        @error('subcategory_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="brand_id" class="form-label">{{ __('Brand') }}</label>
                        <select class="form-select @error('brand_id') is-invalid @enderror" id="brand_id" name="brand_id">
                            <option value="">{{ __('Select Brand') }}</option>
                            @foreach($brands as $id => $name)
                                <option value="{{ $id }}" {{ old('brand_id', $product->brand_id) == $id ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                        @error('brand_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="unit_id" class="form-label">{{ __('Unit') }}</label>
                        <select class="form-select @error('unit_id') is-invalid @enderror" id="unit_id" name="unit_id">
                            <option value="">{{ __('Select Unit') }}</option>
                            @foreach($units as $id => $name)
                                <option value="{{ $id }}" {{ old('unit_id', $product->unit_id) == $id ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                        @error('unit_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">{{ __('Media') }}</h5>
                    <div class="mb-4">
                        <label class="form-label fw-bold">{{ __('Main Image') }}</label>
                        <div class="mb-3 text-center">
                            @if($product->image)
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="rounded shadow-sm mb-2" style="max-width: 100%; height: auto; max-height: 200px;">
                            @else
                                <div class="bg-light rounded p-4 mb-2 text-center">
                                    <i class="bi bi-image display-4 text-muted"></i>
                                    <div class="small text-muted mt-2">{{ __('No image') }}</div>
                                </div>
                            @endif
                        </div>
                        <input type="file" class="form-control form-control-sm @error('image') is-invalid @enderror" id="image" name="image">
                        <div class="form-text mt-1">{{ __('Upload to replace current image.') }}</div>
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('Gallery Images') }}</label>
                        <p class="form-text small text-muted mb-2">{{ __('JPEG, PNG or WebP. Max 2MB per image.') }}</p>
                        @if($product->images && count($product->images) > 0)
                            <div class="gallery-existing mb-3">
                                <span class="small text-muted d-block mb-2">{{ __('Current gallery') }}</span>
                                <div class="d-flex flex-wrap gap-2 gallery-existing-list">
                                    @foreach($product->images as $image)
                                        <div class="gallery-thumb-wrap rounded border bg-white shadow-sm position-relative overflow-hidden" style="width: 72px; height: 72px;">
                                            <img src="{{ $image->image_url }}" alt="" class="w-100 h-100 object-fit-cover">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        <div class="gallery-drop-zone rounded-3 border border-2 bg-light position-relative overflow-hidden" id="galleryDropZone" role="button" tabindex="0" data-remove-title="{{ __("common.remove") }}">
                            <input type="file" class="d-none @error('gallery_images') is-invalid @enderror @error('gallery_images.*') is-invalid @enderror" id="gallery_images" name="gallery_images[]" multiple accept="image/jpeg,image/png,image/jpg,image/webp">
                            <div class="gallery-drop-zone-inner text-center py-4 px-3">
                                <i class="bi bi-cloud-arrow-up display-5 text-primary mb-2"></i>
                                <div class="fw-semibold text-dark mb-1">{{ __('Drag & drop images here') }}</div>
                                <div class="small text-muted">{{ __('or click to browse') }}</div>
                            </div>
                            @error('gallery_images')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            @error('gallery_images.*')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="gallery-preview-list d-flex flex-wrap gap-2 mt-3" id="galleryPreviewList"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="d-flex justify-content-end gap-2 mt-3 pb-5">
        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary-soft">
            {{ __('Cancel') }}
        </a>
        <button type="submit" class="btn btn-primary px-4">
            <i class="bi bi-save me-1"></i> {{ __('Update Product') }}
        </button>
    </div>
</form>
@push('styles')
<link rel="stylesheet" href="{{ asset('backend/css/gallery-upload.css') }}">
<style>
.nav-tabs-product-lang .nav-link { border: 1px solid #dee2e6; margin-right: 4px; border-radius: 6px; font-weight: 500; }
.nav-tabs-product-lang .nav-link.active { background: #e7f1ff; border-color: #0d6efd; color: #0d6efd; }
.nav-tabs-product-lang .nav-link:hover:not(.active) { background: #f8f9fa; }
@media (max-width: 576px) { .nav-tabs-product-lang { overflow-x: auto; -webkit-overflow-scrolling: touch; } .nav-tabs-product-lang .nav-link { font-size: 0.875rem; padding: 0.5rem 0.75rem; } }
</style>
@endpush
@push('scripts')
    <script src="{{ asset('backend/vendor/tinymce/tinymce.min.js') }}"></script>
    <script src="{{ asset('backend/js/gallery-drag-drop.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof tinymce !== 'undefined') {
                tinymce.init({
                    selector: '#description',
                    menubar: false,
                    plugins: 'link lists',
                    toolbar: 'undo redo | bold italic | bullist numlist | link',
                    height: 300,
                    setup: function (editor) {
                        editor.on('change', function () {
                            editor.save();
                        });
                    }
                });
            }
            const categorySelect = document.getElementById('category_id');
            const subcategorySelect = document.getElementById('subcategory_id');
            const oldSubcategoryId = "{{ old('subcategory_id', $product->subcategory_id) }}";
            function loadSubcategories(categoryId, selectedId = null) {
                subcategorySelect.innerHTML = '<option value="">{{ __("Loading...") }}</option>';
                subcategorySelect.disabled = true;
                if (!categoryId) {
                    subcategorySelect.innerHTML = '<option value="">{{ __("Select Sub Category") }}</option>';
                    subcategorySelect.disabled = false;
                    return;
                }
                fetch(`{{ url('admin/categories') }}/${categoryId}/subcategories`)
                    .then(response => response.json())
                    .then(data => {
                        subcategorySelect.innerHTML = '<option value="">{{ __("Select Sub Category") }}</option>';
                        data.forEach(subcategory => {
                            const option = document.createElement('option');
                            option.value = subcategory.id;
                            option.textContent = subcategory.name;
                            if (selectedId && subcategory.id == selectedId) {
                                option.selected = true;
                            }
                            subcategorySelect.appendChild(option);
                        });
                        subcategorySelect.disabled = false;
                    })
                    .catch(error => {
                        subcategorySelect.innerHTML = '<option value="">{{ __("Error loading subcategories") }}</option>';
                        subcategorySelect.disabled = false;
                    });
            }
            categorySelect.addEventListener('change', function() {
                loadSubcategories(this.value);
            });
            if (categorySelect.value) {
                loadSubcategories(categorySelect.value, oldSubcategoryId);
            }
            const tagsContainer = document.getElementById('tags-container');
            const tagsInput = document.getElementById('tags-input');
            const addTagBtn = document.getElementById('add-tag-btn');
            function createHiddenInput(name, value) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = name;
                input.value = value;
                return input;
            }
            function addTag(tag) {
                if (!tag.trim()) return;
                const wrapper = document.createElement('div');
                wrapper.className = 'badge bg-secondary d-flex align-items-center gap-2 p-2';
                const span = document.createElement('span');
                span.textContent = tag;
                const removeIcon = document.createElement('i');
                removeIcon.className = 'bi bi-x-lg';
                removeIcon.style.cursor = 'pointer';
                removeIcon.style.fontSize = '0.8em';
                removeIcon.onclick = function() { wrapper.remove(); };
                wrapper.appendChild(span);
                wrapper.appendChild(createHiddenInput('tags[]', tag));
                wrapper.appendChild(removeIcon);
                tagsContainer.appendChild(wrapper);
            }
            addTagBtn.addEventListener('click', () => {
                addTag(tagsInput.value);
                tagsInput.value = '';
            });
            tagsInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    addTag(tagsInput.value);
                    tagsInput.value = '';
                }
            });
            const existingTags = @json($product->tags ?? []);
            if (Array.isArray(existingTags)) {
                existingTags.forEach(tag => addTag(tag));
            }
            var productLocaleLabels = @json($languages->pluck('name', 'code')->toArray());
            document.querySelectorAll('.product-lang-tab').forEach(function(tab) {
                tab.addEventListener('shown.bs.tab', function() {
                    var locale = this.getAttribute('data-locale');
                    var label = productLocaleLabels[locale] || locale;
                    var el = document.querySelector('.product-editing-mode-label');
                    if (el) el.textContent = '{{ __("common.your_editing_mode") }}: ' + label;
                });
            });
        });
    </script>
@endpush
@endsection
