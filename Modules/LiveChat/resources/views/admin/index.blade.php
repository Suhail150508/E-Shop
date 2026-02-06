@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4" style="height: calc(100vh - 80px);">
    <div class="row h-100">
        <!-- Sidebar -->
        <div class="col-md-4 col-lg-3 h-100 border-end bg-white p-0 d-flex flex-column">
            <div class="p-3 border-bottom bg-light">
                <h5 class="mb-0">{{ __('Conversations') }}</h5>
            </div>
            <div class="flex-grow-1 overflow-auto" id="conversation-list">
                @foreach($conversations as $conversation)
                @php
                    $lastMsg = $conversation->messages->last();
                    $preview = $lastMsg ? ($lastMsg->message ? \Illuminate\Support\Str::limit($lastMsg->message, 30) : __('Attachment')) : __('common.no_messages_yet');
                @endphp
                <div class="conversation-item p-3 border-bottom cursor-pointer {{ $loop->first ? 'active' : '' }}"
                     onclick="loadConversation({{ $conversation->id }}, this)"
                     data-id="{{ $conversation->id }}">
                    <div class="d-flex justify-content-between align-items-start mb-1">
                        <strong class="text-truncate">{{ $conversation->customer_name ?? __('Guest') }}</strong>
                        @if(!$conversation->is_read_by_admin)
                            <span class="badge bg-danger rounded-pill unread-badge">{{ __('New') }}</span>
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
                    <h6 class="mb-0" id="chat-user-name">{{ $conversations->first()->customer_name ?? __('Guest') }}</h6>
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
                                <span class="visually-hidden">New alerts</span>
                            </span>
                        </label>
                        <input type="text" class="form-control" name="message" placeholder="{{ __('Type a reply...') }}" autocomplete="off">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send"></i>
                        </button>
                    </form>
                </div>
            @else
                <div class="h-100 d-flex align-items-center justify-content-center text-muted">
                    <div class="text-center">
                        <i class="bi bi-chat-dots mb-3" style="font-size: 3rem;"></i>
                        <p>{{ __('common.no_active_conversations') }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .cursor-pointer { cursor: pointer; }
    .conversation-item:hover { background-color: #f8f9fa; }
    .conversation-item.active { background-color: #eef2ff; border-left: 3px solid var(--bs-primary); }
    
    .message-bubble {
        max-width: 70%;
        padding: 10px 15px;
        border-radius: 15px;
        margin-bottom: 10px;
        position: relative;
    }
    
    .message-bubble.customer {
        background-color: #e9ecef; /* Light gray */
        color: #333;
        align-self: flex-start;
        border-bottom-left-radius: 2px;
    }
    
    .message-bubble.admin {
        background-color: #4f46e5; /* Primary */
        color: white;
        align-self: flex-end;
        border-bottom-right-radius: 2px;
    }
    
    #admin-chat-messages {
        display: flex;
        flex-direction: column;
    }
</style>

<script>
    let activeConversationId = {{ $conversations->first()->id ?? 'null' }};

    document.addEventListener('DOMContentLoaded', function() {
        if (activeConversationId) {
            loadConversation(activeConversationId);
        }

        const replyForm = document.getElementById('admin-reply-form');
        if (replyForm) {
            replyForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                const form = this;
                const formData = new FormData(form);
                const input = form.querySelector('input[name="message"]');
                const fileInput = form.querySelector('input[name="attachment"]');
                
                if (!input.value.trim() && !fileInput.files.length) return;

                // Optimistic append (Text only)
                if (input.value.trim() && !fileInput.files.length) {
                    appendMessage({
                        message: input.value.trim(),
                        sender_type: 'admin',
                        created_at: new Date().toISOString()
                    });
                    input.value = '';
                    scrollToBottom();
                }

                try {
                    const url = '{{ route("admin.livechat.reply", ":id") }}'.replace(':id', activeConversationId);
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                        body: formData
                    });
                    
                    const data = await response.json().catch(() => ({}));
                    if (response.ok && data.status === 'success') {
                        const hadAttachment = fileInput.files.length > 0;
                        fileInput.value = '';
                        resetAttachmentUI();
                        if (hadAttachment && data.message) {
                            appendMessage(data.message);
                            scrollToBottom();
                        }
                    } else {
                        const errMsg = data.error || data.message || (response.status === 500 ? '{{ __("common.server_error_try_again") }}' : '{{ __("common.error_sending_message_try_again") }}');
                        toastr.error(errMsg);
                    }
                } catch (error) {
                    console.error('Reply error:', error);
                    toastr.error('{{ __("common.error_sending_message_try_again") }}');
                }
            });
        }

        // Attachment UI
        const fileInput = document.querySelector('input[name="attachment"]');
        if (fileInput) {
            fileInput.addEventListener('change', function() {
                const label = document.getElementById('attachment-label');
                const icon = document.getElementById('attachment-icon');
                const indicator = document.getElementById('attachment-indicator');
                
                if (this.files && this.files[0]) {
                    label.classList.remove('btn-light');
                    label.classList.add('btn-primary');
                    icon.classList.remove('fa-paperclip');
                    icon.classList.add('fa-check');
                    indicator.classList.remove('d-none');
                } else {
                    resetAttachmentUI();
                }
            });
        }
    });

    function resetAttachmentUI() {
        const fileInput = document.querySelector('input[name="attachment"]');
        const label = document.getElementById('attachment-label');
        const icon = document.getElementById('attachment-icon');
        const indicator = document.getElementById('attachment-indicator');
        
        if(fileInput) fileInput.value = '';
        if(label) {
            label.classList.add('btn-light');
            label.classList.remove('btn-primary');
        }
        if(icon) {
            icon.classList.add('bi-paperclip');
            icon.classList.remove('bi-check');
        }
        if(indicator) indicator.classList.add('d-none');
    }

    async function loadConversation(id, element = null) {
        activeConversationId = id;
        const hiddenInput = document.getElementById('active-conversation-id');
        if(hiddenInput) hiddenInput.value = id;
        
        // Update UI active state
        if (element) {
            document.querySelectorAll('.conversation-item').forEach(el => el.classList.remove('active'));
            element.classList.add('active');
            // Remove unread badge locally
            const badge = element.querySelector('.unread-badge');
            if (badge) badge.remove();
        }

        try {
            const response = await fetch(`/admin/livechat/${id}`);
            if (!response.ok) throw new Error('Failed to load conversation');
            
            const data = await response.json();
            
            document.getElementById('chat-user-name').textContent = data.conversation.customer_name || '{{ __("Guest") }}';
            document.getElementById('chat-user-email').textContent = data.conversation.customer_email || '';
            
            const messagesArea = document.getElementById('admin-chat-messages');
            messagesArea.innerHTML = '';
            
            data.messages.forEach(msg => appendMessage(msg));

            // Start listening
            if (window.activeChannel) {
                if(window.Echo) window.Echo.leave(window.activeChannel);
            }
            
            window.activeChannel = `chat.${id}`;
            
            let echoRetries = 0;
            const initEcho = () => {
                if (window.Echo) {
                    console.log('Listening to channel:', window.activeChannel);
                    window.Echo.private(window.activeChannel)
                        .listen('.message.sent', (e) => {
                            console.log('Message received:', e);
                            appendMessage(e.message);
                            scrollToBottom();
                        });
                } else if (echoRetries < 20) {
                    echoRetries++;
                    setTimeout(initEcho, 100);
                } else {
                    console.error('Echo is not initialized after retries');
                }
            };
            initEcho();

            scrollToBottom();
        } catch (error) {
            console.error('Load conversation error:', error);
            toastr.error('{{ __("common.failed_to_load_conversation") }}');
        }
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function appendMessage(msg) {
        const div = document.createElement('div');
        div.className = 'message-bubble ' + (msg.sender_type || 'customer');
        let content = '';
        if (msg.message) {
            content += '<div class="mb-1">' + escapeHtml(msg.message) + '</div>';
        }
        if (msg.attachment) {
            const attPath = (msg.attachment.indexOf('/') === 0 ? '' : '/storage/') + msg.attachment;
            content += '<div class="mt-2"><a href="' + attPath + '" target="_blank" rel="noopener noreferrer"><img src="' + attPath + '" alt="{{ __("Attachment") }}" class="img-fluid rounded" style="max-width: 200px; max-height: 200px;" onerror="this.style.display=\'none\'"></a></div>';
        }
        const time = msg.created_at ? new Date(msg.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : '';
        div.innerHTML = content + '<small class="d-block text-end opacity-75" style="font-size: 0.7em;">' + escapeHtml(time) + '</small>';
        document.getElementById('admin-chat-messages').appendChild(div);
    }

    function scrollToBottom() {
        const area = document.getElementById('admin-chat-messages');
        area.scrollTop = area.scrollHeight;
    }


</script>
@endsection
