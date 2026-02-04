@extends('layouts.admin')

@section('page_title', __('Add Subcategory'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h4 fw-bold mb-1">{{ __('Add Subcategory') }}</h2>
        <p class="text-muted mb-0">{{ __('Create a new subcategory.') }}</p>
    </div>
    <a href="{{ route('admin.subcategories.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> {{ __('Back to List') }}
    </a>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
        <form action="{{ route('admin.subcategories.store') }}" method="POST">
            @csrf

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="mb-3">
                        <label for="parent_id" class="form-label">{{ __('Parent Category') }} <span class="text-danger">*</span></label>
                        <select class="form-select form-select-lg @error('parent_id') is-invalid @enderror" id="parent_id" name="parent_id" required>
                            <option value="">{{ __('Select Parent Category') }}</option>
                            @foreach($parents as $id => $parentName)
                                <option value="{{ $id }}" {{ (string) old('parent_id') === (string) $id ? 'selected' : '' }}>
                                    {{ $parentName }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text text-muted">{{ __('Select the parent category this subcategory belongs to.') }}</div>
                        @error('parent_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label">{{ __('Name') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-lg @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="{{ __('e.g. Wireless Headphones') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="card bg-light border-0">
                        <div class="card-body">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="is_active">{{ __('Active') }}</label>
                                <div class="form-text text-muted">{{ __('Enable or disable this subcategory on the storefront.') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="alert alert-info border-0 shadow-sm">
                        <h6 class="alert-heading fw-bold"><i class="bi bi-info-circle me-2"></i>{{ __('About Subcategories') }}</h6>
                        <p class="mb-0 small">{{ __('Subcategories help organize your products within a main category. For example, \"Headphones\" can be a subcategory of \"Electronics\".') }}</p>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <a href="{{ route('admin.subcategories.index') }}" class="btn btn-light">
                    {{ __('Cancel') }}
                </a>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-save me-1"></i> {{ __('Save Subcategory') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
