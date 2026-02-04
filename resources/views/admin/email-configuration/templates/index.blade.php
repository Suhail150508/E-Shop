@extends('layouts.admin')

@section('page_title', __('Email Templates'))

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1">{{ __('Email Templates') }}</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">{{ __('Dashboard') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.email-configuration.index') }}" class="text-decoration-none">{{ __('Email Configuration') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('Templates') }}</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.email-configuration.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> {{ __('Back to Configuration') }}
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">{{ __('All Email Templates') }}</h5>
        </div>
        <div class="card-body p-0">
            
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Subject') }}</th>
                            <th>{{ __('Last Updated') }}</th>
                            <th class="text-end">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($templates as $template)
                        <tr>
                            <td>
                                <div class="fw-medium">{{ $template->name }}</div>
                                <small class="text-muted">{{ $template->slug }}</small>
                            </td>
                            <td>{{ $template->subject }}</td>
                            <td>{{ $template->updated_at->format('d M, Y h:i A') }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.email-configuration.templates.edit', $template->id) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i> {{ __('Edit') }}
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4">{{ __('No templates found.') }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($templates->hasPages())
            <div class="card-footer">
                {{ $templates->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
