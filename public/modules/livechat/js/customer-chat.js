(function() {
    const config = window.LiveChatConfig;
    
    document.addEventListener('DOMContentLoaded', function() {
        const messagesArea = document.getElementById('chat-messages');
        const sendForm = document.getElementById('send-message-form');
        const fileInput = document.getElementById('chat-attachment');
        const filePreview = document.getElementById('file-preview');
        const filenameSpan = document.getElementById('filename');
        const closeFileBtn = filePreview ? filePreview.querySelector('.btn-close') : null;

        if (!messagesArea || !sendForm) return;

        // Scroll to bottom on load
        scrollToBottom();

        // File Selection Handling
        if (fileInput) {
            fileInput.addEventListener('change', function() {
                clearError();
                if (this.files.length > 0) {
                    const file = this.files[0];
                    const maxBytes = 2 * 1024 * 1024; // 2MB
                    if (file.size > maxBytes) {
                        showError(config.translations.fileTooLarge);
                        this.value = '';
                        filePreview.classList.add('d-none');
                        return;
                    }

                    if (filenameSpan) filenameSpan.textContent = this.files[0].name;
                    if (filePreview) filePreview.classList.remove('d-none');
                } else {
                    if (filePreview) filePreview.classList.add('d-none');
                }
            });
        }

        if (closeFileBtn) {
            closeFileBtn.addEventListener('click', function() {
                if (fileInput) fileInput.value = '';
                if (filePreview) filePreview.classList.add('d-none');
            });
        }

        // Send Message
        sendForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const input = sendForm.querySelector('input[name="message"]');
            const message = input.value.trim();
            const hasFile = fileInput && fileInput.files.length > 0;

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
                const originalBtnContent = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> ' + config.translations.sending;

                // If file present, use XHR to show upload progress
                let responseData;
                if (formData.get('attachment') && formData.get('attachment').size > 0) {
                    responseData = await uploadWithProgress(config.routes.send, formData, (pct) => {
                        showProgress(pct);
                    });
                } else {
                    const response = await fetch(config.routes.send, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': config.csrfToken,
                            'Accept': 'application/json'
                        },
                        body: formData
                    });
                    responseData = await response.json().catch(() => ({}));
                    if (!response.ok && !responseData.error) {
                        responseData = { error: response.status === 500 ? config.translations.serverError : config.translations.failedToSend };
                    }
                }

                // Normalize server response
                if (responseData) {
                    if (responseData.errors) {
                        const msgs = Object.values(responseData.errors).flat();
                        showError(msgs.join(' '));
                    } else if (responseData.error) {
                        showError(responseData.error);
                    } else if (responseData.status === 'success') {
                        if (hasFile) {
                            appendMessage(responseData.message);
                            if (fileInput) fileInput.value = '';
                            if (filePreview) filePreview.classList.add('d-none');
                            input.value = ''; 
                            scrollToBottom();
                        }
                    } else {
                        showError(responseData.error || config.translations.unexpectedResponse);
                    }
                }
            } catch (error) {
                showError(config.translations.failedToSend);
            } finally {
                const submitBtn = sendForm.querySelector('button[type="submit"]');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i> ' + config.translations.send;
                hideProgress();
            }
        });

        // Polling
        setInterval(fetchMessages, 3000);

        async function fetchMessages() {
            if (document.hidden) return;

            try {
                const response = await fetch(config.routes.messages);
                const data = await response.json();

                if (data.messages) {
                    const currentCount = messagesArea.querySelectorAll('.message-bubble').length;
                    if (data.messages.length > currentCount) {
                        messagesArea.innerHTML = '';
                        data.messages.forEach(msg => appendMessage(msg));
                        scrollToBottom();
                    }
                }
            } catch (error) {
                // Error fetching messages
            }
        }

        function appendMessage(msg) {
            const div = document.createElement('div');
            div.className = `message-bubble ${msg.sender_type}`;

            if (msg.message) {
                const textEl = document.createElement('div');
                textEl.className = 'mb-1';
                textEl.textContent = msg.message;
                div.appendChild(textEl);
            }

            if (msg.attachment) {
                const container = document.createElement('div');
                container.className = 'mt-2';

                const url = (typeof msg.attachment === 'string' && msg.attachment.startsWith('http')) ? msg.attachment : `/storage/${msg.attachment}`;

                const link = document.createElement('a');
                link.href = url;
                link.target = '_blank';

                const img = document.createElement('img');
                img.src = url;
                img.alt = config.translations.attachment;
                img.className = 'img-fluid rounded';
                img.style.maxWidth = '200px';
                img.style.maxHeight = '200px';
                img.onerror = function() { this.style.display = 'none'; };

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
            if (err) {
                err.textContent = msg;
                err.classList.remove('d-none');
                err.style.display = 'block';
            }
        }

        function clearError() {
            const err = document.getElementById('chat-error');
            if (err) {
                err.textContent = '';
                err.classList.add('d-none');
                err.style.display = 'none';
            }
        }

        function showProgress(pct) {
            const progress = document.getElementById('upload-progress');
            const bar = document.getElementById('upload-progress-bar');
            if (progress && bar) {
                progress.classList.remove('d-none');
                bar.style.width = Math.floor(pct) + '%';
            }
        }

        function hideProgress() {
            const progress = document.getElementById('upload-progress');
            const bar = document.getElementById('upload-progress-bar');
            if (progress && bar) {
                progress.classList.add('d-none');
                bar.style.width = '0%';
            }
        }

        function uploadWithProgress(url, formData, onProgress) {
            return new Promise((resolve, reject) => {
                const xhr = new XMLHttpRequest();
                xhr.open('POST', url, true);
                xhr.setRequestHeader('X-CSRF-TOKEN', config.csrfToken);
                xhr.setRequestHeader('Accept', 'application/json');

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
                            resolve(resp);
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
})();
