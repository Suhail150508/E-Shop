@extends('layouts.admin')

@section('page_title', __('Subscriber List'))

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <div class="row g-3 align-items-center">
            <div class="col-md-6">
                <h5 class="mb-0"><i class="fas fa-envelope-open-text me-2"></i>{{ __('Subscriber List') }}</h5>
            </div>
            <div class="col-md-6 text-end">
                <form action="{{ route('admin.newsletter.index') }}" method="GET" class="d-inline-block">
                    <div class="input-group input-group-sm">
                        <input type="text" name="search" class="form-control" placeholder="{{ __('Search email...') }}" value="{{ request('search') }}">
                        <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-search"></i></button>
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
                        <th class="ps-4">{{ __('Email') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Subscribed Date') }}</th>
                        <th class="text-end pe-4">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subscribers as $subscriber)
                        <tr>
                            <td class="ps-4">{{ $subscriber->email }}</td>
                            <td>
                                @if($subscriber->is_active)
                                    <span class="badge bg-success">{{ __('Active') }}</span>
                                @else
                                    <span class="badge bg-danger">{{ __('Inactive') }}</span>
                                @endif
                            </td>
                            <td>{{ $subscriber->created_at->format('M d, Y H:i') }}</td>
                            <td class="text-end pe-4">
                                <form action="{{ route('admin.newsletter.destroy', $subscriber->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this subscriber?') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ __('Delete') }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="fas fa-users-slash fa-2x mb-3 opacity-50"></i>
                                <p class="mb-0">{{ __('No subscribers found.') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($subscribers->hasPages())
            <div class="card-footer bg-white py-3">
                {{ $subscribers->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
