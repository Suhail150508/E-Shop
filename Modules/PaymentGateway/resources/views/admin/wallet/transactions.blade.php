@extends('layouts.admin')

@section('page_title', 'Transaction History')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <div class="row g-3 align-items-center">
            <div class="col-md-6">
                <h5 class="mb-0"><i class="fas fa-history me-2"></i>{{ __('Transaction History') }}</h5>
            </div>
            <div class="col-md-6">
                <form action="{{ route('admin.wallet.transactions') }}" method="GET" class="d-flex justify-content-end gap-2">
                    <div class="input-group input-group-sm w-auto">
                        <input type="text" name="date_range" class="form-control" placeholder="{{ __('YYYY-MM-DD to YYYY-MM-DD') }}" value="{{ request('date_range') }}">
                        <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-filter"></i></button>
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
                        <th class="py-3">{{ __('Owner Name') }}</th>
                        <th class="py-3">{{ __('Reference') }}</th>
                        <th class="py-3">{{ __('Created At') }}</th>
                        <th class="py-3">{{ __('Amount') }}</th>
                        <th class="py-3">{{ __('Type') }}</th>
                        <th class="py-3">{{ __('Purpose') }}</th>
                        <th class="py-3 text-center">{{ __('Payment Status') }}</th>
                        <th class="py-3 text-end">{{ __('Action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $index => $transaction)
                        <tr>
                            <td class="px-4">{{ $transactions->firstItem() + $index }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-circle bg-primary text-white d-flex align-items-center justify-content-center rounded-circle" style="width: 32px; height: 32px; font-size: 14px;">
                                        {{ substr($transaction->user->name ?? 'Unknown', 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="fw-medium">{{ $transaction->user->name ?? 'Unknown' }}</div>
                                        <div class="small text-muted">{{ $transaction->user->email ?? '' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $transaction->payment_transaction_id ?? '-' }}</td>
                            <td>{{ $transaction->created_at->format('d M Y h:i A') }}</td>
                            <td class="fw-bold {{ $transaction->type === 'credit' ? 'text-success' : 'text-danger' }}">
                                {{ $transaction->type === 'credit' ? '+' : '-' }}{{ number_format($transaction->amount, 2) }}
                            </td>
                            <td>
                                <span class="badge {{ $transaction->type === 'credit' ? 'bg-success' : 'bg-danger' }} bg-opacity-10 {{ $transaction->type === 'credit' ? 'text-success' : 'text-danger' }}">
                                    {{ ucfirst($transaction->type) }}
                                </span>
                            </td>
                            <td>{{ ucfirst($transaction->description) }}</td>
                            <td class="text-center">
                                @php
                                    $statusClass = match($transaction->status) {
                                        'approved', 'completed' => 'success',
                                        'pending' => 'warning',
                                        'rejected', 'failed' => 'danger',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge bg-{{ $statusClass }} bg-opacity-10 text-{{ $statusClass }}">
                                    {{ ucfirst($transaction->status) }}
                                </span>
                            </td>
                            <td class="text-end px-4">
                                @if($transaction->status === 'pending')
                                    <form action="{{ route('admin.wallet.transactions.approve', $transaction->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('{{ __('Are you sure you want to approve this transaction?') }}')">
                                            <i class="fas fa-check"></i> {{ __('Approve') }}
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-history fa-3x mb-3 opacity-50"></i>
                                    <p class="mb-0">{{ __('No transactions found.') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($transactions->hasPages())
        <div class="card-footer bg-white py-3">
            {{ $transactions->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
