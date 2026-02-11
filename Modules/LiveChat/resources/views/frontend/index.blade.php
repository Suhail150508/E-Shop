@extends('layouts.customer')

@section('title', __('livechat::livechat.live_chat'))

@section('account_content')
<div class="card border-0 shadow-sm rounded-3 h-100">
    <div class="card-header bg-white border-bottom p-3 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0 text-dark">{{ __('livechat::livechat.live_chat_support') }}</h5>
        <span class="badge bg-success-subtle text-success">
            <i class="fas fa-circle me-1 small"></i> {{ __('livechat::livechat.online') }}
        </span>
    </div>

    <div class="card-body p-0 d-flex flex-column" style="height: 600px;">
        <!-- Messages Area -->
        <div id="chat-messages" class="flex-grow-1 p-4 overflow-auto bg-light">
            @forelse($messages as $message)
                <div class="message-bubble {{ $message->sender_type }}">
                    @if($message->message)
                        <div class="mb-1">{{ $message->message }}</div>
                    @endif
                    @if($message->attachment)
                        <div class="mt-2">
                            <a href="{{ \Illuminate\Support\Facades\Storage::url($message->attachment) }}" target="_blank" rel="noopener noreferrer">
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($message->attachment) }}" alt="{{ __('livechat::livechat.attachment') }}" class="img-fluid rounded" style="max-width: 200px; max-height: 200px;" onerror="this.style.display='none'">
                            </a>
                        </div>
                    @endif
                    <small class="d-block text-end opacity-75 mt-1" style="font-size: 0.7em;">
                        {{ $message->created_at?->format('h:i A') }}
                    </small>
                </div>
            @empty
                <div class="text-center text-muted my-5">
                    <div class="mb-3">
                        <i class="far fa-comments fa-3x text-muted opacity-50"></i>
                    </div>
                    <p>{{ __('livechat::livechat.start_conversation_hint') }}</p>
                </div>
            @endforelse
        </div>

        <!-- Input Area -->
        <div class="p-3 border-top bg-white">
            <form id="send-message-form" class="d-flex align-items-center gap-2">
                <input type="hidden" name="conversation_id" value="{{ $conversation->id }}">

                <label for="chat-attachment" class="btn btn-light border text-muted" data-bs-toggle="tooltip" title="{{ __('livechat::livechat.attach_image') }}">
                    <i class="fas fa-paperclip"></i>
                    <input type="file" id="chat-attachment" name="attachment" class="d-none" accept="image/*">
                </label>

                <input type="text" class="form-control" name="message" placeholder="{{ __('livechat::livechat.type_message_placeholder') }}" autocomplete="off">

                <button type="submit" class="btn btn-primary px-4">
                    <i class="fas fa-paper-plane me-2"></i> {{ __('livechat::livechat.send') }}
                </button>
            </form>
            <div id="file-preview" class="small text-muted mt-2 d-none">
                <i class="fas fa-file-image me-1"></i> <span id="filename"></span>
                <button type="button" class="btn-close btn-close-xs ms-2" aria-label="Remove"></button>
            </div>
            <div id="chat-error" class="alert alert-danger d-none mt-2" role="alert" style="display:none"></div>
            <div id="upload-progress" class="progress mt-2 d-none" style="height:6px;">
                <div id="upload-progress-bar" class="progress-bar" role="progressbar" style="width:0"></div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="{{ asset('modules/livechat/css/customer-chat.css') }}">

<script>
    window.LiveChatConfig = {
        csrfToken: '{{ csrf_token() }}',
        routes: {
            send: '{{ route("livechat.send") }}',
            messages: '{{ route("livechat.messages") }}'
        },
        translations: {
            fileTooLarge: '{{ __("livechat::livechat.file_too_large") }}',
            sending: '{{ __("livechat::livechat.sending") }}',
            send: '{{ __("livechat::livechat.send") }}',
            serverError: '{{ __("livechat::livechat.server_error") }}',
            failedToSend: '{{ __("livechat::livechat.failed_to_send") }}',
            unexpectedResponse: '{{ __("livechat::livechat.unexpected_response") }}',
            attachment: '{{ __("livechat::livechat.attachment") }}'
        }
    };
</script>
<script src="{{ asset('modules/livechat/js/customer-chat.js') }}"></script>
@endsection
