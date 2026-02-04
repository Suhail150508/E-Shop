@extends('layouts.customer')

@section('title', __('Live Chat'))

@section('account_content')
<div class="card border-0 shadow-sm rounded-3 h-100">
    <div class="card-header bg-white border-bottom p-3 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0 text-dark">{{ __('Live Chat Support') }}</h5>
        <span class="badge bg-success-subtle text-success">
            <i class="fas fa-circle me-1 small"></i> Online
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
                            <a href="{{ Storage::url($message->attachment) }}" target="_blank">
                                <img src="{{ Storage::url($message->attachment) }}" alt="Attachment" class="img-fluid rounded" style="max-width: 200px; max-height: 200px;">
                            </a>
                        </div>
                    @endif
                    <small class="d-block text-end opacity-75 mt-1" style="font-size: 0.7em;">
                        {{ $message->created_at->format('h:i A') }}
                    </small>
                </div>
            @empty
                <div class="text-center text-muted my-5">
                    <div class="mb-3">
                        <i class="far fa-comments fa-3x text-muted opacity-50"></i>
                    </div>
                    <p>{{ __('Start a conversation with our support team.') }}</p>
                </div>
            @endforelse
        </div>

        <!-- Input Area -->
        <div class="p-3 border-top bg-white">
            <form id="send-message-form" class="d-flex align-items-center gap-2">
                <input type="hidden" name="conversation_id" value="{{ $conversation->id }}">
                
                <label for="chat-attachment" class="btn btn-light border text-muted" data-bs-toggle="tooltip" title="Attach Image">
                    <i class="fas fa-paperclip"></i>
                    <input type="file" id="chat-attachment" name="attachment" class="d-none" accept="image/*">
                </label>
                
                <input type="text" class="form-control" name="message" placeholder="{{ __('Type a message...') }}" autocomplete="off">
                
                <button type="submit" class="btn btn-primary px-4">
                    <i class="fas fa-paper-plane me-2"></i> {{ __('Send') }}
                </button>
            </form>
            <div id="file-preview" class="small text-muted mt-2 d-none">
                <i class="fas fa-file-image me-1"></i> <span id="filename"></span> 
                <button type="button" class="btn-close btn-close-xs ms-2" aria-label="Remove"></button>
            </div>
        </div>
    </div>
</div>

<style>
    .message-bubble {
        max-width: 75%;
        padding: 12px 16px;
        border-radius: 12px;
        margin-bottom: 15px;
        position: relative;
        font-size: 0.95rem;
        line-height: 1.5;
    }
    
    .message-bubble.customer {
        background-color: var(--rust);
        color: white;
        align-self: flex-end;
        border-bottom-right-radius: 2px;
        margin-left: auto;
    }
    
    .message-bubble.admin {
        background-color: white;
        color: var(--charcoal);
        border: 1px solid rgba(0,0,0,0.05);
        align-self: flex-start;
        border-bottom-left-radius: 2px;
        margin-right: auto;
        box-shadow: 0 2px 5px rgba(0,0,0,0.02);
    }

    #chat-messages {
        display: flex;
        flex-direction: column;
    }
    
    /* Scrollbar Styling */
    #chat-messages::-webkit-scrollbar {
        width: 6px;
    }
    #chat-messages::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    #chat-messages::-webkit-scrollbar-thumb {
        background: #ccc;
        border-radius: 3px;
    }
    #chat-messages::-webkit-scrollbar-thumb:hover {
        background: #aaa;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const messagesArea = document.getElementById('chat-messages');
        const sendForm = document.getElementById('send-message-form');
        const fileInput = document.getElementById('chat-attachment');
        const filePreview = document.getElementById('file-preview');
        const filenameSpan = document.getElementById('filename');
        const closeFileBtn = filePreview.querySelector('.btn-close');
        
        // Scroll to bottom on load
        scrollToBottom();

        // File Selection Handling
        fileInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                filenameSpan.textContent = this.files[0].name;
                filePreview.classList.remove('d-none');
            } else {
                filePreview.classList.add('d-none');
            }
        });

        closeFileBtn.addEventListener('click', function() {
            fileInput.value = '';
            filePreview.classList.add('d-none');
        });

        // Send Message
        sendForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const input = sendForm.querySelector('input[name="message"]');
            const message = input.value.trim();
            const hasFile = fileInput.files.length > 0;
            
            if (!message && !hasFile) return;

            const formData = new FormData(sendForm);

            // Optimistic UI (Text only)
            if (message && !hasFile) {
                appendMessage({
                    message: message,
                    sender_type: 'customer',
                    created_at: new Date().toISOString(),
                    attachment: null
                });
                input.value = '';
                scrollToBottom();
            }

            try {
                const submitBtn = sendForm.querySelector('button[type="submit"]');
                const originalBtnText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';

                const response = await fetch('{{ route("livechat.send") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.status === 'success') {
                    if (hasFile) {
                        // For file uploads, we need the server response to get the URL
                        // So we append only after success
                        appendMessage(data.message);
                        fileInput.value = '';
                        filePreview.classList.add('d-none');
                        input.value = ''; // Clear text too if mixed
                        scrollToBottom();
                    } else if (!message) {
                        // Should not happen given logic, but safety
                    }
                }
            } catch (error) {
                console.error('Error sending message:', error);
            } finally {
                const submitBtn = sendForm.querySelector('button[type="submit"]');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i> {{ __("Send") }}';
            }
        });

        // Polling
        setInterval(fetchMessages, 3000);

        async function fetchMessages() {
            if (document.hidden) return;
            
            try {
                const response = await fetch('{{ route("livechat.messages") }}');
                const data = await response.json();
                
                if (data.messages) {
                    // Simple Diffing: clear and redraw is inefficient but safe. 
                    // Better: Only append new. For now, let's just replace content if count differs?
                    // Actually, replacing content kills optimistic updates if polling happens fast.
                    // Let's implement a smarter append or just trust the server state is truth.
                    // Given the previous implementation used full replace, let's stick to that 
                    // BUT avoid flickering by checking length.
                    
                    const currentCount = messagesArea.querySelectorAll('.message-bubble').length;
                    if (data.messages.length > currentCount) {
                        // Re-render all to be safe about order and updates
                        messagesArea.innerHTML = '';
                        data.messages.forEach(msg => appendMessage(msg));
                        scrollToBottom();
                    }
                }
            } catch (error) {
                console.error('Error fetching messages:', error);
            }
        }

        function appendMessage(msg) {
            // Check if message already exists (to avoid duplicates from optimistic UI + polling)
            // A simple way is to check if the last message is identical?
            // Or just clear innerHTML as done in polling.
            // For appendMessage called by submit, we just append.
            
            const div = document.createElement('div');
            div.className = `message-bubble ${msg.sender_type}`;
            
            let content = '';
            if (msg.message) content += `<div class="mb-1">${msg.message}</div>`;
            if (msg.attachment) {
                const url = msg.attachment.startsWith('http') ? msg.attachment : `/storage/${msg.attachment}`;
                content += `<div class="mt-2"><a href="${url}" target="_blank">
                    <img src="${url}" alt="Attachment" class="img-fluid rounded" style="max-width: 200px; max-height: 200px;">
                </a></div>`;
            }
            
            const time = new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
            div.innerHTML = `${content}<small class="d-block text-end opacity-75 mt-1" style="font-size: 0.7em;">${time}</small>`;
            
            messagesArea.appendChild(div);
        }

        function scrollToBottom() {
            messagesArea.scrollTop = messagesArea.scrollHeight;
        }
    });
</script>
@endsection
