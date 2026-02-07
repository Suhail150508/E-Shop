(function() {
    'use strict';
    
    /**
     * Hero Gallery Slider - One-by-One Image Transition
     * Changes one image at a time every 6 seconds with smooth animation
     * Cycles through 9 images (3 positions × 3 images each)
     */
    
    function initHeroSlider() {
        const galleryItems = document.querySelectorAll('.gallery-item');
        
        if (galleryItems.length === 0) {
            return;
        }
        
        // Track current image index for each position (0, 1, 2)
        const imageIndices = [0, 0, 0];
        const totalImagesPerPosition = 3;
        let currentPositionIndex = 0; // Which position to change next (0, 1, 2)
        let slideInterval = null;
        const intervalDuration = 6000; // 6 seconds
        
        // Initialize: Activate first image in each position
        function initializeImages() {
            galleryItems.forEach(function(item, positionIndex) {
                const images = item.querySelectorAll('.gallery-img');
                const badge = item.querySelector('.gallery-badge');
                
                images.forEach(function(img, imgIndex) {
                    if (imgIndex === 0) {
                        img.classList.add('active');
                        // Update badge text
                        if (badge && img.dataset.badge) {
                            badge.textContent = img.dataset.badge;
                        }
                    } else {
                        img.classList.remove('active');
                    }
                });
            });
        }
        
        // Change image in a specific position
        function changeImageInPosition(positionIndex) {
            const item = galleryItems[positionIndex];
            if (!item) return;
            
            const images = item.querySelectorAll('.gallery-img');
            const badge = item.querySelector('.gallery-badge');
            const currentImgIndex = imageIndices[positionIndex];
            const nextImgIndex = (currentImgIndex + 1) % totalImagesPerPosition;
            
            // Get current and next images
            const currentImg = images[currentImgIndex];
            const nextImg = images[nextImgIndex];
            
            if (!currentImg || !nextImg) return;
            
            // Remove active class from current image (triggers fade out)
            currentImg.classList.remove('active');
            
            // Update badge
            if (badge && nextImg.dataset.badge) {
                badge.textContent = nextImg.dataset.badge;
            }
            
            // Add active class to next image (triggers fade in)
            requestAnimationFrame(function() {
                nextImg.classList.add('active');
            });
            
            // Update index for this position
            imageIndices[positionIndex] = nextImgIndex;
        }
        
        // Rotate to next image (one position at a time)
        function rotateNextImage() {
            // Change image in current position
            changeImageInPosition(currentPositionIndex);
            
            // Move to next position (with wrapping)
            currentPositionIndex = (currentPositionIndex + 1) % galleryItems.length;
        }
        
        // Start auto-rotation
        function startSlider() {
            if (slideInterval) {
                clearInterval(slideInterval);
            }
            slideInterval = setInterval(rotateNextImage, intervalDuration);
        }
        
        // Pause slider on hover (better UX)
        function setupHoverPause() {
            const gallery = document.querySelector('.hero-gallery');
            if (!gallery) return;
            
            gallery.addEventListener('mouseenter', function() {
                if (slideInterval) {
                    clearInterval(slideInterval);
                    slideInterval = null;
                }
            });
            
            gallery.addEventListener('mouseleave', function() {
                startSlider();
            });
        }
        
        // Initialize everything
        initializeImages();
        startSlider();
        setupHoverPause();
        
        // Cleanup on page unload
        window.addEventListener('beforeunload', function() {
            if (slideInterval) {
                clearInterval(slideInterval);
            }
        });
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initHeroSlider);
    } else {
        initHeroSlider();
    }
})();

/**
 * Scroll-triggered animations: add .in-view when .fade-in enters viewport
 */
(function() {
    var fadeEls = document.querySelectorAll('.home-page .fade-in');
    if (!fadeEls.length) return;
    var io = typeof IntersectionObserver !== 'undefined' ? new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('in-view');
            }
        });
    }, { rootMargin: '0px 0px -40px 0px', threshold: 0.05 }) : null;
    if (io) {
        fadeEls.forEach(function(el) { io.observe(el); });
    } else {
        fadeEls.forEach(function(el) { el.classList.add('in-view'); });
    }
})();
