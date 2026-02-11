/**
 * Virtual Try-On (global): works from product cards and product detail page.
 * Requires #tryOnModal and .try-on-btn elements (data-id, optional data-image).
 */
(function() {
    document.addEventListener('DOMContentLoaded', function() {
        var productId = null;
        var tryModalEl = document.getElementById('tryOnModal');
        var customerImage = document.getElementById('customerImage');
        var startTryBtn = document.getElementById('startTryBtn');
        var uploadStep = document.getElementById('tryonUploadStep');
        var resultStep = document.getElementById('tryonResultStep');
        var previewImage = document.getElementById('previewImage');
        var dropzone = document.getElementById('tryonDropzone');
        var selectedPreview = document.getElementById('tryonSelectedPreview');
        var selectedImg = document.getElementById('tryonSelectedImg');
        var removeSelectionBtn = document.getElementById('tryonRemoveSelection');
        var downloadBtn = document.getElementById('downloadBtn');
        var retryBtn = document.getElementById('retryBtn');

        var tryModal = window.bootstrap && tryModalEl ? new bootstrap.Modal(tryModalEl) : null;
        var virtualTryUrl = tryModalEl ? (tryModalEl.getAttribute('data-virtual-try-url') || '/virtual-try') : '/virtual-try';
        var msgPleaseUpload = tryModalEl ? (tryModalEl.getAttribute('data-msg-please-upload') || 'Please upload a photo first.') : 'Please upload a photo first.';
        var msgError = tryModalEl ? (tryModalEl.getAttribute('data-msg-error') || 'Something went wrong. Please try again.') : 'Something went wrong. Please try again.';
        var msgProcessing = tryModalEl ? (tryModalEl.getAttribute('data-msg-processing') || 'Processing...') : 'Processing...';

        var resultAdjust = document.getElementById('tryonResultAdjust');
        var resultBg = document.getElementById('tryonResultBg');
        var resultOverlay = document.getElementById('tryonResultOverlay');
        var positionControls = document.getElementById('tryonPositionControls');
        var MOVE_STEP = 8;
        var WIDTH_STEP = 0.18;
        var MIN_OVERLAY_SCALE = 0.2;
        var MAX_OVERLAY_SCALE = 4;
        var tryonState = { adjustMode: false, userBlobUrl: null, userW: 0, userH: 0, overlayUrl: null, baseLeft: 0, baseTop: 0, overlayW: 0, overlayH: 0, offsetX: 0, offsetY: 0, overlayScale: 1, compositeUrl: null, displayScale: 1 };

        if (!tryModalEl || !startTryBtn) return;

        function showToast(type, msg) {
            if (window.toastr) {
                if (type === 'warning') window.toastr.warning(msg);
                else window.toastr.error(msg);
            } else {
                alert(msg);
            }
        }

        function setPreviewFromFile(file) {
            if (!file || !selectedImg || !selectedPreview) return;
            var reader = new FileReader();
            reader.onload = function(e) {
                selectedImg.src = e.target.result;
                selectedImg.alt = file.name;
                selectedPreview.classList.remove('d-none');
                if (dropzone) dropzone.classList.add('tryon-has-file');
            };
            reader.readAsDataURL(file);
        }

        function clearSelection() {
            if (customerImage) customerImage.value = '';
            if (selectedPreview) selectedPreview.classList.add('d-none');
            if (selectedImg) selectedImg.src = '';
            if (dropzone) dropzone.classList.remove('tryon-has-file');
            startTryBtn.disabled = true;
        }

        function revokeUserBlob() {
            if (tryonState.userBlobUrl) {
                try { URL.revokeObjectURL(tryonState.userBlobUrl); } catch (e) {}
                tryonState.userBlobUrl = null;
            }
        }

        function showAdjustView(userBlobUrl, userW, userH, overlayUrl, left, top, w, h) {
            if (!resultAdjust || !resultBg || !resultOverlay || !positionControls) return;
            var maxW = (typeof window !== 'undefined' && window.innerWidth) ? Math.min(580, window.innerWidth - 48) : 580;
            var isNarrow = typeof window !== 'undefined' && window.innerWidth <= 576;
            var maxH = (typeof window !== 'undefined' && window.innerHeight) ? Math.floor(window.innerHeight * (isNarrow ? 0.38 : 0.42)) : (isNarrow ? 280 : 320);
            var scale = Math.min(maxW / userW, maxH / userH, 1);
            scale = Math.max(0.1, scale);
            tryonState.adjustMode = true;
            tryonState.userBlobUrl = userBlobUrl;
            tryonState.userW = userW;
            tryonState.userH = userH;
            tryonState.overlayUrl = overlayUrl;
            tryonState.baseLeft = left;
            tryonState.baseTop = top;
            tryonState.overlayW = w;
            tryonState.overlayH = h;
            tryonState.offsetX = 0;
            tryonState.offsetY = 0;
            tryonState.overlayScale = 1;
            tryonState.displayScale = scale;
            if (previewImage) previewImage.classList.add('d-none');
            resultAdjust.classList.remove('d-none');
            resultAdjust.style.width = Math.round(userW * scale) + 'px';
            resultAdjust.style.height = Math.round(userH * scale) + 'px';
            resultAdjust.style.maxWidth = '100%';
            resultBg.style.backgroundImage = 'url("' + userBlobUrl + '")';
            resultOverlay.src = overlayUrl;
            updateOverlayPosition();
            positionControls.classList.remove('d-none');
        }

        function updateOverlayPosition() {
            if (!resultOverlay) return;
            var s = tryonState.displayScale || 1;
            var sc = tryonState.overlayScale || 1;
            var cw = tryonState.overlayW * sc;
            var ch = tryonState.overlayH * sc;
            var cx = tryonState.baseLeft + tryonState.offsetX + tryonState.overlayW / 2;
            var cy = tryonState.baseTop + tryonState.offsetY + tryonState.overlayH / 2;
            var left = cx - cw / 2;
            var top = cy - ch / 2;
            var wPx = Math.round(cw * s) + 'px';
            var hPx = Math.round(ch * s) + 'px';
            resultOverlay.style.setProperty('width', wPx, 'important');
            resultOverlay.style.setProperty('height', hPx, 'important');
            resultOverlay.style.setProperty('max-width', 'none', 'important');
            resultOverlay.style.setProperty('max-height', 'none', 'important');
            resultOverlay.style.setProperty('left', Math.round(left * s) + 'px', 'important');
            resultOverlay.style.setProperty('top', Math.round(top * s) + 'px', 'important');
        }

        function resetModalState() {
            revokeUserBlob();
            tryonState.adjustMode = false;
            tryonState.compositeUrl = null;
            clearSelection();
            if (uploadStep) uploadStep.classList.remove('d-none');
            if (resultStep) resultStep.classList.add('d-none');
            if (previewImage) { previewImage.src = ''; previewImage.classList.remove('d-none'); previewImage.classList.remove('tryon-fade-in'); }
            if (resultAdjust) resultAdjust.classList.add('d-none');
            if (resultOverlay) resultOverlay.src = '';
            if (positionControls) positionControls.classList.add('d-none');
            startTryBtn.disabled = true;
            var btnText = startTryBtn.querySelector('.btn-text');
            var loader = startTryBtn.querySelector('.loader-inline');
            if (btnText) btnText.textContent = tryModalEl ? tryModalEl.getAttribute('data-msg-preview') || 'Preview' : 'Preview';
            if (btnText) btnText.classList.remove('d-none');
            if (loader) loader.classList.add('d-none');
            startTryBtn.classList.remove('btn-loading');
        }

        var tryonTipsTooltip = null;
        function initTryonPhotoTipsTooltip() {
            var trigger = document.getElementById('tryonPhotoTipsTrigger');
            if (!trigger || tryonTipsTooltip) return;
            if (window.bootstrap && window.bootstrap.Tooltip) {
                tryonTipsTooltip = new bootstrap.Tooltip(trigger, { container: 'body', trigger: 'hover focus' });
            }
        }

        document.querySelectorAll('.try-on-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                productId = btn.getAttribute('data-id');
                resetModalState();
                if (tryModal) tryModal.show();
                setTimeout(initTryonPhotoTipsTooltip, 200);
            });
        });
        if (tryModalEl) {
            tryModalEl.addEventListener('shown.bs.modal', function() {
                initTryonPhotoTipsTooltip();
            });
        }

        if (customerImage) {
            customerImage.addEventListener('change', function() {
                var file = this.files && this.files[0];
                if (file) setPreviewFromFile(file);
                else clearSelection();
                startTryBtn.disabled = !file;
            });
        }

        if (dropzone) {
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(function(ev) {
                dropzone.addEventListener(ev, function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    if (ev === 'drop') {
                        dropzone.classList.remove('tryon-dragover');
                        var files = e.dataTransfer && e.dataTransfer.files;
                        var file = files && files[0];
                        if (file && customerImage) {
                            var type = (file.type || '').toLowerCase();
                            if (type === 'image/jpeg' || type === 'image/jpg' || type === 'image/png') {
                                customerImage.files = files;
                                setPreviewFromFile(file);
                                startTryBtn.disabled = false;
                            }
                        }
                    } else if (ev === 'dragleave' || ev === 'drop') {
                        dropzone.classList.remove('tryon-dragover');
                    } else {
                        dropzone.classList.add('tryon-dragover');
                    }
                }, false);
            });
        }

        if (removeSelectionBtn) {
            removeSelectionBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                clearSelection();
            });
        }

        startTryBtn.addEventListener('click', function() {
            var file = customerImage && customerImage.files[0];
            if (!file) {
                showToast('warning', msgPleaseUpload);
                return;
            }

            var formData = new FormData();
            formData.append('image', file);
            formData.append('product_id', productId);
            var csrfToken = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : null;
            if (csrfToken) formData.append('_token', csrfToken);

            startTryBtn.disabled = true;
            var btnText = startTryBtn.querySelector('.btn-text');
            var loader = startTryBtn.querySelector('.loader-inline');
            if (btnText) { btnText.textContent = msgProcessing; }
            if (loader) loader.classList.remove('d-none');
            startTryBtn.classList.add('btn-loading');

            var headers = {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            };
            if (csrfToken) headers['X-CSRF-TOKEN'] = csrfToken;

            fetch(virtualTryUrl, {
                method: 'POST',
                body: formData,
                headers: headers
            })
            .then(function(response) { return response.json(); })
            .then(function(data) {
                if (data.success && data.image) {
                    tryonState.compositeUrl = data.image;
                    if (uploadStep) uploadStep.classList.add('d-none');
                    if (resultStep) resultStep.classList.remove('d-none');
                    if (data.overlay_image != null && data.overlay_left != null && data.overlay_top != null && data.overlay_width != null && data.overlay_height != null && file) {
                        var userBlobUrl = URL.createObjectURL(file);
                        var img = new Image();
                        img.onload = function() {
                            var uw = img.naturalWidth || img.width;
                            var uh = img.naturalHeight || img.height;
                            showAdjustView(userBlobUrl, uw, uh, data.overlay_image, data.overlay_left, data.overlay_top, data.overlay_width, data.overlay_height);
                        };
                        img.onerror = function() {
                            URL.revokeObjectURL(userBlobUrl);
                            previewImage.src = data.image;
                            previewImage.classList.add('tryon-fade-in');
                        };
                        img.src = userBlobUrl;
                    } else {
                        previewImage.src = data.image;
                        previewImage.classList.add('tryon-fade-in');
                    }
                } else {
                    showToast('error', data.message || msgError);
                }
            })
            .catch(function(err) {
                showToast('error', msgError);
            })
            .finally(function() {
                startTryBtn.disabled = false;
                if (btnText) { btnText.textContent = tryModalEl ? (tryModalEl.getAttribute('data-msg-preview') || 'Preview') : 'Preview'; }
                if (loader) loader.classList.add('d-none');
                startTryBtn.classList.remove('btn-loading');
            });
        });

        if (retryBtn) {
            retryBtn.addEventListener('click', function() {
                resetModalState();
            });
        }

        ['tryonMoveUp', 'tryonMoveDown', 'tryonMoveLeft', 'tryonMoveRight'].forEach(function(id) {
            var btn = document.getElementById(id);
            if (!btn) return;
            btn.addEventListener('click', function() {
                if (!tryonState.adjustMode) return;
                if (id === 'tryonMoveUp') tryonState.offsetY -= MOVE_STEP;
                else if (id === 'tryonMoveDown') tryonState.offsetY += MOVE_STEP;
                else if (id === 'tryonMoveLeft') tryonState.offsetX -= MOVE_STEP;
                else if (id === 'tryonMoveRight') tryonState.offsetX += MOVE_STEP;
                updateOverlayPosition();
            });
        });

        ['tryonWidthLess', 'tryonWidthMore'].forEach(function(id) {
            var btn = document.getElementById(id);
            if (!btn) return;
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                if (!tryonState.adjustMode) return;
                var sc = tryonState.overlayScale || 1;
                if (id === 'tryonWidthLess') {
                    tryonState.overlayScale = Math.max(MIN_OVERLAY_SCALE, sc - WIDTH_STEP);
                } else {
                    tryonState.overlayScale = Math.min(MAX_OVERLAY_SCALE, sc + WIDTH_STEP);
                }
                updateOverlayPosition();
            });
        });

        if (downloadBtn) {
            downloadBtn.addEventListener('click', function() {
                if (tryonState.adjustMode && tryonState.userBlobUrl && tryonState.overlayUrl) {
                    var canvas = document.createElement('canvas');
                    canvas.width = tryonState.userW;
                    canvas.height = tryonState.userH;
                    var ctx = canvas.getContext('2d');
                    var userImg = new Image();
                    userImg.onload = function() {
                        ctx.drawImage(userImg, 0, 0);
                        var overlayImg = new Image();
                        overlayImg.crossOrigin = 'anonymous';
                        overlayImg.onload = function() {
                            var sc = tryonState.overlayScale || 1;
                            var cw = tryonState.overlayW * sc;
                            var ch = tryonState.overlayH * sc;
                            var cx = tryonState.baseLeft + tryonState.offsetX + tryonState.overlayW / 2;
                            var cy = tryonState.baseTop + tryonState.offsetY + tryonState.overlayH / 2;
                            var drawLeft = cx - cw / 2;
                            var drawTop = cy - ch / 2;
                            ctx.drawImage(overlayImg, drawLeft, drawTop, cw, ch);
                            var link = document.createElement('a');
                            link.href = canvas.toDataURL('image/png');
                            link.download = 'virtual-try-on.png';
                            link.setAttribute('rel', 'noopener');
                            document.body.appendChild(link);
                            link.click();
                            document.body.removeChild(link);
                        };
                        overlayImg.onerror = function() {
                            if (tryonState.compositeUrl) {
                                var link = document.createElement('a');
                                link.href = tryonState.compositeUrl;
                                link.download = 'virtual-try-on.png';
                                link.setAttribute('rel', 'noopener');
                                document.body.appendChild(link);
                                link.click();
                                document.body.removeChild(link);
                            }
                        };
                        overlayImg.src = tryonState.overlayUrl;
                    };
                    userImg.onerror = function() {
                        if (tryonState.compositeUrl) {
                            var link = document.createElement('a');
                            link.href = tryonState.compositeUrl;
                            link.download = 'virtual-try-on.png';
                            link.setAttribute('rel', 'noopener');
                            document.body.appendChild(link);
                            link.click();
                            document.body.removeChild(link);
                        }
                    };
                    userImg.src = tryonState.userBlobUrl;
                } else if (previewImage && previewImage.src) {
                    var link = document.createElement('a');
                    link.href = previewImage.src;
                    link.download = 'virtual-try-on.png';
                    link.setAttribute('rel', 'noopener');
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                }
            });
        }
    });
})();
