@extends('layouts.admin')

@section('page_title', __('Menu Management'))

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom-0 py-3">
                <h5 class="card-title mb-0 fw-bold">{{ __('Create Menu') }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.menus.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">{{ __('Menu Name') }}</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="{{ __('e.g. Header Menu') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="position" class="form-label">{{ __('Position') }}</label>
                        <select class="form-select" id="position" name="position">
                            <option value="">{{ __('Select Position') }}</option>
                            <option value="header">{{ __('Header Main Menu') }}</option>
                            <option value="footer_1">{{ __('Footer Column 1') }}</option>
                            <option value="footer_2">{{ __('Footer Column 2') }}</option>
                            <option value="footer_3">{{ __('Footer Column 3') }}</option>
                            <option value="footer_4">{{ __('Footer Column 4') }}</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">{{ __('Create Menu') }}</button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom-0 py-3">
                <h5 class="card-title mb-0 fw-bold">{{ __('All Menus') }}</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">{{ __('Name') }}</th>
                            <th>{{ __('Position') }}</th>
                            <th>{{ __('Items') }}</th>
                            <th class="text-end pe-4">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($menus as $menu)
                        <tr>
                            <td class="ps-4 fw-medium">{{ $menu->name }}</td>
                            <td>
                                @if($menu->position)
                                    <span class="badge bg-info-subtle text-info border border-info-subtle">{{ ucfirst(str_replace('_', ' ', $menu->position)) }}</span>
                                @else
                                    <span class="text-muted small">{{ __('Not Assigned') }}</span>
                                @endif
                            </td>
                            <td>{{ $menu->items->count() }}</td>
                            <td class="text-end pe-4">
                                <a href="{{ route('admin.menus.builder', $menu->id) }}" class="btn btn-sm btn-secondary-soft">
                                    <i class="fas fa-layer-group me-1"></i> {{ __('Builder') }}
                                </a>
                                <form action="{{ route('admin.menus.destroy', $menu->id) }}" method="POST" class="d-inline-block ms-1" onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger-soft">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">
                                {{ __('No menus found. Create one to get started.') }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
