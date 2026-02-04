@extends('layouts.customer')

@section('title', __('Profile'))

@section('account_content')
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white border-bottom-0 py-3 px-4">
            <h5 class="mb-0 fw-bold">{{ __('Edit Profile') }}</h5>
        </div>
        <div class="card-body p-4">
            <form method="POST" action="{{ route('customer.profile.update') }}">
                @csrf
                @method('PUT')
                
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" placeholder="{{ __('Full Name') }}" required>
                            <label for="name">{{ __('Full Name') }}</label>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" placeholder="{{ __('Email Address') }}" required>
                            <label for="email">{{ __('Email Address') }}</label>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">
                            {{ __('Save Changes') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
