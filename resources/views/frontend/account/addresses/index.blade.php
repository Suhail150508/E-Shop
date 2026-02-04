@extends('layouts.customer')

@section('title', __('Addresses'))

@section('account_content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold mb-0">{{ __('Addresses') }}</h5>
        <a href="{{ route('customer.addresses.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
            <i class="fa-solid fa-plus me-2"></i> {{ __('Add New Address') }}
        </a>
    </div>

    @if($addresses->isEmpty())
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body text-center py-5">
                <i class="fa-solid fa-location-dot fa-3x text-muted opacity-25 mb-3"></i>
                <p class="text-muted mb-0">{{ __('No addresses saved yet.') }}</p>
            </div>
        </div>
    @else
        <div class="row g-4">
            @foreach($addresses as $address)
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-3 h-100">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <div class="fw-bold fs-6 text-dark d-flex align-items-center">
                                        {{ $address->label ?: __('Address') }}
                                        @if($address->is_default)
                                            <span class="badge rounded-pill bg-primary bg-opacity-10 text-white ms-2 text-uppercase address-font-xs">{{ __('Default') }}</span>
                                        @endif
                                    </div>
                                    <div class="text-muted small text-uppercase fw-semibold mt-1">
                                        {{ $address->type }}
                                    </div>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light rounded-circle shadow-sm" type="button" data-bs-toggle="dropdown">
                                        <i class="fa-solid fa-ellipsis-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm rounded-3">
                                        <li>
                                            <a href="{{ route('customer.addresses.edit', $address) }}" class="dropdown-item">
                                                <i class="fa-solid fa-pen-to-square me-2"></i> {{ __('Edit') }}
                                            </a>
                                        </li>
                                        @if(!$address->is_default)
                                            <li>
                                                <form method="POST" action="{{ route('customer.addresses.default', $address) }}">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item">
                                                        <i class="fa-solid fa-check me-2"></i> {{ __('Set as Default') }}
                                                    </button>
                                                </form>
                                            </li>
                                        @endif
                                        <li>
                                            <form method="POST" action="{{ route('customer.addresses.destroy', $address) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="fa-solid fa-trash me-2"></i> {{ __('Delete') }}
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="text-muted small mb-0">
                                <div class="d-flex mb-1"><i class="fa-solid fa-map-pin me-2 mt-1 opacity-50 address-icon-width"></i> <span>{{ $address->line1 }}</span></div>
                                @if($address->line2)
                                    <div class="d-flex mb-1"><i class="fa-solid fa-map-pin me-2 mt-1 opacity-50 address-icon-width"></i> <span>{{ $address->line2 }}</span></div>
                                @endif
                                <div class="d-flex mb-1"><i class="fa-solid fa-city me-2 mt-1 opacity-50 address-icon-width"></i> <span>{{ $address->city }}, {{ $address->state }} {{ $address->postal_code }}</span></div>
                                <div class="d-flex mb-1"><i class="fa-solid fa-globe me-2 mt-1 opacity-50 address-icon-width"></i> <span>{{ $address->country }}</span></div>
                                @if($address->phone)
                                    <div class="d-flex mt-2"><i class="fa-solid fa-phone me-2 mt-1 opacity-50 address-icon-width"></i> <span>{{ $address->phone }}</span></div>
                                @endif
                                @if($address->email)
                                    <div class="d-flex mt-1"><i class="fa-solid fa-envelope me-2 mt-1 opacity-50 address-icon-width"></i> <span>{{ $address->email }}</span></div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
