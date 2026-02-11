@extends('layouts.customer')

@section('title', __('common.change_password'))

@section('account_content')
    <div class="card border-0 shadow-sm rounded-3 password-card">
        <div class="card-header bg-white border-bottom-0 py-3 px-4">
            <h5 class="mb-0 fw-bold">{{ __('common.update_security_credentials') }}</h5>
        </div>
        <div class="card-body p-4">
            <form method="POST" action="{{ route('customer.password.update') }}">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <div class="form-floating">
                        <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" placeholder="{{ __('common.current_password') }}" required>
                        <label for="current_password">{{ __('common.current_password') }}</label>
                    </div>
                    @error('current_password')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <div class="form-floating">
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="{{ __('common.new_password') }}" required>
                        <label for="password">{{ __('common.new_password') }}</label>
                    </div>
                    @error('password')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    <div class="form-text text-muted small mt-2">
                        <i class="fa-solid fa-circle-info me-1"></i> {{ __('common.password_requirements') }}
                    </div>
                </div>

                <div class="mb-4">
                    <div class="form-floating">
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="{{ __('common.confirm_new_password') }}" required>
                        <label for="password_confirmation">{{ __('common.confirm_new_password') }}</label>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">
                        {{ __('common.update_password') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
