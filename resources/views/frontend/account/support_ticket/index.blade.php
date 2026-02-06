@extends('layouts.customer')

@section('title', __('Support Tickets'))

@section('account_content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">{{ __('Support Tickets') }}</h4>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTicketModal">
            <i class="fas fa-plus-circle me-2"></i>{{ __('Create a ticket') }}
        </button>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form action="{{ route('customer.support-tickets.index') }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-3">
                        <select name="department_id" class="form-select" onchange="this.form.submit()">
                            <option value="">{{ __('--Select Department--') }}</option>
                            @foreach($departments ?? [] as $dept)
                                <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select" onchange="this.form.submit()">
                            <option value="">{{ __('--Select Status--') }}</option>
                            <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>{{ __('Open') }}</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                            <option value="replied" {{ request('status') == 'replied' ? 'selected' : '' }}>{{ __('Replied') }}</option>
                            <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>{{ __('Closed') }}</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="priority" class="form-select" onchange="this.form.submit()">
                            <option value="">{{ __('--Select Priority--') }}</option>
                            <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>{{ __('Low') }}</option>
                            <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>{{ __('Medium') }}</option>
                            <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>{{ __('High') }}</option>
                            <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>{{ __('Urgent') }}</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="{{ __('Search...') }}" value="{{ is_string(request('search')) ? e(request('search')) : '' }}">
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
                        <th class="px-4 py-3">{{ __('SL') }}</th>
                        <th class="py-3">{{ __('Ticket ID') }}</th>
                        <th class="py-3">{{ __('Title') }}</th>
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
                                    {{ ucfirst($ticket->priority) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $ticket->status_color }}">{{ ucfirst($ticket->status) }}</span>
                            </td>
                            <td class="text-end px-4">
                                <a href="{{ route('customer.support-tickets.show', $ticket->id) }}" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="{{ __('View Ticket') }}">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
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
                    <h5 class="modal-title">{{ __('Create New Ticket') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('customer.support-tickets.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Subject') }} <span class="text-danger">*</span></label>
                                <input type="text" name="subject" class="form-control" required placeholder="{{ __('Enter ticket subject') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Department') }} <span class="text-danger">*</span></label>
                                <select name="department_id" class="form-select" required>
                                    <option value="">{{ __('Select Department') }}</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Priority') }} <span class="text-danger">*</span></label>
                                <select name="priority" class="form-select" required>
                                    <option value="low">{{ __('Low') }}</option>
                                    <option value="medium">{{ __('Medium') }}</option>
                                    <option value="high">{{ __('High') }}</option>
                                    <option value="urgent">{{ __('Urgent') }}</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Attachment') }}</label>
                                <input type="file" name="attachment" class="form-control">
                                <div class="form-text">{{ __('Supported files: jpg, jpeg, png, pdf, doc, docx') }}</div>
                            </div>
                            <div class="col-12">
                                <label class="form-label">{{ __('Message') }} <span class="text-danger">*</span></label>
                                <textarea name="message" class="form-control" rows="5" required placeholder="{{ __('Describe your issue here...') }}"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Submit Ticket') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
