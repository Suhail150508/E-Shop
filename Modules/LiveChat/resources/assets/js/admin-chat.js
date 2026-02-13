(function() {
    let activeConversationId = window.LiveChatConfig.activeConversationId;
    const config = window.LiveChatConfig;

    document.addEventListener('DOMContentLoaded', function() {
        if (activeConversationId) {
            loadConversation(activeConversationId);
        }

        const replyForm = document.getElementById('admin-reply-form');
        if (replyForm) {
            replyForm.addEventListener('submit', handleReplySubmit);
        }

        // Attachment UI
        const fileInput = document.querySelector('input[name="attachment"]');
        if (fileInput) {
            fileInput.addEventListener('change', handleFileChange);
        }
    });

    async function handleReplySubmit(e) {
        e.preventDefault();
        const form = this;
        const formData = new FormData(form);
        const input = form.querySelector('input[name="message"]');
        const fileInput = form.querySelector('input[name="attachment"]');
        
        if (!input.value.trim() && !fileInput.files.length) return;

        // Optimistic append (Text only) - REMOVED to prevent duplicates with Echo
        if (input.value.trim() && !fileInput.files.length) {
            // appendMessage removed
            input.value = '';
            scrollToBottom();
        }

        try {
            const url = config.routes.reply.replace(':id', activeConversationId);
            const response = await fetch(url, {
                method: 'POST',
                headers: { 
                    'X-CSRF-TOKEN': config.csrfToken, 
                    'Accept': 'application/json' 
                },
                body: formData
            });
            
            const data = await response.json().catch(() => ({}));
            if (response.ok && data.status === 'success') {
                const hadAttachment = fileInput.files.length > 0;
                fileInput.value = '';
                resetAttachmentUI();
                if (hadAttachment && data.message) {
                    // appendMessage(data.message); // Removed to prevent duplicates
                    scrollToBottom();
                }
            } else {
                const errMsg = data.error || data.message || (response.status === 500 ? config.translations.serverError : config.translations.sendError);
                toastr.error(errMsg);
            }
        } catch (error) {
            toastr.error(config.translations.sendError);
        }
    }

    function handleFileChange() {
        const label = document.getElementById('attachment-label');
        const icon = document.getElementById('attachment-icon');
        const indicator = document.getElementById('attachment-indicator');
        
        if (this.files && this.files[0]) {
            label.classList.remove('btn-secondary-soft');
            label.classList.add('btn-primary');
            icon.classList.remove('bi-paperclip');
            icon.classList.add('bi-check');
            indicator.classList.remove('d-none');
        } else {
            resetAttachmentUI();
        }
    }

    function resetAttachmentUI() {
        const fileInput = document.querySelector('input[name="attachment"]');
        const label = document.getElementById('attachment-label');
        const icon = document.getElementById('attachment-icon');
        const indicator = document.getElementById('attachment-indicator');
        
        if(fileInput) fileInput.value = '';
        if(label) {
            label.classList.add('btn-secondary-soft');
            label.classList.remove('btn-primary');
        }
        if(icon) {
            icon.classList.add('bi-paperclip');
            icon.classList.remove('bi-check');
        }
        if(indicator) indicator.classList.add('d-none');
    }

    window.loadConversation = async function(id, element = null) {
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
            
            document.getElementById('chat-user-name').textContent = data.conversation.customer_name || config.translations.guest;
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
                    window.Echo.private(window.activeChannel)
                        .listen('.message.sent', (e) => {
                            appendMessage(e.message);
                            scrollToBottom();
                        });
                } else if (echoRetries < 20) {
                    echoRetries++;
                    setTimeout(initEcho, 100);
                }
            };
            initEcho();

            scrollToBottom();
        } catch (error) {
            toastr.error(config.translations.loadError);
        }
    };

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
            content += '<div class="mt-2"><a href="' + attPath + '" target="_blank" rel="noopener noreferrer"><img src="' + attPath + '" alt="' + config.translations.attachment + '" class="img-fluid rounded" style="max-width: 200px; max-height: 200px;" onerror="this.style.display=\'none\'"></a></div>';
        }
        const time = msg.created_at ? new Date(msg.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : '';
        div.innerHTML = content + '<small class="d-block text-end opacity-75" style="font-size: 0.7em;">' + escapeHtml(time) + '</small>';
        document.getElementById('admin-chat-messages').appendChild(div);
    }

    function scrollToBottom() {
        const area = document.getElementById('admin-chat-messages');
        if(area) area.scrollTop = area.scrollHeight;
    }

})();
