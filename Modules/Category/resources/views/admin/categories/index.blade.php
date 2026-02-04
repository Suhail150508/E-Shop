@extends('layouts.admin')

@section('page_title', __('Categories'))

@push('styles')
<style>
    .avatar-sm {
        width: 40px;
        height: 40px;
        object-fit: cover;
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
@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h2 class="h4 fw-bold mb-1">{{ __('Categories') }}</h2>
        <p class="text-muted mb-0">{{ __('Manage your product categories.') }}</p>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-danger d-none" id="bulk-delete-btn">
            <i class="bi bi-trash me-1"></i> {{ __('Delete Selected') }}
        </button>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> {{ __('Add Category') }}
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form action="{{ route('admin.categories.index') }}" method="GET">
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
                        <input type="text" name="search" class="form-control border-0 bg-light rounded-start-3 py-2 fs-14" placeholder="{{ __('Search categories...') }}" value="{{ request('search') }}">
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
                        <th class="ps-4" style="width: 50px;">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="select-all">
                            </div>
                        </th>
                        <th class="text-center" style="width: 60px;">{{ __('ID') }}</th>
                        <th>{{ __('Category Info') }}</th>
                        <th>{{ __('Parent') }}</th>
                        <th class="text-center">{{ __('Status') }}</th>
                        <th class="text-end pe-4">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                        <tr>
                            <td class="ps-4">
                                <div class="form-check">
                                    <input class="form-check-input item-checkbox" type="checkbox" value="{{ $category->id }}">
                                </div>
                            </td>
                            <td class="text-center text-muted">#{{ $category->id }}</td>
                            <td data-label="{{ __('Category Info') }}">
                                <div class="d-flex align-items-center">
                                    <div class="position-relative me-3">
                                        @if($category->image_url)
                                            <img src="{{ $category->image_url }}" alt="{{ $category->name }}" class="avatar-sm rounded-3 border">
                                        @else
                                            <div class="avatar-sm rounded-3 border bg-light d-flex align-items-center justify-content-center text-muted">
                                                <i class="bi bi-image fs-5"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold text-dark">{{ $category->name }}</h6>
                                        <small class="text-muted">{{ $category->products_count ?? 0 }} {{ __('products') }}</small>
                                    </div>
                                </div>
                            </td>
                            <td data-label="{{ __('Parent') }}">
                                @if($category->parent)
                                    <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill">
                                        {{ $category->parent->name }}
                                    </span>
                                @else
                                    <span class="text-muted small">--</span>
                                @endif
                            </td>
                            <td class="text-center" data-label="{{ __('Status') }}">
                                @if($category->is_active ?? true)
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
                                    <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-soft-secondary" data-bs-toggle="tooltip" title="{{ __('Edit') }}">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="d-inline-block" onsubmit="return confirm('{{ __('Are you sure you want to delete this category?') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-soft-danger" data-bs-toggle="tooltip" title="{{ __('Delete') }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted mb-2">
                                    <i class="bi bi-folder2-open display-4"></i>
                                </div>
                                <h5 class="h6 text-muted">{{ __('No categories found') }}</h5>
                                <a href="{{ route('admin.categories.create') }}" class="btn btn-sm btn-primary mt-2">
                                    {{ __('Create First Category') }}
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if(method_exists($categories, 'links') && $categories->hasPages())
        <div class="card-footer border-0 bg-white py-3">
            {{ $categories->links() }}
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('select-all');
        const checkboxes = document.querySelectorAll('.item-checkbox');
        const bulkDeleteBtn = document.getElementById('bulk-delete-btn');

        function updateBulkDeleteBtn() {
            const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
            if (anyChecked) {
                bulkDeleteBtn.classList.remove('d-none');
            } else {
                bulkDeleteBtn.classList.add('d-none');
            }
        }

        if(selectAll) {
            selectAll.addEventListener('change', function() {
                checkboxes.forEach(cb => cb.checked = this.checked);
                updateBulkDeleteBtn();
            });
        }

        checkboxes.forEach(cb => {
            cb.addEventListener('change', updateBulkDeleteBtn);
        });

        if(bulkDeleteBtn) {
            bulkDeleteBtn.addEventListener('click', function() {
                if (!confirm('{{ __("Are you sure you want to delete selected items?") }}')) return;

                const selectedIds = Array.from(checkboxes)
                    .filter(cb => cb.checked)
                    .map(cb => cb.value);

                fetch('{{ route("admin.categories.bulk-delete") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ ids: selectedIds })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        if (typeof toastr !== 'undefined') {
                            toastr.error(data.error || '{{ __('Something went wrong') }}');
                        } else {
                            alert(data.error || '{{ __('Something went wrong') }}');
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    if (typeof toastr !== 'undefined') {
                        toastr.error('{{ __('An error occurred') }}');
                    } else {
                        alert('{{ __('An error occurred') }}');
                    }
                });
            });
        }
    });
</script>
@endpush
