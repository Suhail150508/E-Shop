@extends('layouts.customer')

@section('title', __('common.support_tickets'))

@section('account_content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">{{ __('common.support_tickets') }}</h4>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTicketModal">
            <i class="fas fa-plus-circle me-2"></i>{{ __('common.create_ticket') }}
        </button>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form action="{{ route('customer.support-tickets.index') }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-3">
                        <select name="department_id" class="form-select" onchange="this.form.submit()">
                            <option value="">{{ __('common.select_department') }}</option>
                            @foreach($departments ?? [] as $dept)
                                <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select" onchange="this.form.submit()">
                            <option value="">{{ __('common.select_status') }}</option>
                            <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>{{ __('common.open') }}</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('common.pending') }}</option>
                            <option value="replied" {{ request('status') == 'replied' ? 'selected' : '' }}>{{ __('common.replied') }}</option>
                            <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>{{ __('common.closed') }}</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="priority" class="form-select" onchange="this.form.submit()">
                            <option value="">{{ __('common.select_priority') }}</option>
                            <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>{{ __('common.low') }}</option>
                            <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>{{ __('common.medium') }}</option>
                            <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>{{ __('common.high') }}</option>
                            <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>{{ __('common.urgent') }}</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="{{ __('common.search_placeholder') }}" value="{{ is_string(request('search')) ? e(request('search')) : '' }}">
                            <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Ticket List -->
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="px-4 py-3">{{ __('common.sl') }}</th>
                        <th class="py-3">{{ __('common.ticket_id') }}</th>
                        <th class="py-3">{{ __('common.title') }}</th>
                        <th class="py-3">{{ __('common.department') }}</th>
                        <th class="py-3">{{ __('common.priority') }}</th>
                        <th class="py-3">{{ __('common.status') }}</th>
                        <th class="py-3 text-end px-4">{{ __('common.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tickets as $index => $ticket)
                        <tr>
                            <td class="px-4">{{ $tickets->firstItem() + $index }}</td>
                            <td class="fw-bold text-primary">{{ $ticket->ticket_number }}</td>
                            <td>{{ Str::limit($ticket->subject, 30) }}</td>
                            <td>
                                @if($ticket->department)
                                    <span class="badge bg-light text-dark border">{{ $ticket->department->name }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge border border-{{ $ticket->priority_color }} text-{{ $ticket->priority_color }}">
                                    {{ __('common.' . $ticket->priority) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $ticket->status_color }}">{{ __('common.' . $ticket->status) }}</span>
                            </td>
                            <td class="text-end px-4">
                                <a href="{{ route('customer.support-tickets.show', $ticket->id) }}" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="{{ __('common.view_ticket') }}">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-ticket-alt fa-3x mb-3 opacity-50"></i>
                                    <p class="mb-0">{{ __('common.no_support_tickets_found') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(isset($tickets) && $tickets instanceof \Illuminate\Pagination\LengthAwarePaginator && $tickets->hasPages())
            <div class="card-footer bg-white py-3">
                {{ $tickets->withQueryString()->links() }}
            </div>
        @endif
    </div>

    <!-- Create Ticket Modal -->
    <div class="modal fade" id="createTicketModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('common.create_new_ticket') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('customer.support-tickets.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">{{ __('common.subject') }} <span class="text-danger">*</span></label>
                                <input type="text" name="subject" class="form-control" required placeholder="{{ __('common.enter_ticket_subject') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('common.department') }} <span class="text-danger">*</span></label>
                                <select name="department_id" class="form-select" required>
                                    <option value="">{{ __('common.select_department') }}</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('common.priority') }} <span class="text-danger">*</span></label>
                                <select name="priority" class="form-select" required>
                                    <option value="low">{{ __('common.low') }}</option>
                                    <option value="medium">{{ __('common.medium') }}</option>
                                    <option value="high">{{ __('common.high') }}</option>
                                    <option value="urgent">{{ __('common.urgent') }}</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('common.attachment') }}</label>
                                <input type="file" name="attachment" class="form-control">
                                <div class="form-text">{{ __('common.supported_files_hint') }}</div>
                            </div>
                            <div class="col-12">
                                <label class="form-label">{{ __('common.message') }} <span class="text-danger">*</span></label>
                                <textarea name="message" class="form-control" rows="5" required placeholder="{{ __('common.describe_issue_placeholder') }}"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('common.submit_ticket') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
