@extends('layouts.admin')

@section('page_title', __('Wallet Lists'))

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <div class="row g-3 align-items-center">
            <div class="col-md-6">
                <h5 class="mb-0"><i class="fas fa-wallet me-2"></i>{{ __('Wallet') }}</h5>
            </div>
            <div class="col-md-6">
                <form action="{{ route('admin.wallet.index') }}" method="GET" class="d-flex justify-content-end gap-2">
                    <select name="status" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                        <option value="">{{ __('All Status') }}</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                    </select>
                    <div class="input-group input-group-sm w-auto">
                        <input type="text" name="search" class="form-control" placeholder="{{ __('Search...') }}" value="{{ request('search') }}">
                        <button class="btn btn-secondary-soft" type="submit"><i class="fas fa-search"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="px-4 py-3">{{ __('SL') }}</th>
                        <th class="py-3">{{ __('Owner ID') }}</th>
                        <th class="py-3">{{ __('Owner Name') }}</th>
                        <th class="py-3">{{ __('Owner Type') }}</th>
                        <th class="py-3">{{ __('Balance') }}</th>
                        <th class="py-3 text-center">{{ __('Status') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $index => $user)
                        <tr>
                            <td class="px-4">{{ $users->firstItem() + $index }}</td>
                            <td>{{ $user->id }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-circle bg-primary text-white d-flex align-items-center justify-content-center rounded-circle" style="width: 32px; height: 32px; font-size: 14px;">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="fw-medium">{{ $user->name }}</div>
                                        <div class="small text-muted">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge bg-info bg-opacity-10 text-info">{{ ucfirst($user->role) }}</span></td>
                            <td class="fw-bold">{{ number_format($user->wallet_balance, 2) }}</td>
                            <td class="text-center">
                                <div class="form-check form-switch d-flex justify-content-center">
                                    <input class="form-check-input wallet-status-toggle" type="checkbox" 
                                           data-id="{{ $user->id }}" 
                                           {{ $user->wallet_status ? 'checked' : '' }}>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-wallet fa-3x mb-3 opacity-50"></i>
                                    <p class="mb-0">{{ __('No wallet users found.') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($users->hasPages())
        <div class="card-footer bg-white py-3">
            {{ $users->withQueryString()->links() }}
        </div>
    @endif
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggles = document.querySelectorAll('.wallet-status-toggle');
        
        toggles.forEach(toggle => {
            toggle.addEventListener('change', function() {
                const userId = this.dataset.id;
                const status = this.checked ? 1 : 0;
                
                fetch(`{{ url('admin/wallet') }}/${userId}/status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ status: status })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Optional: Show toast notification
                        // Success logic
                    } else {
                        // Revert toggle if failed
                        this.checked = !status;
                        alert('Failed to update status');
                    }
                })
                .catch(error => {
                    this.checked = !status;
                });
            });
        });
    });
</script>
@endpush
@endsection
