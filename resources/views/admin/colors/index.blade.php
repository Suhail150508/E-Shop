@extends('layouts.admin')

@section('page_title', __('Colors'))

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h2 class="h4 fw-bold mb-1">{{ __('Colors') }}</h2>
        <p class="text-muted mb-0">{{ __('Manage your product colors.') }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.colors.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> {{ __('Add Color') }}
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form action="{{ route('admin.colors.index') }}" method="GET">
            <div class="row g-2">
                <div class="col-12 col-md-3">
                    <select name="status" class="form-select border-0 bg-light rounded-3 py-2 fs-14">
                        <option value="">{{ __('Status') }}</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>{{ __('Active') }}</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                    </select>
                </div>
                <div class="col-12 col-md-9">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control border-0 bg-light rounded-start-3 py-2 fs-14" placeholder="{{ __('Search colors...') }}" value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary rounded-end-3 px-3">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0 table-hover">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4" style="width: 60px;">{{ __('ID') }}</th>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Code') }}</th>
                        <th class="text-center">{{ __('Status') }}</th>
                        <th class="text-end pe-4">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($colors as $color)
                        <tr>
                            <td class="ps-4 text-muted">#{{ $color->id }}</td>
                            <td>
                                <h6 class="mb-0 fw-bold text-dark">{{ $color->name }}</h6>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle border" style="width: 24px; height: 24px; background-color: {{ $color->code }};"></div>
                                    <span class="text-muted">{{ $color->code }}</span>
                                </div>
                            </td>
                            <td class="text-center">
                                @if($color->is_active)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3">
                                        {{ __('Active') }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-3">
                                        {{ __('Inactive') }}
                                    </span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.colors.edit', $color->id) }}" class="btn btn-sm btn-light border" title="{{ __('Edit') }}">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.colors.destroy', $color->id) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light border text-danger" title="{{ __('Delete') }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox display-4 d-block mb-3"></i>
                                {{ __('No colors found') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($colors->hasPages())
            <div class="card-footer bg-white border-top-0 py-3">
                {{ $colors->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
