@extends('layouts.admin')

@section('page_title', __('Support Departments'))

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-layer-group me-2"></i>{{ __('Departments') }}</h5>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createDepartmentModal">
                <i class="fas fa-plus me-1"></i>{{ __('Add New') }}
            </button>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="px-4 py-3">{{ __('SL') }}</th>
                        <th class="py-3">{{ __('Name') }}</th>
                        <th class="py-3 text-center">{{ __('Status') }}</th>
                        <th class="py-3 text-end px-4">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($departments as $index => $department)
                        <tr>
                            <td class="px-4">{{ $departments->firstItem() + $index }}</td>
                            <td>{{ $department->name }}</td>
                            <td class="text-center">
                                @if($department->is_active)
                                    <span class="badge bg-success">{{ __('Active') }}</span>
                                @else
                                    <span class="badge bg-danger">{{ __('Inactive') }}</span>
                                @endif
                            </td>
                            <td class="text-end px-4">
                                <button class="btn btn-sm btn-secondary-soft me-1" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editDepartmentModal{{ $department->id }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('admin.support-departments.destroy', $department->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger-soft">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <!-- Edit Modal -->
                        <div class="modal fade" id="editDepartmentModal{{ $department->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">{{ __('Edit Department') }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="{{ route('admin.support-departments.update', $department->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('Name') }}</label>
                                                <input type="text" name="name" class="form-control" value="{{ $department->name }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label d-block">{{ __('Status') }}</label>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="is_active" value="1" {{ $department->is_active ? 'checked' : '' }}>
                                                    <label class="form-check-label">{{ __('Active') }}</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="is_active" value="0" {{ !$department->is_active ? 'checked' : '' }}>
                                                    <label class="form-check-label">{{ __('Inactive') }}</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary-soft" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                                            <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5">{{ __('No departments found.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($departments->hasPages())
        <div class="card-footer bg-white py-3">
            {{ $departments->links() }}
        </div>
    @endif
</div>

<!-- Create Modal -->
<div class="modal fade" id="createDepartmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Add New Department') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.support-departments.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('Name') }}</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label d-block">{{ __('Status') }}</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="is_active" value="1" checked>
                            <label class="form-check-label">{{ __('Active') }}</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="is_active" value="0">
                            <label class="form-check-label">{{ __('Inactive') }}</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary-soft" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Create') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
