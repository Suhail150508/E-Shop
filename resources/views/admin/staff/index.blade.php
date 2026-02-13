@extends('layouts.admin')

@section('page_title', __('staff.management'))

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <div class="row g-3 align-items-center justify-content-between">
            <div class="col-md-4">
                <h5 class="mb-0"><i class="fas fa-users me-2"></i>{{ __('staff.staff_members') }}</h5>
            </div>
            <div class="col-md-8">
                <div class="d-flex justify-content-md-end gap-2">
                    <form action="{{ route('admin.staff.index') }}" method="GET" class="d-flex gap-2">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="{{ __('staff.search_placeholder') }}" value="{{ request('search') }}">
                        </div>
                        <button type="submit" class="btn btn-secondary-soft">{{ __('common.search') }}</button>
                    </form>
                    <a href="{{ route('admin.staff.create') }}" class="btn btn-primary text-nowrap">
                        <i class="fas fa-plus me-1"></i> {{ __('staff.add_new') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">{{ __('staff.name') }}</th>
                        <th>{{ __('staff.email') }}</th>
                        <th>{{ __('staff.assigned_orders') }}</th>
                        <th>{{ __('staff.joined_date') }}</th>
                        <th class="text-end pe-4">{{ __('staff.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($staffMembers as $staff)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-sm bg-primary text-white d-flex align-items-center justify-content-center rounded-circle">
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
                                <div class="btn-group gap-2">
                                    <a href="{{ route('admin.staff.edit', $staff->id) }}" class="btn btn-sm btn-secondary-soft" title="{{ __('staff.edit') }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.staff.destroy', $staff->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('common.delete_confirmation') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger-soft" title="{{ __('common.delete') }}">
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
                                <p class="mb-0">{{ __('staff.no_staff_found') }}</p>
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
