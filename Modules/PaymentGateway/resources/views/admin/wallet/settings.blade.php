@extends('layouts.admin')

@section('page_title', 'Wallet Settings')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0"><i class="fas fa-cog me-2"></i>{{ __('Wallet Settings') }}</h5>
    </div>
    <div class="card-body">
        <div class="alert alert-info d-flex align-items-center mb-4" role="alert">
            <i class="fas fa-info-circle me-2"></i>
            <div>
                {{ __('Note: You can save maximum deposit amount per transaction!') }}
            </div>
        </div>

        <form action="{{ route('admin.wallet.settings.update') }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label for="wallet_deposit_limit" class="form-label">{{ __('Deposit Maximum Amount') }} <i class="fas fa-info-circle text-muted" title="{{ __('Maximum amount allowed per deposit transaction') }}"></i></label>
                <input type="number" step="0.01" class="form-control" id="wallet_deposit_limit" name="wallet_deposit_limit" value="{{ old('wallet_deposit_limit', $settings['wallet_deposit_limit'] ?? 50000) }}">
                @error('wallet_deposit_limit')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary px-4">
                {{ __('Save Changes') }}
            </button>
        </form>
    </div>
</div>
@endsection
