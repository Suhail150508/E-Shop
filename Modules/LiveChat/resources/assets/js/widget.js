document.addEventListener('DOMContentLoaded', function() {
    const config = window.LiveChatWidgetConfig;
    if (!config) return;

    const toggleBtn = document.getElementById('live-chat-toggle');
    const chatWindow = document.getElementById('chat-window');
    const closeBtn = document.getElementById('chat-close');
    const sendForm = document.getElementById('send-message-form');
    const messagesArea = document.getElementById('chat-messages');

    let conversationId = null;


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
            const response = await fetch(config.routes.start, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': config.csrfToken,
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
                // Silent fail
            }
        }

    // Send Message
    sendForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const input = sendForm.querySelector('input[name="message"]');
        const fileInput = sendForm.querySelector('input[name="attachment"]');
        const message = input.value.trim();

        if (!message && !fileInput.files.length) return;

        clearError();

        const formData = new FormData();
        formData.append('conversation_id', conversationId);
        formData.append('message', message);
        if (fileInput.files.length) {
            const file = fileInput.files[0];
            const maxBytes = 2 * 1024 * 1024; // 2MB
            if (file.size > maxBytes) {
                showError(config.translations.fileTooLarge);
                return;
            }
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
            let responseData;
            if (formData.get('attachment')) {
                responseData = await uploadWithProgress(config.routes.send, formData, (pct) => showProgress(pct));
            } else {
                const response = await fetch(config.routes.send, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': config.csrfToken
                    },
                    body: formData
                });
                responseData = await response.json();
            }

            if (responseData.status === 'success') {
                if (responseData.message && responseData.message.attachment) {
                    appendMessage(responseData.message);
                    scrollToBottom();
                }
            } else if (responseData.error) {
                showError(responseData.error);
            }
            } catch (e) {
                showError(config.translations.failedToSend);
            } finally {
            hideProgress();
        }
    });

    function startListening() {
        if (!conversationId) return;

        if (window.Echo) {
            window.Echo.private(`chat.${conversationId}`)
                .listen('.message.sent', (e) => {
                    // Only append if it's NOT from me (to avoid duplicate with optimistic UI)
                    // OR if it IS from me but has an attachment (which wasn't optimistic)
                    const myId = config.userId;
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
            const response = await fetch(config.routes.messages);
            const data = await response.json();

            if (data.messages) {
                renderMessages(data.messages);
            }
        } catch (error) {
            // Silent error
        }
    }

    function renderMessages(messages) {
        messagesArea.innerHTML = `<div class="text-center text-muted small my-2">${config.translations.today}</div>`;
        messages.forEach(msg => appendMessage(msg));
        scrollToBottom();
    }

    function appendMessage(msg) {
        const div = document.createElement('div');
        div.className = `message ${msg.sender_type}`;

        // Message text as a text node
        if (msg.message) {
            const text = document.createElement('div');
            text.className = 'text';
            text.textContent = msg.message;
            div.appendChild(text);
        }
        if (msg.attachment) {
            const attach = document.createElement('div');
            attach.className = 'attachment';
            const url = (typeof msg.attachment === 'string' && msg.attachment.startsWith('http')) ? msg.attachment : `/storage/${msg.attachment}`;
            const a = document.createElement('a');
            a.href = url;
            a.target = '_blank';
            const img = document.createElement('img');
            img.src = url;
            img.alt = config.translations.attachment;
            a.appendChild(img);
            attach.appendChild(a);
            div.appendChild(attach);
        }
        const time = document.createElement('span');
        time.className = 'message-time';
        time.textContent = new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
        div.appendChild(time);
        messagesArea.appendChild(div);
    }

    function scrollToBottom() {
        const chatBody = document.getElementById('chat-body');
        chatBody.scrollTop = chatBody.scrollHeight;
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
            xhr.setRequestHeader('X-CSRF-TOKEN', config.csrfToken);

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
