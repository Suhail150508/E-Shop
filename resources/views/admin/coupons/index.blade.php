@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('Coupons') }}</h1>
        <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> {{ __('Create Coupon') }}
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>{{ __('Code') }}</th>
                            <th>{{ __('Type') }}</th>
                            <th>{{ __('Value') }}</th>
                            <th>{{ __('Usage / Limit') }}</th>
                            <th>{{ __('Expiry Date') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($coupons as $coupon)
                        <tr>
                            <td>
                                <span class="fw-bold">{{ $coupon->code }}</span>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ __(ucfirst($coupon->type)) }}</span>
                            </td>
                            <td>
                                @if($coupon->type == 'fixed')
                                    ${{ number_format($coupon->value, 2) }}
                                @else
                                    {{ $coupon->value }}%
                                @endif
                            </td>
                            <td>
                                {{ $coupon->used_count }} / {{ $coupon->usage_limit ?? '∞' }}
                            </td>
                            <td>
                                {{ $coupon->expiry_date ? $coupon->expiry_date->format('Y-m-d') : __('Never') }}
                            </td>
                            <td>
                                @if($coupon->is_active)
                                    <span class="badge bg-success">{{ __('Active') }}</span>
                                @else
                                    <span class="badge bg-danger">{{ __('Inactive') }}</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.coupons.edit', $coupon) }}" class="btn btn-sm btn-info text-white">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-danger" onclick="if(confirm('{{ __('Are you sure?') }}')) this.closest('form').submit();">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">{{ __('No coupons found.') }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-end">
                {{ $coupons->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
