@extends('layouts.customer')

@section('title', __('common.my_wallet'))

@section('account_content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">{{ __('common.my_wallet') }}</h4>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addFundsModal">
            <i class="fas fa-plus-circle me-2"></i>{{ __('common.add_funds') }}
        </button>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-6 col-lg-4">
            <div class="card bg-primary text-white border-0 shadow-sm overflow-hidden h-100 position-relative">
                <div class="card-body position-relative z-1">
                    <h6 class="text-white-50 mb-2">{{ __('common.current_balance') }}</h6>
                    <h2 class="mb-0 fw-bold">{{ $settings->currency_symbol ?? '$' }}{{ number_format($user->wallet_balance, 2) }}</h2>
                    @if($pendingBalance > 0)
                        <small class="text-white-50 mt-2 d-block">
                            <i class="fas fa-clock me-1"></i>
                            {{ __('common.pending_balance') }}: {{ $settings->currency_symbol ?? '$' }}{{ number_format($pendingBalance, 2) }}
                        </small>
                    @endif
                </div>
                <div class="position-absolute top-0 end-0 opacity-25 p-3">
                    <i class="fas fa-wallet fa-5x"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted mb-1">{{ __('common.total_deposited') }}</h6>
                        <h4 class="mb-0 text-success">{{ $settings->currency_symbol ?? '$' }}{{ number_format($user->walletTransactions()->where('type', 'credit')->where('status', 'approved')->sum('amount'), 2) }}</h4>
                    </div>
                    <div class="vr mx-3"></div>
                    <div>
                        <h6 class="text-muted mb-1">{{ __('common.total_spent') }}</h6>
                        <h4 class="mb-0 text-danger">{{ $settings->currency_symbol ?? '$' }}{{ number_format($user->walletTransactions()->where('type', 'debit')->where('status', 'approved')->sum('amount'), 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 border-bottom-0">
            <h5 class="mb-0">{{ __('common.transaction_history') }}</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="border-top-0">{{ __('common.date') }}</th>
                        <th class="border-top-0">{{ __('common.description') }}</th>
                        <th class="border-top-0">{{ __('common.type') }}</th>
                        <th class="border-top-0">{{ __('common.amount') }}</th>
                        <th class="border-top-0">{{ __('common.status') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $transaction)
                        <tr>
                            <td class="text-nowrap">{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                            <td>
                                <span class="d-inline-block text-truncate" style="max-width: 200px;" title="{{ $transaction->description ?: $transaction->type }}">
                                    {{ $transaction->description ?: $transaction->type }}
                                </span>
                            </td>
                            <td>
                                @if($transaction->type === 'credit')
                                    <span class="badge bg-success-subtle text-success">{{ __('common.credit') }}</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger">{{ __('common.debit') }}</span>
                                @endif
                            </td>
                            <td class="fw-bold {{ $transaction->type === 'credit' ? 'text-success' : 'text-danger' }}">
                                {{ $transaction->type === 'credit' ? '+' : '-' }}{{ $settings->currency_symbol ?? '$' }}{{ number_format($transaction->amount, 2) }}
                            </td>
                            <td>
                                @if($transaction->status === 'approved')
                                    <span class="badge bg-success">{{ __('common.approved') }}</span>
                                @elseif($transaction->status === 'pending')
                                    <span class="badge bg-warning text-dark">{{ __('common.pending') }}</span>
                                @elseif($transaction->status === 'declined')
                                    <span class="badge bg-danger">{{ __('common.declined') }}</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($transaction->status) }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-receipt fa-3x mb-3 opacity-50"></i>
                                    <p class="mb-0">{{ __('common.no_transactions_found') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($transactions->hasPages())
            <div class="card-footer bg-white border-top-0">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>

    <!-- Add Funds Modal -->
    <div class="modal fade" id="addFundsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('customer.wallet.deposit') }}" method="POST">
                    @csrf
                    <div class="modal-header bg-light">
                        <h5 class="modal-title">{{ __('common.add_funds_to_wallet') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-4">
                            <label for="amount" class="form-label fw-bold">{{ __('common.amount') }}</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-white border-end-0">{{ $settings->currency_symbol ?? '$' }}</span>
                                <input type="number" class="form-control border-start-0 ps-0" id="amount" name="amount" min="1" max="{{ $depositLimit }}" step="0.01" required placeholder="0.00">
                            </div>
                            <div class="form-text">{{ __('common.maximum_deposit_limit', ['limit' => ($settings->currency_symbol ?? '$') . number_format($depositLimit, 2)]) }}</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('common.payment_method') }}</label>
                            @if(empty($gateways))
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i> {{ __('common.no_payment_methods_available') }}
                                </div>
                            @else
                                <div class="row g-2">
                                    @foreach($gateways as $key => $gateway)
                                        <div class="col-6">
                                            <input type="radio" class="btn-check" name="payment_method" id="method_{{ $key }}" value="{{ $key }}" {{ $loop->first ? 'checked' : '' }}>
                                            <label class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3" for="method_{{ $key }}">
                                                @if($key == 'stripe') <i class="fab fa-stripe-s fa-2x mb-2"></i>
                                                @elseif($key == 'paypal') <i class="fab fa-paypal fa-2x mb-2"></i>
                                                @elseif($key == 'razorpay') <i class="fas fa-money-bill-wave fa-2x mb-2"></i>
                                                @elseif($key == 'paystack') <i class="fas fa-layer-group fa-2x mb-2"></i>
                                                @elseif($key == 'bank') <i class="fas fa-university fa-2x mb-2"></i>
                                                @else <i class="fas fa-credit-card fa-2x mb-2"></i>
                                                @endif
                                                <span class="small fw-bold">{{ ucfirst($key) }}</span>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-link text-muted text-decoration-none" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                        <button type="submit" class="btn btn-primary px-4" {{ empty($gateways) ? 'disabled' : '' }}>{{ __('common.proceed_to_payment') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
