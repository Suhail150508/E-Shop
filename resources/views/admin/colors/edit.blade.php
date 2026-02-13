@extends('layouts.admin')

@section('page_title', __('Edit Color'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h4 fw-bold mb-1">{{ __('Edit Color') }}</h2>
        <p class="text-muted mb-0">{{ __('Update product color.') }}</p>
    </div>
    <a href="{{ route('admin.colors.index') }}" class="btn btn-secondary-soft">
        <i class="bi bi-arrow-left me-1"></i> {{ __('Back to List') }}
    </a>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
        <form action="{{ route('admin.colors.update', $color->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="mb-3">
                        <label for="name" class="form-label">{{ __('Name') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-lg @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $color->name) }}" placeholder="{{ __('e.g. Red, Blue, Navy') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="code" class="form-label">{{ __('Color Code') }} <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="color" class="form-control form-control-color" id="code_picker" value="{{ old('code', $color->code) }}" title="{{ __('Choose your color') }}">
                            <input type="text" class="form-control form-control-lg @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $color->code) }}" placeholder="{{ __('e.g. #FF0000') }}" required>
                        </div>
                        <div class="form-text text-muted">{{ __('Enter hex color code or pick from the color picker.') }}</div>
                        @error('code')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="card bg-light border-0">
                        <div class="card-body">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $color->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="is_active">{{ __('Active') }}</label>
                                <div class="form-text text-muted">{{ __('Enable or disable this color.') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-start gap-2 mt-4 pt-3 border-top">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-save me-1"></i> {{ __('Update Color') }}
                </button>
                <a href="{{ route('admin.colors.index') }}" class="btn btn-secondary-soft">
                    {{ __('Cancel') }}
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('code_picker').addEventListener('input', function() {
        document.getElementById('code').value = this.value;
    });
    
    document.getElementById('code').addEventListener('input', function() {
        if (this.value.match(/^#[0-9A-Fa-f]{6}$/)) {
            document.getElementById('code_picker').value = this.value;
        }
    });
</script>
@endpush
@endsection
