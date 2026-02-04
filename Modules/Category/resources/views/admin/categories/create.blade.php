@extends('layouts.admin')

@section('page_title', __('Add Category'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h4 fw-bold mb-1">{{ __('Add Category') }}</h2>
        <p class="text-muted mb-0">{{ __('Create a new product category.') }}</p>
    </div>
    <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> {{ __('Back to List') }}
    </a>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
        <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="mb-3">
                        <label for="name" class="form-label">{{ __('Name') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-lg @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="{{ __('e.g. Electronics') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="parent_id" class="form-label">{{ __('Parent Category') }}</label>
                        <select class="form-select form-select-lg @error('parent_id') is-invalid @enderror" id="parent_id" name="parent_id">
                            <option value="">{{ __('None (Root Category)') }}</option>
                            @foreach($parents as $id => $parentName)
                                <option value="{{ $id }}" {{ (string) old('parent_id') === (string) $id ? 'selected' : '' }}>
                                    {{ $parentName }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text text-muted">{{ __('Select a parent category if this is a sub-category.') }}</div>
                        @error('parent_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="card bg-light border-0">
                        <div class="card-body">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="is_active">{{ __('Active') }}</label>
                                <div class="form-text text-muted">{{ __('Enable or disable this category on the storefront.') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="card border border-dashed text-center h-100 bg-light">
                        <div class="card-body d-flex flex-column justify-content-center align-items-center">
                            <label for="image" class="form-label mb-3 fw-bold">{{ __('Category Image') }}</label>
                            <div class="mb-3">
                                <i class="bi bi-image display-4 text-muted"></i>
                            </div>
                            <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image">
                            <div class="form-text mt-2">{{ __('Recommended: 300x300px, JPG/PNG') }}</div>
                            @error('image')
                                <div class="invalid-feedback text-start w-100 mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <a href="{{ route('admin.categories.index') }}" class="btn btn-light">
                    {{ __('Cancel') }}
                </a>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-save me-1"></i> {{ __('Save Category') }}
                </button>
            </div>
        </form>
    </div>
    </div>
@endsection
