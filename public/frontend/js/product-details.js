/**
 * Product Details Page Scripts
 * Handles gallery, zoom, 360 view, quantity control. Virtual try-on is in tryon.js (loaded globally).
 */

document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('zoomContainer');
    const img = document.getElementById('mainProductImage');
    const zoomBtn = document.getElementById('toggleZoomBtn');
    const rotateBtn = document.getElementById('toggle360Btn');
    const thumbBtns = document.querySelectorAll('.thumb-btn');
    
    let isZoomEnabled = false;
    let is360Enabled = false;
    let images = [];
    
    // Collect images for 360 view
    if (thumbBtns.length > 0) {
        thumbBtns.forEach(btn => {
            const url = btn.getAttribute('data-image-url');
            if (url) images.push(url);
            else {
                const imgEl = btn.querySelector('img');
                if (imgEl) images.push(imgEl.src);
            }
        });
    } else if (img) {
        images.push(img.src);
    }

    // Share Buttons Logic
    document.querySelectorAll('.btn-share').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const url = encodeURIComponent(window.location.href);
            const title = encodeURIComponent(document.title);
            let shareUrl = '';

            const icon = this.querySelector('i');
            if (!icon) return;

            if (icon.classList.contains('fa-facebook-f')) {
                shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${url}`;
            } else if (icon.classList.contains('fa-twitter')) {
                shareUrl = `https://twitter.com/intent/tweet?url=${url}&text=${title}`;
            } else if (icon.classList.contains('fa-whatsapp')) {
                shareUrl = `https://api.whatsapp.com/send?text=${title}%20${url}`;
            }

            if (shareUrl) {
                window.open(shareUrl, 'share-dialog', 'width=600,height=400');
            }
        });
    });

    // Zoom Logic
    function enableZoom() {
        if (!container) return;
        isZoomEnabled = true;
        is360Enabled = false;
        container.classList.add('zoomed');
        container.classList.remove('mode-360');
        if(zoomBtn) zoomBtn.classList.add('active');
        if(rotateBtn) rotateBtn.classList.remove('active');
    }

    function disableZoom() {
        if (!container) return;
        isZoomEnabled = false;
        container.classList.remove('zoomed');
        if(zoomBtn) zoomBtn.classList.remove('active');
        if (img) {
            img.style.transform = 'scale(1)';
            img.style.transformOrigin = 'center center';
        }
    }

    // 360 Logic
    function enable360() {
        if(images.length < 2) return;
        is360Enabled = true;
        isZoomEnabled = false;
        if (container) {
            container.classList.add('mode-360');
            container.classList.remove('zoomed');
        }
        if(rotateBtn) rotateBtn.classList.add('active');
        if(zoomBtn) zoomBtn.classList.remove('active');
        if (img) img.style.transform = 'scale(1)'; // Reset zoom
    }

    function disable360() {
        is360Enabled = false;
        if (container) container.classList.remove('mode-360');
        if(rotateBtn) rotateBtn.classList.remove('active');
    }

    // Event Listeners
    if(zoomBtn) {
        zoomBtn.addEventListener('click', function() {
            if(isZoomEnabled) disableZoom();
            else enableZoom();
        });
    }

    if(rotateBtn) {
        rotateBtn.addEventListener('click', function() {
            if(is360Enabled) disable360();
            else enable360();
        });
    }

    // Mouse Events
    if (container) {
        container.addEventListener('mousemove', function(e) {
            if(isZoomEnabled && img) {
                const { left, top, width, height } = container.getBoundingClientRect();
                const x = e.clientX - left;
                const y = e.clientY - top;
                
                const xPercent = (x / width) * 100;
                const yPercent = (y / height) * 100;
                
                img.style.transformOrigin = `${xPercent}% ${yPercent}%`;
                img.style.transform = 'scale(2)';
            } else if(is360Enabled && img) {
                const { left, width } = container.getBoundingClientRect();
                const x = e.clientX - left;
                const percent = x / width;
                
                // Map position to image index
                const index = Math.floor(percent * images.length);
                const safeIndex = Math.min(Math.max(index, 0), images.length - 1);
                
                // Update image if changed
                if(img.src !== images[safeIndex]) {
                    img.src = images[safeIndex];
                    // Update active thumb
                    thumbBtns.forEach(b => b.classList.remove('active'));
                    if(thumbBtns[safeIndex]) thumbBtns[safeIndex].classList.add('active');
                }
            }
        });

        container.addEventListener('mouseleave', function() {
            if(isZoomEnabled && img) {
                img.style.transform = 'scale(1)';
            }
        });
    }

    // Default: Enable Zoom on Hover if not mobile
    if(window.innerWidth > 992) {
       enableZoom();
    }
});

// Global function for thumbs
window.updateMainImage = function(btn, src) {
    const mainImg = document.getElementById('mainProductImage');
    if (mainImg) mainImg.src = src;
    document.querySelectorAll('.thumb-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
};

window.increaseQty = function() {
    const input = document.getElementById('qtyInput');
    if (!input) return;
    const max = parseInt(input.getAttribute('max')) || 999;
    const val = parseInt(input.value) || 1;
    if(val < max) {
        input.value = val + 1;
    }
};

window.decreaseQty = function() {
    const input = document.getElementById('qtyInput');
    if (!input) return;
    const val = parseInt(input.value) || 1;
    if(val > 1) {
        input.value = val - 1;
    }
};

