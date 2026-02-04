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
                    <h6 class="mb-0 text-white">Customer Support</h6>
                    <small class="text-white-50">We reply immediately</small>
                </div>
            </div>
            <button id="chat-close" class="btn-close btn-close-white"></button>
        </div>

        <div class="chat-body" id="chat-body">
            <!-- Messages Area -->
            <div id="chat-messages" class="chat-messages p-3">
                <div class="text-center text-muted small my-2">Today</div>
                <!-- Messages will be injected here -->
            </div>
        </div>

        <div class="chat-footer p-2 border-top" id="chat-input-area">
            <form id="send-message-form" class="d-flex align-items-center gap-2">
                <label for="chat-attachment" class="btn btn-sm btn-light text-muted">
                    <i class="fas fa-paperclip"></i>
                    <input type="file" id="chat-attachment" name="attachment" class="d-none">
                </label>
                <input type="text" class="form-control form-control-sm" name="message" placeholder="Type a message..." autocomplete="off">
                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="bi bi-send"></i>
                </button>
            </form>
        </div>
    </div>
</div>

<style>
    .live-chat-widget {
        position: fixed;
        bottom: 20px;
        left: 20px; /* Left side to avoid conflict with floating cart/scroll top */
        z-index: 10000;
        font-family: 'DM Sans', sans-serif;
    }

    .live-chat-toggle {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: var(--rust);
        color: white;
        border: none;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        font-size: 24px;
        cursor: pointer;
        transition: transform 0.3s ease;
        position: relative;
    }

    .live-chat-toggle:hover {
        transform: scale(1.1);
    }

    .notification-badge {
        position: absolute;
        top: 0;
        right: 0;
        background: red;
        color: white;
        font-size: 10px;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid white;
        display: none; /* Hidden by default */
    }

    .chat-window {
        position: absolute;
        bottom: 80px;
        left: 0;
        width: 350px;
        height: 500px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        border: 1px solid rgba(0,0,0,0.05);
        animation: slideUp 0.3s ease;
    }

    @media (max-width: 480px) {
        .chat-window {
            width: 92%;
            left: 4%;
            bottom: 90px;
        }
        .live-chat-toggle {
            width: 50px;
            height: 50px;
            font-size: 20px;
        }
    }

    @keyframes slideUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .chat-header {
        background: var(--rust);
        padding: 15px;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .chat-body {
        flex: 1;
        overflow-y: auto;
        background: #f8f9fa;
        position: relative;
    }

    .chat-messages {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .message {
        max-width: 80%;
        padding: 8px 12px;
        border-radius: 12px;
        font-size: 0.9rem;
        position: relative;
        word-wrap: break-word;
    }

    .message.customer {
        align-self: flex-end;
        background: var(--rust);
        color: white;
        border-bottom-right-radius: 2px;
    }

    .message.admin {
        align-self: flex-start;
        background: white;
        border: 1px solid #eee;
        border-bottom-left-radius: 2px;
        color: #333;
    }

    .message-time {
        font-size: 0.7rem;
        opacity: 0.7;
        margin-top: 4px;
        display: block;
        text-align: right;
    }
    
    .message img {
        max-width: 100%;
        border-radius: 8px;
        margin-top: 5px;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const widget = document.getElementById('live-chat-widget');
        const toggleBtn = document.getElementById('live-chat-toggle');
        const chatWindow = document.getElementById('chat-window');
        const closeBtn = document.getElementById('chat-close');
        const sendForm = document.getElementById('send-message-form');
        const messagesArea = document.getElementById('chat-messages');
        const inputArea = document.getElementById('chat-input-area');
        
        let conversationId = null;
        let lastMessageId = 0;


        // Auto init chat
        initChat();

        // Toggle Window
        toggleBtn.addEventListener('click', () => {
            chatWindow.classList.toggle('d-none');
            if (!chatWindow.classList.contains('d-none')) {
                scrollToBottom();
                if (conversationId) {
                    fetchMessages();
                }
            }
        });

        closeBtn.addEventListener('click', () => {
            chatWindow.classList.add('d-none');
        });

        // Initialize Chat (Find or Create)
        async function initChat() {
            try {
                const response = await fetch('{{ route("livechat.start") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                
                if (data.status === 'success') {
                    conversationId = data.conversation_id;
                    renderMessages(data.messages);
                    startListening();
                }
            } catch (error) {
                console.error('Error initializing chat:', error);
            }
        }

        // Send Message
        sendForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const input = sendForm.querySelector('input[name="message"]');
            const fileInput = sendForm.querySelector('input[name="attachment"]');
            const message = input.value.trim();
            
            if (!message && !fileInput.files.length) return;

            const formData = new FormData();
            formData.append('conversation_id', conversationId);
            formData.append('message', message);
            if (fileInput.files.length) {
                formData.append('attachment', fileInput.files[0]);
            }

            // Optimistic UI for text messages
            if (message && !fileInput.files.length) {
                appendMessage({
                    message: message,
                    sender_type: 'customer',
                    created_at: new Date().toISOString(),
                    attachment: null,
                    sender: { name: 'Me' } // Mock sender
                });
                input.value = '';
                scrollToBottom();
            }

            try {
                const response = await fetch('{{ route("livechat.send") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                });
                
                const data = await response.json();
                if (data.status === 'success') {
                    input.value = ''; // Ensure cleared
                    fileInput.value = ''; // Clear file input
                    // If it was a file message, append it now (since we didn't optimistically)
                    if (data.message.attachment) {
                        appendMessage(data.message);
                        scrollToBottom();
                    }
                }
            } catch (e) {
                console.error(e);
            }
        });

        function startListening() {
            if (!conversationId) return;
            
            if (window.Echo) {
                window.Echo.private(`chat.${conversationId}`)
                    .listen('.message.sent', (e) => {
                        // Only append if it's NOT from me (to avoid duplicate with optimistic UI)
                        // OR if it IS from me but has an attachment (which wasn't optimistic)
                        const myId = {{ auth()->id() ?? 'null' }};
                        if (e.message.sender_id !== myId) {
                            appendMessage(e.message);
                            scrollToBottom();
                        } else if (e.message.attachment) {
                            // Already handled by fetch success, but just in case...
                            // Actually fetch success handles it. Let's strictly ignore own messages here
                            // to avoid race conditions.
                        }
                    });
            }
        }

        // Fetch Messages
        async function fetchMessages() {
            if (!conversationId) return;
            try {
                const response = await fetch('{{ route("livechat.messages") }}');
                const data = await response.json();
                
                if (data.messages) {
                    renderMessages(data.messages);
                }
            } catch (error) {
                console.error('Error fetching messages:', error);
            }
        }

        function renderMessages(messages) {
            messagesArea.innerHTML = '<div class="text-center text-muted small my-2">Today</div>';
            messages.forEach(msg => appendMessage(msg));
            scrollToBottom();
        }

        function appendMessage(msg) {
            const div = document.createElement('div');
            div.className = `message ${msg.sender_type}`;
            
            let content = '';
            if (msg.message) content += `<div class="text">${msg.message}</div>`;
            if (msg.attachment) {
                content += `<div class="attachment"><a href="/storage/${msg.attachment}" target="_blank"><img src="/storage/${msg.attachment}" alt="Attachment"></a></div>`;
            }
            content += `<span class="message-time">${new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</span>`;
            
            div.innerHTML = content;
            messagesArea.appendChild(div);
        }

        function scrollToBottom() {
            const chatBody = document.getElementById('chat-body');
            chatBody.scrollTop = chatBody.scrollHeight;
        }


    });
</script>
