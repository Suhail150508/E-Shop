@extends('layouts.admin')

@section('page_title', __('Staff Management'))

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <div class="row g-3 align-items-center">
            <div class="col-md-6">
                <h5 class="mb-0"><i class="fas fa-users me-2"></i>{{ __('Staff Members') }}</h5>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('admin.staff.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i> {{ __('Add New Staff') }}
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
                        <th>{{ __('Assigned Orders') }}</th>
                        <th>{{ __('Joined Date') }}</th>
                        <th class="text-end pe-4">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($staffMembers as $staff)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-circle bg-primary text-white d-flex align-items-center justify-content-center rounded-circle" style="width: 32px; height: 32px; font-size: 14px;">
                                        {{ substr($staff->name, 0, 1) }}
                                    </div>
                                    <span class="fw-medium">{{ $staff->name }}</span>
                                </div>
                            </td>
                            <td>{{ $staff->email }}</td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3">
                                    {{ $staff->assigned_orders_count }}
                                </span>
                            </td>
                            <td>{{ $staff->created_at->format('M d, Y') }}</td>
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    <a href="{{ route('admin.staff.edit', $staff->id) }}" class="btn btn-sm btn-outline-primary" title="{{ __('Edit') }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.staff.destroy', $staff->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this staff member?') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ __('Delete') }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="fas fa-users-slash fa-2x mb-3 opacity-50"></i>
                                <p class="mb-0">{{ __('No staff members found.') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($staffMembers->hasPages())
            <div class="card-footer bg-white py-3">
                {{ $staffMembers->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
