@extends('layouts.admin')

@section('page_title', __('Create Language'))

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-secondary">{{ __('Create Language') }}</h5>
                    <a href="{{ route('admin.language.index') }}" class="btn btn-secondary-soft">
                        <i class="fas fa-arrow-left me-1"></i> {{ __('Back') }}
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.language.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">{{ __('Language Name') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="e.g. English" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="code" class="form-label">{{ __('Language Code') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code') }}" placeholder="e.g. en" required>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="direction" class="form-label">{{ __('Direction') }} <span class="text-danger">*</span></label>
                            <select class="form-select @error('direction') is-invalid @enderror" id="direction" name="direction" required>
                                <option value="ltr" {{ old('direction') == 'ltr' ? 'selected' : '' }}>{{ __('Left to Right (LTR)') }}</option>
                                <option value="rtl" {{ old('direction') == 'rtl' ? 'selected' : '' }}>{{ __('Right to Left (RTL)') }}</option>
                            </select>
                            @error('direction')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="is_default" name="is_default" value="1" {{ old('is_default') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_default">{{ __('Make this the default language') }}</label>
                        </div>
                        <div class="form-text">{{ __('If checked, other languages will be unset as default.') }}</div>
                    </div>

                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="status" name="status" value="1" {{ old('status', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="status">{{ __('Active Status') }}</label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary px-4">
                            {{ __('Save Language') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
