@extends('layouts.customer')

@section('title', __('Ticket') . ' ' . $ticket->ticket_number)

@section('account_content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">{{ $ticket->subject }}</h4>
            <div class="d-flex align-items-center gap-3 text-muted small">
                <span><i class="fas fa-hashtag me-1"></i>{{ $ticket->ticket_number }}</span>
                <span><i class="fas fa-layer-group me-1"></i>{{ $ticket->department->name ?? __('Unknown') }}</span>
                <span><i class="fas fa-calendar me-1"></i>{{ $ticket->created_at->format('M d, Y H:i') }}</span>
            </div>
        </div>
        <div class="text-end">
            <span class="badge bg-{{ $ticket->status_color }} fs-6 mb-2">{{ ucfirst($ticket->status) }}</span>
            <div>
                <a href="{{ route('customer.support-tickets.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>{{ __('Back to List') }}
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <!-- Messages -->
            <div class="d-flex flex-column gap-3 mb-4">
                @foreach($ticket->messages as $message)
                    <div class="card border-0 shadow-sm {{ $message->user_id === auth()->id() ? 'bg-primary-subtle ms-5' : 'bg-white me-5' }}">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="fw-bold {{ $message->user_id === auth()->id() ? 'text-primary' : 'text-dark' }}">
                                        {{ $message->user->name ?? __('Unknown User') }}
                                    </div>
                                    <span class="text-muted small">{{ $message->created_at->format('M d, Y H:i A') }}</span>
                                </div>
                                @if($message->user_id !== auth()->id())
                                    <span class="badge bg-light text-dark border">{{ __('Support') }}</span>
                                @endif
                            </div>
                            <div class="mb-0 text-break">
                                {!! nl2br(e($message->message)) !!}
                            </div>
                            @if($message->attachment)
                                <div class="mt-3 pt-3 border-top border-secondary-subtle">
                                    <a href="{{ asset('storage/' . $message->attachment) }}" target="_blank" class="text-decoration-none small">
                                        <i class="fas fa-paperclip me-1"></i>{{ __('View Attachment') }}
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Reply Form -->
            @if($ticket->status !== 'closed')
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0">{{ __('Reply to Ticket') }}</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('customer.support-tickets.reply', $ticket->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <textarea name="message" class="form-control" rows="4" required placeholder="{{ __('Write your reply here...') }}"></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small text-muted">{{ __('Attachment (Optional)') }}</label>
                                <input type="file" name="attachment" class="form-control form-control-sm">
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-1"></i>{{ __('Send Reply') }}
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <div class="alert alert-secondary text-center">
                    <i class="fas fa-lock me-2"></i>{{ __('This ticket has been closed. You cannot reply to it.') }}
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">{{ __('Ticket Information') }}</h6>
                    <div class="mb-3">
                        <div class="text-muted small">{{ __('Department') }}</div>
                        <div class="fw-medium">{{ $ticket->department->name ?? '-' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">{{ __('Priority') }}</div>
                        <div class="fw-medium text-capitalize">
                            <span class="badge border border-{{ $ticket->priority_color }} text-{{ $ticket->priority_color }}">{{ $ticket->priority }}</span>
                        </div>
                    </div>
                    <div class="mb-0">
                        <div class="text-muted small">{{ __('Last Updated') }}</div>
                        <div class="fw-medium">{{ $ticket->updated_at->diffForHumans() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
