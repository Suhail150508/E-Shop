@extends('layouts.admin')

@section('page_title', __('Products'))

@section('content')
<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h4 fw-bold mb-0">
        <i class="fas fa-box me-2 text-primary"></i>{{ __('Product') }}
    </h2>
    <a href="{{ route('admin.products.create') }}" class="btn btn-primary rounded-3 px-4">
        <i class="fas fa-plus me-2"></i> {{ __('Add Product') }}
    </a>
</div>

<!-- Filter Section -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <form action="{{ route('admin.products.index') }}" method="GET">
            <div class="row g-2">
                <div class="col-12 col-md-2">
                    <select name="status" class="form-select border-0 bg-light rounded-3 py-2 fs-14">
                        <option value="">{{ __('Status') }}</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>{{ __('Active') }}</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <select name="category_id" class="form-select border-0 bg-light rounded-3 py-2 fs-14">
                        <option value="">{{ __('Category') }}</option>
                        @foreach($categories as $id => $name)
                            <option value="{{ $id }}" {{ request('category_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <select name="brand_id" class="form-select border-0 bg-light rounded-3 py-2 fs-14">
                        <option value="">{{ __('Brand') }}</option>
                        @foreach($brands as $id => $name)
                            <option value="{{ $id }}" {{ request('brand_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-6">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control border-0 bg-light rounded-start-3 py-2 fs-14" placeholder="{{ __('Search by name') }}" value="{{ old('search', request('search')) }}">
                        <button type="submit" class="btn btn-primary rounded-end-3 px-3">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Product Table -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0 table-hover">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 w-50px"></th> <!-- Expand Toggle -->
                        <th class="text-center w-60px">{{ __('SL') }}</th>
                        <th>{{ __('Product Info') }}</th>
                        <th>{{ __('Category') }}</th>
                        <th>{{ __('Brand') }}</th>
                        <th class="text-center">{{ __('Status') }}</th>
                        <th class="text-end pe-4">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $index => $product)
                        <tr class="product-row">
                            <td class="ps-4">
                                <button class="btn btn-sm btn-light rounded-circle shadow-sm border toggle-details" type="button" data-bs-toggle="collapse" data-bs-target="#details-{{ $product->id }}" aria-expanded="false">
                                    <i class="fas fa-plus text-primary"></i>
                                </button>
                            </td>
                            <td class="text-center fw-medium text-muted" data-label="{{ __('SL') }}">
                                {{ $products->firstItem() + $index }}
                            </td>
                            <td data-label="{{ __('Product Info') }}">
                                <div class="d-flex align-items-center">
                                    <div class="position-relative me-3">
                                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="avatar-sm rounded-3 border">
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold text-dark">{{ $product->name }}</h6>
                                    </div>
                                </div>
                            </td>
                            <td data-label="{{ __('Category') }}">
                                <span class="fw-medium text-dark">{{ $product->category->name ?? __('Uncategorized') }}</span>
                            </td>
                            <td data-label="{{ __('Brand') }}">
                                <span class="text-muted">{{ $product->brand->name ?? __('No Brand') }}</span>
                            </td>
                            <td class="text-center" data-label="{{ __('Status') }}">
                                @if($product->is_active)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3">
                                        {{ __('Active') }}
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3">
                                        {{ __('Inactive') }}
                                    </span>
                                @endif
                            </td>
                            <td class="text-end pe-4" data-label="{{ __('Actions') }}">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('shop.product.show', $product->slug) }}" target="_blank" class="btn btn-sm btn-info-soft rounded-2" title="{{ __('View') }}">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-sm btn-primary-soft rounded-2" title="{{ __('Edit') }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="d-inline-block delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger-soft rounded-2" title="{{ __('Delete') }}" onclick="return confirm('{{ __('common.confirm_delete_product') }}')">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <!-- Expandable Details Row -->
                        <tr class="collapse bg-light" id="details-{{ $product->id }}">
                            <td colspan="6" class="p-0 border-0">
                                <div class="p-4">
                                    <div class="card border-0 shadow-sm rounded-3">
                                        <div class="card-body p-0">
                                            <table class="table table-sm align-middle mb-0">
                                                <thead class="bg-primary-subtle text-primary">
                                                    <tr>
                                                        <th class="ps-3">{{ __('Image') }}</th>
                                                        <th>{{ __('SKU') }}</th>
                                                        <th>{{ __('Price') }}</th>
                                                        <th>{{ __('Special Price') }}</th>
                                                        <th>{{ __('Stock') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td class="ps-3 py-3">
                                                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="rounded-2 border" width="40" height="40">
                                                        </td>
                                                        <td class="fw-medium text-dark">{{ $product->sku ?? __('N/A') }}</td>
                                                        <td class="fw-bold text-dark">{{ format_price($product->price) }}</td>
                                                        <td class="fw-bold text-danger">
                                                            {{ $product->discount_price ? format_price($product->discount_price) : '-' }}
                                                        </td>
                                                        <td>
                                                            <span class="badge {{ $product->stock > 0 ? 'bg-success' : 'bg-danger' }}">
                                                                {{ $product->stock }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    {{-- If we had variants, we would loop them here --}}
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="empty-state">
                                    <div class="empty-state-icon bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3 empty-state-icon-large">
                                        <i class="fas fa-box-open fa-2x text-muted"></i>
                                    </div>
                                    <h5 class="fw-bold text-dark mb-1">{{ __('common.no_products_found') }}</h5>
                                    <p class="text-muted">{{ __('Try adjusting your search or filters.') }}</p>
                                    <a href="{{ route('admin.products.create') }}" class="btn btn-primary px-4 mt-2">
                                        <i class="fas fa-plus me-2"></i>{{ __('Add New Product') }}
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($products->hasPages())
            <div class="card-footer bg-white border-top-0 py-3">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleButtons = document.querySelectorAll('.toggle-details');
        toggleButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const icon = this.querySelector('i');
                if (this.getAttribute('aria-expanded') === 'true') {
                    icon.classList.remove('fa-plus');
                    icon.classList.add('fa-minus');
                } else {
                    icon.classList.remove('fa-minus');
                    icon.classList.add('fa-plus');
                }
            });
        });
    });
</script>
@endpush
