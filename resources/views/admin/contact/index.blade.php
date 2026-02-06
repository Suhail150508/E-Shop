@extends('layouts.admin')

@section('page_title', __('Contact Messages'))

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <div class="row g-3 align-items-center">
            <div class="col-md-6">
                <h5 class="mb-0"><i class="fas fa-envelope me-2"></i>{{ __('Contact Messages') }}</h5>
            </div>
            <div class="col-md-6 text-end">
                <form action="{{ route('admin.contact.index') }}" method="GET" class="d-inline-block">
                    <div class="input-group input-group-sm">
                        <input type="text" name="search" class="form-control" placeholder="{{ __('Search messages...') }}" value="{{ request('search') }}">
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
                        <th class="ps-4">{{ __('Name') }}</th>
                        <th>{{ __('Subject') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Date') }}</th>
                        <th class="text-end pe-4">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($messages as $message)
                        <tr class="{{ $message->is_read ? '' : 'fw-bold bg-light-subtle' }}">
                            <td class="ps-4">
                                <div class="d-flex flex-column">
                                    <span class="text-dark">{{ $message->name }}</span>
                                    <small class="text-muted">{{ $message->email }}</small>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column" style="max-width: 300px;">
                                    <span class="text-truncate">{{ $message->subject }}</span>
                                    <small class="text-muted text-truncate">{{ Str::limit($message->message, 50) }}</small>
                                </div>
                            </td>
                            <td>
                                @if($message->status == 'new')
                                    <span class="badge bg-primary">{{ __('New') }}</span>
                                @elseif($message->status == 'read')
                                    <span class="badge bg-info">{{ __('Read') }}</span>
                                @elseif($message->status == 'replied')
                                    <span class="badge bg-success">{{ __('Replied') }}</span>
                                @endif
                            </td>
                            <td>{{ $message->created_at->format('M d, Y H:i') }}</td>
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#replyModal{{ $message->id }}" 
                                            title="{{ __('View & Reply') }}">
                                        <i class="fas fa-reply"></i>
                                    </button>
                                    <form action="{{ route('admin.contact.destroy', $message->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this message?') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ __('Delete') }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>

                                <!-- Reply Modal -->
                                <div class="modal fade text-start" id="replyModal{{ $message->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">{{ __('Message Details') }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-4">
                                                    <div class="row mb-2">
                                                        <div class="col-sm-3 fw-bold">{{ __('From:') }}</div>
                                                        <div class="col-sm-9">{{ $message->name }} &lt;{{ $message->email }}&gt;</div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <div class="col-sm-3 fw-bold">{{ __('Phone:') }}</div>
                                                        <div class="col-sm-9">{{ $message->phone ?? __('N/A') }}</div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <div class="col-sm-3 fw-bold">{{ __('Date:') }}</div>
                                                        <div class="col-sm-9">{{ $message->created_at->format('M d, Y H:i A') }}</div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <div class="col-sm-3 fw-bold">{{ __('Subject:') }}</div>
                                                        <div class="col-sm-9">{{ $message->subject }}</div>
                                                    </div>
                                                    <div class="row mt-3">
                                                        <div class="col-12">
                                                            <div class="p-3 bg-light rounded border">
                                                                {{ $message->message }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                @if($message->reply)
                                                    <div class="alert alert-success">
                                                        <strong>{{ __('Replied on:') }}</strong> {{ $message->updated_at->format('M d, Y H:i A') }}<br>
                                                        <strong>{{ __('Content:') }}</strong><br>
                                                        {{ $message->reply }}
                                                    </div>
                                                @else
                                                    <form action="{{ route('admin.contact.reply', $message->id) }}" method="POST">
                                                        @csrf
                                                        <div class="mb-3">
                                                            <label for="reply" class="form-label">{{ __('Reply Message') }}</label>
                                                            <textarea class="form-control" name="reply" rows="5" required></textarea>
                                                        </div>
                                                        <div class="text-end">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                                                            <button type="submit" class="btn btn-primary">
                                                                <i class="fas fa-paper-plane me-1"></i> {{ __('Send Reply') }}
                                                            </button>
                                                        </div>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fas fa-inbox fa-2x mb-3 opacity-50"></i>
                                <p class="mb-0">{{ __('No messages found.') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($messages->hasPages())
            <div class="card-footer bg-white py-3">
                {{ $messages->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
