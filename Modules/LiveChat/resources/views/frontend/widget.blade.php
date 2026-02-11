<div id="live-chat-widget" class="live-chat-widget">
    <button id="live-chat-toggle" class="live-chat-toggle">
        <i class="bi bi-chat-dots-fill"></i>
        <span class="notification-badge d-none" id="chat-badge">0</span>
    </button>

    <div class="chat-window d-none" id="chat-window">
        <div class="chat-header">
            <div class="d-flex align-items-center">
                <div class="avatar me-2">
                    <i class="fas fa-headset"></i>
                </div>
                <div>
                    <h6 class="mb-0 text-white">{{ __('livechat::livechat.customer_support') }}</h6>
                    <small class="text-white-50">{{ __('livechat::livechat.we_reply_immediately') }}</small>
                </div>
            </div>
            <button id="chat-close" class="btn-close btn-close-white"></button>
        </div>

        <div class="chat-body" id="chat-body">
            <!-- Messages Area -->
            <div id="chat-messages" class="chat-messages p-3">
                <div class="text-center text-muted small my-2">{{ __('livechat::livechat.today') }}</div>
                <!-- Messages will be injected here -->
            </div>
        </div>

        <div class="chat-footer p-2 border-top" id="chat-input-area">
            <form id="send-message-form" class="d-flex align-items-center gap-2">
                <label for="chat-attachment" class="btn btn-sm btn-light text-muted">
                    <i class="fas fa-paperclip"></i>
                    <input type="file" id="chat-attachment" name="attachment" class="d-none">
                </label>
                <input type="text" class="form-control form-control-sm" name="message" placeholder="{{ __('livechat::livechat.type_message_placeholder') }}" autocomplete="off">
                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="bi bi-send"></i>
                </button>
            </form>
            <div id="chat-error" class="alert alert-danger d-none mt-2" role="alert" style="display:none"></div>
            <div id="upload-progress" class="progress mt-2 d-none" style="height:6px;">
                <div id="upload-progress-bar" class="progress-bar" role="progressbar" style="width:0%"></div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="{{ asset('modules/livechat/css/widget.css') }}">

<script>
    window.LiveChatWidgetConfig = {
        csrfToken: '{{ csrf_token() }}',
        userId: {{ auth()->id() ?? 'null' }},
        routes: {
            start: '{{ route("livechat.start") }}',
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
            attachment: '{{ __("livechat::livechat.attachment") }}',
            today: '{{ __("livechat::livechat.today") }}'
        }
    };
</script>
<script src="{{ asset('modules/livechat/js/widget.js') }}"></script>
