@extends('layouts.admin')

@section('page_title', __('Currency List'))

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-secondary">{{ __('Currency List') }}</h5>
            <a href="{{ route('admin.currency.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> {{ __('Create New') }}
            </a>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">{{ __('Name') }}</th>
                        <th>{{ __('Code') }}</th>
                        <th>{{ __('Symbol') }}</th>
                        <th>{{ __('Rate') }}</th>
                        <th>{{ __('Default') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th class="text-end pe-4">{{ __('Action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($currencies as $currency)
                        <tr>
                            <td class="ps-4 fw-semibold">{{ $currency->name }}</td>
                            <td>{{ $currency->code }}</td>
                            <td>{{ $currency->symbol }}</td>
                            <td>{{ $currency->rate }}</td>
                            <td>
                                @if($currency->is_default)
                                    <span class="badge bg-success-subtle border border-success-subtle text-success rounded-pill">{{ __('Yes') }}</span>
                                @else
                                    <span class="badge bg-secondary-subtle border border-secondary-subtle text-secondary rounded-pill">{{ __('No') }}</span>
                                @endif
                            </td>
                            <td>
                                @if($currency->status)
                                    <span class="badge bg-success-subtle border border-success-subtle text-success rounded-pill">{{ __('Active') }}</span>
                                @else
                                    <span class="badge bg-danger-subtle border border-danger-subtle text-danger rounded-pill">{{ __('Inactive') }}</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.currency.edit', $currency) }}" class="btn btn-sm btn-secondary-soft">
                                        <i class="fas fa-edit"></i> {{ __('Edit') }}
                                    </a>
                                    <form action="{{ route('admin.currency.destroy', $currency) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger-soft" {{ $currency->is_default ? 'disabled' : '' }}>
                                            <i class="fas fa-trash"></i> {{ __('Delete') }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="fas fa-coins fa-3x mb-3 opacity-25"></i>
                                    <span>{{ __('No currencies found.') }}</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($currencies->hasPages())
        <div class="card-footer bg-white border-top-0 py-3">
            {{ $currencies->links() }}
        </div>
    @endif
</div>
@endsection
