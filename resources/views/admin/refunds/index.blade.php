@extends('layouts.admin')

@section('page_title', __('Refund Requests'))

@section('content')
<div class="d-flex flex-column gap-4">
    <!-- Header & Search -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <h2 class="h4 fw-bold mb-0">
            <i class="bi bi-arrow-return-left me-2 text-primary"></i>{{ __('Refund Requests') }}
        </h2>
        <div class="flex-grow-1 mx-md-4" style="max-width: 500px;">
            <form action="{{ route('admin.refund-requests.index') }}" method="GET" class="position-relative">
                <input type="text" name="search" value="{{ request('search') }}" 
                    class="form-control border-0 bg-white shadow-sm rounded-pill py-2 ps-4 pe-5" 
                    placeholder="{{ __('Search by invoice no...') }}">
                <button type="submit" class="btn position-absolute top-50 end-0 translate-middle-y me-2 text-primary">
                    <i class="bi bi-search"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <!-- Filter Row -->
            <div class="p-3 border-bottom">
                <form action="{{ route('admin.refund-requests.index') }}" method="GET" id="filter-form">
                    <div class="row g-3">
                        @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif

                        <div class="col-12 col-md-3">
                             <select name="status" class="form-select border-0 bg-light rounded-3 py-2 fs-14" onchange="this.form.submit()">
                                <option value="">{{ __('Select Status') }}</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>{{ __('Approved') }}</option>
                                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>{{ __('Rejected') }}</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-3 ms-auto text-md-end">
                            <a href="{{ route('admin.refund-requests.index') }}" class="btn btn-soft-secondary rounded-3">
                                <i class="bi bi-arrow-counterclockwise me-1"></i> {{ __('Reset') }}
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Refunds Table -->
            <div class="table-responsive">
                <table class="table align-middle mb-0 table-hover">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4" style="width: 60px;">{{ __('SL') }}</th>
                            <th>{{ __('Invoice No') }}</th>
                            <th>{{ __('Customer') }}</th>
                            <th>{{ __('Amount') }}</th>
                            <th>{{ __('Refund Reason') }}</th>
                            <th class="text-center">{{ __('Status') }}</th>
                            <th class="text-end pe-4">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($refunds as $index => $refund)
                            <tr>
                                <td class="ps-4 text-muted fw-medium" data-label="{{ __('SL') }}">
                                    {{ $refunds->firstItem() + $index }}
                                </td>
                                <td data-label="{{ __('Invoice No') }}">
                                    <a href="{{ route('admin.orders.show', $refund->order_id) }}" class="font-monospace text-dark text-decoration-none fw-bold">
                                        {{ $refund->order?->order_number ?? __('common.na') }}
                                    </a>
                                </td>
                                <td data-label="{{ __('Customer') }}">
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold text-dark">{{ $refund->user?->name ?? __('common.guest') }}</span>
                                        <small class="text-muted">{{ $refund->user?->email ?? '' }}</small>
                                    </div>
                                </td>
                                <td data-label="{{ __('Amount') }}">
                                    <span class="fw-bold text-dark">
                                        {{ $refund->order?->currency ?? \App\Models\Currency::getDefaultSymbol() }}{{ number_format($refund->amount ?? 0, 2) }}
                                    </span>
                                </td>
                                <td data-label="{{ __('Refund Reason') }}">
                                    <span class="text-dark">{{ Str::limit($refund->reason, 30) }}</span>
                                </td>
                                <td class="text-center" data-label="{{ __('Status') }}">
                                    @php
                                        $statusColor = match($refund->status) {
                                            'pending' => 'warning',
                                            'approved' => 'success',
                                            'rejected' => 'danger',
                                            default => 'secondary'
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $statusColor }}-subtle text-{{ $statusColor }} border border-{{ $statusColor }}-subtle rounded-pill px-3 py-1 text-capitalize">
                                        {{ __(ucfirst($refund->status)) }}
                                    </span>
                                </td>
                                <td class="text-end pe-4" data-label="{{ __('Actions') }}">
                                    <div class="d-flex justify-content-end gap-2">
                                        <button type="button" class="btn btn-sm btn-soft-primary rounded-2" data-bs-toggle="modal" data-bs-target="#refundModal{{ $refund->id }}" title="{{ __('View Details') }}">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                    
                                    <!-- Refund Modal -->
                                    <div class="modal fade" id="refundModal{{ $refund->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow">
                                                <div class="modal-header border-bottom-0">
                                                    <h5 class="modal-title fw-bold">{{ __('Refund Request Details') }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body text-start">
                                                    <div class="mb-3">
                                                        <label class="form-label text-muted small">{{ __('Reason') }}</label>
                                                        <p class="fw-medium mb-0">{{ $refund->reason }}</p>
                                                    </div>
                                                    @if($refund->details)
                                                    <div class="mb-3">
                                                        <label class="form-label text-muted small">{{ __('Details') }}</label>
                                                        <p class="mb-0 bg-light p-3 rounded-3">{{ $refund->details }}</p>
                                                    </div>
                                                    @endif
                                                    
                                                    @if($refund->images)
                                                    <div class="mb-3">
                                                        <label class="form-label text-muted small">{{ __('Attachments') }}</label>
                                                        <div class="d-flex gap-2 flex-wrap">
                                                            @foreach($refund->images as $image)
                                                                <a href="{{ asset('storage/' . $image) }}" target="_blank">
                                                                    <img src="{{ asset('storage/' . $image) }}" class="rounded border" style="width: 60px; height: 60px; object-fit: cover;">
                                                                </a>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                    @endif

                                                    <form action="{{ route('admin.refund-requests.update', $refund->id) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="mb-3">
                                                            <label class="form-label text-muted small">{{ __('Admin Note') }}</label>
                                                            <textarea name="admin_note" class="form-control" rows="2" placeholder="{{ __('Optional note...') }}">{{ $refund->admin_note }}</textarea>
                                                        </div>
                                                        @if($refund->status === 'pending')
                                                            <div class="d-flex gap-2 pt-2">
                                                                <button type="submit" name="status" value="approved" class="btn btn-success flex-grow-1 text-white">
                                                                    <i class="bi bi-check-lg me-1"></i> {{ __('Approve') }}
                                                                </button>
                                                                <button type="submit" name="status" value="rejected" class="btn btn-danger flex-grow-1 text-white">
                                                                    <i class="bi bi-x-lg me-1"></i> {{ __('Reject') }}
                                                                </button>
                                                            </div>
                                                        @endif
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="empty-state">
                                        <div class="mb-3 text-muted opacity-50">
                                            <i class="bi bi-arrow-return-left display-1"></i>
                                        </div>
                                        <h5 class="text-muted">{{ __('No refund requests found') }}</h5>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($refunds->hasPages())
                <div class="card-footer bg-white border-top-0 py-3">
                    {{ $refunds->links() }}
                </div>
            @endif
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

    .btn-soft-secondary {
        color: #6c757d;
        background-color: rgba(108, 117, 125, 0.1);
        border: none;
    }
    .btn-soft-secondary:hover {
        color: #fff;
        background-color: #6c757d;
    }
</style>
@endpush
