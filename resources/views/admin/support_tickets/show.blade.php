@extends('layouts.admin')

@section('page_title', __('Ticket') . ' ' . $supportTicket->ticket_number)

@section('content')
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-1">{{ $supportTicket->subject }}</h4>
                    <div class="d-flex align-items-center gap-3 text-muted small">
                        <span><i class="fas fa-hashtag me-1"></i>{{ $supportTicket->ticket_number }}</span>
                        <span><i class="fas fa-calendar me-1"></i>{{ $supportTicket->created_at->format('M d, Y H:i') }}</span>
                    </div>
                </div>
                <div>
                    <a href="{{ route('admin.support-tickets.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>{{ __('Back to List') }}
                    </a>
                </div>
            </div>

            <!-- Messages -->
            <div class="d-flex flex-column gap-3 mb-4">
                @foreach($supportTicket->messages as $message)
                    <div class="card border-0 shadow-sm {{ $message->user_id === auth()->id() ? 'bg-primary-subtle ms-5' : 'bg-white me-5' }}">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="fw-bold {{ $message->user_id === auth()->id() ? 'text-primary' : 'text-dark' }}">
                                        {{ $message->user->name }}
                                    </div>
                                    <span class="text-muted small">{{ $message->created_at->format('M d, Y H:i A') }}</span>
                                </div>
                                <span class="badge bg-light text-dark border">{{ ucfirst($message->user->role) }}</span>
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
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0">{{ __('Reply to Ticket') }}</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.support-tickets.reply', $supportTicket->id) }}" method="POST" enctype="multipart/form-data">
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
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">{{ __('Customer Info') }}</h6>
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="avatar-circle bg-primary text-white d-flex align-items-center justify-content-center rounded-circle" style="width: 48px; height: 48px; font-size: 20px;">
                            {{ substr($supportTicket->user->name, 0, 1) }}
                        </div>
                        <div>
                            <div class="fw-bold">{{ $supportTicket->user->name }}</div>
                            <div class="text-muted small">{{ $supportTicket->user->email }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">{{ __('Manage Ticket') }}</h6>
                    <form action="{{ route('admin.support-tickets.update', $supportTicket->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label small text-muted">{{ __('Status') }}</label>
                            <select name="status" class="form-select">
                                <option value="open" {{ $supportTicket->status == 'open' ? 'selected' : '' }}>{{ __('Open') }}</option>
                                <option value="pending" {{ $supportTicket->status == 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                                <option value="replied" {{ $supportTicket->status == 'replied' ? 'selected' : '' }}>{{ __('Replied') }}</option>
                                <option value="closed" {{ $supportTicket->status == 'closed' ? 'selected' : '' }}>{{ __('Closed') }}</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small text-muted">{{ __('Priority') }}</label>
                            <select name="priority" class="form-select">
                                <option value="low" {{ $supportTicket->priority == 'low' ? 'selected' : '' }}>{{ __('Low') }}</option>
                                <option value="medium" {{ $supportTicket->priority == 'medium' ? 'selected' : '' }}>{{ __('Medium') }}</option>
                                <option value="high" {{ $supportTicket->priority == 'high' ? 'selected' : '' }}>{{ __('High') }}</option>
                                <option value="urgent" {{ $supportTicket->priority == 'urgent' ? 'selected' : '' }}>{{ __('Urgent') }}</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small text-muted">{{ __('Department') }}</label>
                            <input type="text" class="form-control" value="{{ $supportTicket->department->name ?? '-' }}" disabled>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">{{ __('Update Ticket') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
