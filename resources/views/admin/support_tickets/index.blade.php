@extends('layouts.admin')

@section('page_title', __('Support Tickets'))

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <div class="row g-3 align-items-center">
            <div class="col-md-6">
                <h5 class="mb-0"><i class="fas fa-ticket-alt me-2"></i>{{ __('Support Tickets') }}</h5>
            </div>
            <div class="col-md-6">
                <form action="{{ route('admin.support-tickets.index') }}" method="GET" class="d-flex justify-content-end gap-2">
                    <select name="status" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                        <option value="">{{ __('All Status') }}</option>
                        <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>{{ __('Open') }}</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                        <option value="replied" {{ request('status') == 'replied' ? 'selected' : '' }}>{{ __('Replied') }}</option>
                        <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>{{ __('Closed') }}</option>
                    </select>
                    <select name="priority" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                        <option value="">{{ __('All Priority') }}</option>
                        <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>{{ __('Low') }}</option>
                        <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>{{ __('Medium') }}</option>
                        <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>{{ __('High') }}</option>
                        <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>{{ __('Urgent') }}</option>
                    </select>
                    <div class="input-group input-group-sm w-auto">
                        <input type="text" name="search" class="form-control" placeholder="{{ __('Search...') }}" value="{{ is_string(request('search')) ? request('search') : '' }}">
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
                        <th class="px-4 py-3">{{ __('SL') }}</th>
                        <th class="py-3">{{ __('Ticket ID') }}</th>
                        <th class="py-3">{{ __('Customer') }}</th>
                        <th class="py-3">{{ __('Subject') }}</th>
                        <th class="py-3">{{ __('Department') }}</th>
                        <th class="py-3">{{ __('Priority') }}</th>
                        <th class="py-3">{{ __('Status') }}</th>
                        <th class="py-3 text-end px-4">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tickets as $index => $ticket)
                        <tr>
                            <td class="px-4">{{ $tickets->firstItem() + $index }}</td>
                            <td class="fw-bold text-primary">{{ $ticket->ticket_number }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-circle bg-primary text-white d-flex align-items-center justify-content-center rounded-circle" style="width: 32px; height: 32px; font-size: 14px;">
                                        {{ substr($ticket->user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="fw-medium">{{ $ticket->user->name }}</div>
                                        <div class="small text-muted">{{ $ticket->user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ Str::limit($ticket->subject, 30) }}</td>
                            <td>{{ $ticket->department->name ?? '-' }}</td>
                            <td>
                                <span class="badge border border-{{ $ticket->priority_color }} text-{{ $ticket->priority_color }}">
                                    {{ ucfirst($ticket->priority) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $ticket->status_color }}">{{ ucfirst($ticket->status) }}</span>
                            </td>
                            <td class="text-end px-4">
                                <a href="{{ route('admin.support-tickets.show', $ticket->id) }}" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="{{ __('View Ticket') }}">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-ticket-alt fa-3x mb-3 opacity-50"></i>
                                    <p class="mb-0">{{ __('No support tickets found.') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($tickets->hasPages())
            <div class="card-footer bg-white py-3">
                {{ $tickets->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
