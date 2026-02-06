@extends('layouts.customer')

@section('title', __('Live Chat'))

@section('account_content')
<div class="card border-0 shadow-sm rounded-3 h-100">
    <div class="card-header bg-white border-bottom p-3 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0 text-dark">{{ __('Live Chat Support') }}</h5>
        <span class="badge bg-success-subtle text-success">
            <i class="fas fa-circle me-1 small"></i> {{ __('Online') }}
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
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($message->attachment) }}" alt="{{ __('Attachment') }}" class="img-fluid rounded" style="max-width: 200px; max-height: 200px;" onerror="this.style.display='none'">
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
            <div id="chat-error" class="alert alert-danger d-none mt-2" role="alert" style="display:none"></div>
            <div id="upload-progress" class="progress mt-2 d-none" style="height:6px;">
                <div id="upload-progress-bar" class="progress-bar" role="progressbar" style="width:0"></div>
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
            clearError();
            if (this.files.length > 0) {
                const file = this.files[0];
                const maxBytes = 2 * 1024 * 1024; // 2MB
                if (file.size > maxBytes) {
                    showError('Selected file exceeds the maximum allowed size of 2 MB.');
                    this.value = '';
                    filePreview.classList.add('d-none');
                    return;
                }

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
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';

                // If file present, use XHR to show upload progress
                let responseData;
                if (formData.get('attachment')) {
                    responseData = await uploadWithProgress('{{ route("livechat.send") }}', formData, (pct) => {
                        showProgress(pct);
                    });
                } else {
                    const response = await fetch('{{ route("livechat.send") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: formData
                    });
                    responseData = await response.json().catch(() => ({}));
                    if (!response.ok && !responseData.error) {
                        responseData = { error: response.status === 500 ? '{{ __("Server error. Please try again.") }}' : '{{ __("Failed to send message.") }}' };
                    }
                }

                // Normalize server response which may include `errors` (validation) or `error` (string)
                if (responseData) {
                    if (responseData.errors) {
                        // Laravel validation errors come as an object of arrays
                        const msgs = Object.values(responseData.errors).flat();
                        showError(msgs.join(' '));
                    } else if (responseData.error) {
                        showError(responseData.error);
                    } else if (responseData.status === 'success') {
                        if (hasFile) {
                            appendMessage(responseData.message);
                            fileInput.value = '';
                            filePreview.classList.add('d-none');
                            input.value = ''; // Clear text too if mixed
                            scrollToBottom();
                        }
                    } else {
                        showError(responseData.error || '{{ __("An unexpected response was received. Please try again.") }}');
                    }
                }
            } catch (error) {
                console.error('Error sending message:', error);
                showError('An error occurred while sending the message. Please try again.');
            } finally {
                const submitBtn = sendForm.querySelector('button[type="submit"]');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i> {{ __("Send") }}';
                hideProgress();
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
            const div = document.createElement('div');
            div.className = `message-bubble ${msg.sender_type}`;

            // Message text (use textContent to avoid XSS)
            if (msg.message) {
                const textEl = document.createElement('div');
                textEl.className = 'mb-1';
                textEl.textContent = msg.message;
                div.appendChild(textEl);
            }

            // Attachment handling (create elements and set src/href safely)
            if (msg.attachment) {
                const container = document.createElement('div');
                container.className = 'mt-2';

                const url = (typeof msg.attachment === 'string' && msg.attachment.startsWith('http')) ? msg.attachment : `/storage/${msg.attachment}`;

                const link = document.createElement('a');
                link.href = url;
                link.target = '_blank';

                const img = document.createElement('img');
                img.src = url;
                img.alt = 'Attachment';
                img.className = 'img-fluid rounded';
                img.style.maxWidth = '200px';
                img.style.maxHeight = '200px';

                link.appendChild(img);
                container.appendChild(link);
                div.appendChild(container);
            }

            const time = new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
            const small = document.createElement('small');
            small.className = 'd-block text-end opacity-75 mt-1';
            small.style.fontSize = '0.7em';
            small.textContent = time;
            div.appendChild(small);

            messagesArea.appendChild(div);
        }

        function scrollToBottom() {
            messagesArea.scrollTop = messagesArea.scrollHeight;
        }

        function showError(msg) {
            const err = document.getElementById('chat-error');
            err.textContent = msg;
            err.classList.remove('d-none');
            err.style.display = 'block';
        }

        function clearError() {
            const err = document.getElementById('chat-error');
            err.textContent = '';
            err.classList.add('d-none');
            err.style.display = 'none';
        }

        function showProgress(pct) {
            const progress = document.getElementById('upload-progress');
            const bar = document.getElementById('upload-progress-bar');
            progress.classList.remove('d-none');
            bar.style.width = Math.floor(pct) + '%';
        }

        function hideProgress() {
            const progress = document.getElementById('upload-progress');
            const bar = document.getElementById('upload-progress-bar');
            progress.classList.add('d-none');
            bar.style.width = '0%';
        }

        function uploadWithProgress(url, formData, onProgress) {
            return new Promise((resolve, reject) => {
                const xhr = new XMLHttpRequest();
                xhr.open('POST', url, true);
                xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');

                xhr.upload.addEventListener('progress', (e) => {
                    if (e.lengthComputable) {
                        const percent = (e.loaded / e.total) * 100;
                        onProgress(percent);
                    }
                });

                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4) {
                        try {
                            const resp = JSON.parse(xhr.responseText);
                            if (xhr.status >= 200 && xhr.status < 300) {
                                resolve(resp);
                            } else {
                                resolve(resp);
                            }
                        } catch (e) {
                            reject(e);
                        }
                    }
                };

                xhr.onerror = function () {
                    reject(new Error('Upload failed'));
                };

                xhr.send(formData);
            });
        }
    });
</script>
@endsection
