@extends('layouts.admin')

@section('page_title', __('Edit Currency'))

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-secondary">{{ __('Edit Currency') }}</h5>
                    <a href="{{ route('admin.currency.index') }}" class="btn btn-secondary-soft">
                        <i class="fas fa-arrow-left me-1"></i> {{ __('Back') }}
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.currency.update', $currency) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">{{ __('Currency Name') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $currency->name) }}" placeholder="e.g. US Dollar" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="code" class="form-label">{{ __('Currency Code') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $currency->code) }}" placeholder="e.g. USD" required>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="symbol" class="form-label">{{ __('Currency Symbol') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('symbol') is-invalid @enderror" id="symbol" name="symbol" value="{{ old('symbol', $currency->symbol) }}" placeholder="e.g. $" required>
                            @error('symbol')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="rate" class="form-label">{{ __('Exchange Rate (1 Default Currency = ?)') }} <span class="text-danger">*</span></label>
                        <input type="number" step="0.000001" class="form-control @error('rate') is-invalid @enderror" id="rate" name="rate" value="{{ old('rate', $currency->rate) }}" placeholder="e.g. 1.00" required>
                        <div class="form-text">{{ __('The exchange rate against the default currency.') }}</div>
                        @error('rate')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="is_default" name="is_default" value="1" {{ old('is_default', $currency->is_default) ? 'checked' : '' }} {{ $currency->is_default ? 'disabled' : '' }}>
                            <label class="form-check-label" for="is_default">{{ __('Make this the default currency') }}</label>
                        </div>
                        @if($currency->is_default)
                            <div class="form-text text-warning">{{ __('You cannot unset the default currency directly. Set another currency as default instead.') }}</div>
                            <input type="hidden" name="is_default" value="1">
                        @else
                            <div class="form-text">{{ __('If checked, other currencies will be unset as default.') }}</div>
                        @endif
                    </div>

                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="status" name="status" value="1" {{ old('status', $currency->status) ? 'checked' : '' }} {{ $currency->is_default ? 'disabled' : '' }}>
                            <label class="form-check-label" for="status">{{ __('Active Status') }}</label>
                        </div>
                        @if($currency->is_default)
                            <input type="hidden" name="status" value="1">
                        @endif
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary px-4">
                            {{ __('Update Currency') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
