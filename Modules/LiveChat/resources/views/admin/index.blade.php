@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4" style="height: calc(100vh - 80px);">
    <div class="row h-100">
        <!-- Sidebar -->
        <div class="col-md-4 col-lg-3 h-100 border-end bg-white p-0 d-flex flex-column">
            <div class="p-3 border-bottom bg-light">
                <h5 class="mb-0">{{ __('livechat::livechat.conversations') }}</h5>
            </div>
            <div class="flex-grow-1 overflow-auto" id="conversation-list">
                @foreach($conversations as $conversation)
                @php
                    $lastMsg = $conversation->messages->last();
                    $preview = $lastMsg ? ($lastMsg->message ? \Illuminate\Support\Str::limit($lastMsg->message, 30) : __('livechat::livechat.attachment')) : __('livechat::livechat.no_messages_yet');
                @endphp
                <div class="conversation-item p-3 border-bottom cursor-pointer {{ $loop->first ? 'active' : '' }}"
                     onclick="loadConversation({{ $conversation->id }}, this)"
                     data-id="{{ $conversation->id }}">
                    <div class="d-flex justify-content-between align-items-start mb-1">
                        <strong class="text-truncate">{{ $conversation->customer_name ?? __('livechat::livechat.guest') }}</strong>
                        @if(!$conversation->is_read_by_admin)
                            <span class="badge bg-danger rounded-pill unread-badge">{{ __('livechat::livechat.new') }}</span>
                        @endif
                    </div>
                    <small class="text-muted d-block text-truncate">{{ $preview }}</small>
                    <small class="text-muted" style="font-size: 0.75rem;">{{ $conversation->updated_at?->diffForHumans() }}</small>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Chat Area -->
        <div class="col-md-8 col-lg-9 h-100 d-flex flex-column p-0 bg-white">
            @if($conversations->count() > 0)
                <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-white shadow-sm" style="z-index: 10;">
                    <h6 class="mb-0" id="chat-user-name">{{ $conversations->first()->customer_name ?? __('livechat::livechat.guest') }}</h6>
                    <small class="text-muted" id="chat-user-email">{{ $conversations->first()->customer_email ?? '' }}</small>
                </div>

                <div class="flex-grow-1 p-4 overflow-auto bg-light" id="admin-chat-messages">
                    <!-- Messages loaded via JS -->
                </div>

                <div class="p-3 border-top bg-white">
                    <form id="admin-reply-form" class="d-flex gap-2">
                        <input type="hidden" name="conversation_id" id="active-conversation-id" value="{{ $conversations->first()->id ?? '' }}">
                        <label class="btn btn-light border position-relative" id="attachment-label">
                            <i class="bi bi-paperclip" id="attachment-icon"></i>
                            <input type="file" name="attachment" class="d-none">
                            <span id="attachment-indicator" class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle d-none">
                                <span class="visually-hidden">{{ __('livechat::livechat.new') }}</span>
                            </span>
                        </label>
                        <input type="text" class="form-control" name="message" placeholder="{{ __('livechat::livechat.type_a_reply') }}" autocomplete="off">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send"></i>
                        </button>
                    </form>
                </div>
            @else
                <div class="h-100 d-flex align-items-center justify-content-center text-muted">
                    <div class="text-center">
                        <i class="bi bi-chat-dots mb-3" style="font-size: 3rem;"></i>
                        <p>{{ __('livechat::livechat.no_active_conversations') }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<link rel="stylesheet" href="{{ asset('modules/livechat/css/admin-chat.css') }}">

<script>
    window.LiveChatConfig = {
        activeConversationId: {{ $conversations->first()->id ?? 'null' }},
        csrfToken: '{{ csrf_token() }}',
        routes: {
            reply: '{{ route("admin.livechat.reply", ":id") }}'
        },
        translations: {
            guest: '{{ __("livechat::livechat.guest") }}',
            attachment: '{{ __("livechat::livechat.attachment") }}',
            serverError: '{{ __("livechat::livechat.server_error") }}',
            sendError: '{{ __("livechat::livechat.error_sending_message") }}',
            loadError: '{{ __("livechat::livechat.failed_to_load_conversation") }}'
        }
    };
</script>
<script src="{{ asset('modules/livechat/js/admin-chat.js') }}"></script>
@endsection
