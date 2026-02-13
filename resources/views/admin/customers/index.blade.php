@extends('layouts.admin')

@section('page_title', __('Customer Management'))

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <div class="row g-3 align-items-center">
            <div class="col-md-6">
                <h5 class="mb-0">
                    <span class="d-inline-flex align-items-center justify-content-center bg-primary-subtle rounded-circle me-2" style="width: 40px; height: 40px;">
                        <i class="fas fa-users text-primary"></i>
                    </span>
                    {{ __('Customers') }}
                </h5>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('admin.customers.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i> {{ __('Add New Customer') }}
                </a>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">{{ __('Name') }}</th>
                        <th>{{ __('Email') }}</th>
                        <th>{{ __('Total Orders') }}</th>
                        <th>{{ __('Joined Date') }}</th>
                        <th class="text-end pe-4">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-sm bg-primary text-white d-flex align-items-center justify-content-center rounded-circle">
                                    {{ substr($customer->name, 0, 1) }}
                                </div>
                                    <span class="fw-medium">{{ $customer->name }}</span>
                                </div>
                            </td>
                            <td>{{ $customer->email }}</td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3">
                                    {{ $customer->orders_count }}
                                </span>
                            </td>
                            <td>{{ $customer->created_at->format('M d, Y') }}</td>
                            <td class="text-end pe-4">
                                <div class="btn-group gap-2">
                                    <a href="{{ route('admin.customers.edit', $customer->id) }}" class="btn btn-sm btn-secondary-soft" title="{{ __('Edit') }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.customers.destroy', $customer->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this customer?') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger-soft" title="{{ __('Delete') }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fas fa-users-slash fa-2x mb-3 opacity-50"></i>
                                <p class="mb-0">{{ __('No customers found.') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($customers->hasPages())
            <div class="card-footer bg-white py-3">
                {{ $customers->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
