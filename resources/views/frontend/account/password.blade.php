@extends('layouts.customer')

@section('title', __('Change Password'))

@section('account_content')
    <div class="card border-0 shadow-sm rounded-3 password-card">
        <div class="card-header bg-white border-bottom-0 py-3 px-4">
            <h5 class="mb-0 fw-bold">{{ __('Update Security Credentials') }}</h5>
        </div>
        <div class="card-body p-4">
            <form method="POST" action="{{ route('customer.password.update') }}">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <div class="form-floating">
                        <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" placeholder="{{ __('Current password') }}" required>
                        <label for="current_password">{{ __('Current Password') }}</label>
                    </div>
                    @error('current_password')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <div class="form-floating">
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="{{ __('New password') }}" required>
                        <label for="password">{{ __('New Password') }}</label>
                    </div>
                    @error('password')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    <div class="form-text text-muted small mt-2">
                        <i class="fa-solid fa-circle-info me-1"></i> {{ __('Password must be at least 8 characters long and contain mixed case, numbers, and symbols.') }}
                    </div>
                </div>

                <div class="mb-4">
                    <div class="form-floating">
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="{{ __('Confirm new password') }}" required>
                        <label for="password_confirmation">{{ __('Confirm New Password') }}</label>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">
                        {{ __('Update Password') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
