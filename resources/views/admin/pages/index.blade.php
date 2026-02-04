@extends('layouts.admin')

@section('page_title', __('Pages'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h4 fw-bold mb-1">{{ __('Pages') }}</h2>
        <p class="text-muted mb-0">{{ __('Manage static pages content.') }}</p>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">{{ __('Title') }}</th>
                        <th>{{ __('Slug') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th class="text-end pe-4">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pages as $page)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-semibold text-dark">{{ $page->title }}</div>
                            </td>
                            <td>
                                <code class="text-primary">{{ $page->slug }}</code>
                            </td>
                            <td>
                                @if($page->is_active)
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
                                <a href="{{ route('admin.pages.edit', $page) }}" class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" title="{{ __('Edit') }}">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <h5 class="h6 text-muted">{{ __('No pages found') }}</h5>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
