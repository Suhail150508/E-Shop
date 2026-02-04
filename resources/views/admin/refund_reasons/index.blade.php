@extends('layouts.admin')

@section('page_title', __('Refund Reasons'))

@section('content')
<div class="d-flex flex-column gap-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <h2 class="h4 fw-bold mb-0">
            <i class="bi bi-list-check me-2 text-primary"></i>{{ __('Refund Reasons') }}
        </h2>
        <button type="button" class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#createReasonModal">
            <i class="bi bi-plus-lg me-1"></i> {{ __('Add New Reason') }}
        </button>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0 table-hover">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4" style="width: 60px;">{{ __('SL') }}</th>
                            <th>{{ __('Reason') }}</th>
                            <th class="text-center">{{ __('Status') }}</th>
                            <th class="text-end pe-4">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reasons as $index => $reason)
                            <tr>
                                <td class="ps-4 text-muted fw-medium" data-label="{{ __('SL') }}">
                                    {{ $reasons->firstItem() + $index }}
                                </td>
                                <td data-label="{{ __('Reason') }}">
                                    <span class="fw-medium text-dark">{{ $reason->reason }}</span>
                                </td>
                                <td class="text-center" data-label="{{ __('Status') }}">
                                    @if($reason->status)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1">{{ __('Active') }}</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-1">{{ __('Inactive') }}</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4" data-label="{{ __('Actions') }}">
                                    <div class="d-flex justify-content-end gap-2">
                                        <button type="button" class="btn btn-sm btn-soft-primary rounded-2" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editReasonModal{{ $reason->id }}" 
                                                title="{{ __('Edit') }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form action="{{ route('admin.refund-reasons.destroy', $reason->id) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-soft-danger rounded-2" title="{{ __('Delete') }}">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>

                                    <!-- Edit Modal -->
                                    <div class="modal fade" id="editReasonModal{{ $reason->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow">
                                                <div class="modal-header border-bottom-0">
                                                    <h5 class="modal-title fw-bold">{{ __('Edit Reason') }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form action="{{ route('admin.refund-reasons.update', $reason->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-body text-start">
                                                        <div class="mb-3">
                                                            <label class="form-label">{{ __('Reason') }} <span class="text-danger">*</span></label>
                                                            <input type="text" name="reason" class="form-control" value="{{ $reason->reason }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">{{ __('Status') }}</label>
                                                            <select name="status" class="form-select">
                                                                <option value="1" {{ $reason->status ? 'selected' : '' }}>{{ __('Active') }}</option>
                                                                <option value="0" {{ !$reason->status ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer border-top-0">
                                                        <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                                                        <button type="submit" class="btn btn-primary rounded-pill px-4">{{ __('Update') }}</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <div class="empty-state">
                                        <div class="mb-3 text-muted opacity-50">
                                            <i class="bi bi-list-check display-1"></i>
                                        </div>
                                        <h5 class="text-muted">{{ __('No refund reasons found') }}</h5>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
             @if($reasons->hasPages())
                <div class="card-footer bg-white border-top-0 py-3">
                    {{ $reasons->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createReasonModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title fw-bold">{{ __('Add New Reason') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.refund-reasons.store') }}" method="POST">
                @csrf
                <div class="modal-body text-start">
                    <div class="mb-3">
                        <label class="form-label">{{ __('Reason') }} <span class="text-danger">*</span></label>
                        <input type="text" name="reason" class="form-control" placeholder="{{ __('Enter refund reason') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Status') }}</label>
                        <select name="status" class="form-select">
                            <option value="1">{{ __('Active') }}</option>
                            <option value="0">{{ __('Inactive') }}</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">{{ __('Save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .btn-soft-primary {
        color: #0d6efd;
        background-color: rgba(13, 110, 253, 0.1);
        border: none;
    }
    .btn-soft-primary:hover {
        color: #fff;
        background-color: #0d6efd;
    }

    .btn-soft-danger {
        color: #dc3545;
        background-color: rgba(220, 53, 69, 0.1);
        border: none;
    }
    .btn-soft-danger:hover {
        color: #fff;
        background-color: #dc3545;
    }
</style>
@endpush
