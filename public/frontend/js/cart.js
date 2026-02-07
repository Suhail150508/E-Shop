document.addEventListener('submit', function (e) {
    if (!e.target.matches('.add-to-cart-form')) return;

    e.preventDefault();
    const form = e.target;
    const count = document.getElementById('floating-cart-count');
    const submitBtn = form.querySelector('button[type="submit"]');
    let originalBtnContent = '';
    const transCart = window.translations?.cart || {};
    const transCommon = window.translations?.common || {};

    if (submitBtn) {
        originalBtnContent = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    }

    // Flying Animation Logic
    const productCard = form.closest('.product-card');
    if (productCard) {
        const img = productCard.querySelector('.product-img');
        // Target the floating cart or the header cart icon
        const cartTarget = document.getElementById('floating-cart') || document.querySelector('.header-cart-icon') || document.querySelector('.fa-shopping-bag');
        
        if (img && cartTarget) {
            const imgClone = img.cloneNode(true);
            const imgRect = img.getBoundingClientRect();
            const cartRect = cartTarget.getBoundingClientRect();
            
            // Initial styles for the clone
            imgClone.style.position = 'fixed';
            imgClone.style.top = imgRect.top + 'px';
            imgClone.style.left = imgRect.left + 'px';
            imgClone.style.width = imgRect.width + 'px';
            imgClone.style.height = imgRect.height + 'px';
            imgClone.style.zIndex = '9999';
            imgClone.style.transition = 'all 0.8s cubic-bezier(0.19, 1, 0.22, 1)'; // Smooth ease-out
            imgClone.style.borderRadius = '12px';
            imgClone.style.pointerEvents = 'none';
            imgClone.style.boxShadow = '0 10px 25px rgba(0,0,0,0.2)';
            
            document.body.appendChild(imgClone);
            
            // Trigger animation after a brief delay
            requestAnimationFrame(() => {
                imgClone.style.top = (cartRect.top + cartRect.height / 2 - 15) + 'px'; // Center vertically
                imgClone.style.left = (cartRect.left + cartRect.width / 2 - 15) + 'px'; // Center horizontally
                imgClone.style.width = '30px';
                imgClone.style.height = '30px';
                imgClone.style.opacity = '0.5';
                imgClone.style.transform = 'scale(0.1)';
            });
            
            // Cleanup after animation
            setTimeout(() => {
                imgClone.remove();
                // Optional: Shake animation for cart icon
                cartTarget.classList.add('animate__animated', 'animate__headShake');
                setTimeout(() => cartTarget.classList.remove('animate__animated', 'animate__headShake'), 1000);
            }, 800);
        }
    }

    fetch(form.action, {
        method: 'POST',
        headers: { 
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: new FormData(form)
    })
    .then(res => {
        if (!res.ok) throw new Error('Network response was not ok');
        return res.json();
    })
    .then(data => {
        if (data.success) {
            if (count) {
                // Animate counter change
                count.style.transform = 'scale(1.5)';
                count.textContent = data.cartCount;
                setTimeout(() => count.style.transform = 'scale(1)', 200);
            }
            if (typeof toastr !== 'undefined') {
                toastr.success(data.message || transCart.add_success || 'Added to cart successfully');
            }
        } else {
             if (typeof toastr !== 'undefined') {
                toastr.error(data.message || transCart.add_failed || 'Failed to add to cart');
             }
        }
    })
    .catch((error) => {
        console.error('Error:', error);
        if (typeof toastr !== 'undefined') {
            toastr.error(transCommon.error_generic || 'Something went wrong. Please try again.');
        }
    })
    .finally(() => {
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnContent;
        }
    });
});

// Wishlist Logic
document.addEventListener('submit', function (e) {
    if (!e.target.matches('.add-to-wishlist-form')) return;

    e.preventDefault();
    const form = e.target;
    const btn = form.querySelector('button');
    const icon = btn ? btn.querySelector('i') : null;
    let originalIconClass = '';
    const transCommon = window.translations?.common || {};

    if (icon) {
        originalIconClass = icon.className;
        icon.className = 'fas fa-spinner fa-spin';
    }

    fetch(form.action, {
        method: 'POST',
        headers: { 
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: new FormData(form)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            if (typeof toastr !== 'undefined') {
                toastr.success(data.message);
            }
            // Update icon based on status
            if (icon) {
                if (data.added) {
                     icon.className = 'fas fa-heart text-danger'; // Filled heart
                } else {
                     icon.className = 'far fa-heart'; // Empty heart
                }
            }
            
            // Update wishlist count in navbar
            if (data.wishlistCount !== undefined) {
                const wishlistCounters = document.querySelectorAll('.action-btn[href*="wishlist"] .badge-count');
                wishlistCounters.forEach(counter => {
                    counter.textContent = data.wishlistCount;
                });
            }
        } else {
             if (typeof toastr !== 'undefined') {
                toastr.error(data.message || transCommon.error_generic || 'Error');
             }
             if (icon) icon.className = originalIconClass;
        }
    })
    .catch((error) => {
        console.error('Error:', error);
        if (typeof toastr !== 'undefined') {
            toastr.error(transCommon.error_generic || 'Something went wrong. Please try again.');
        }
        if (icon) icon.className = originalIconClass;
    });
});

// Remove from Cart Logic
document.addEventListener('submit', function (e) {
    if (!e.target.matches('.remove-from-cart-form')) return;

    e.preventDefault();
    const form = e.target;
    const row = form.closest('tr');
    const transCart = window.translations?.cart || {};
    const transCommon = window.translations?.common || {};
    
    fetch(form.action, {
        method: 'POST',
        headers: { 
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: new FormData(form)
    })
    .then(res => {
        if (!res.ok) throw new Error('Network response was not ok');
        return res.json();
    })
    .then(data => {
        if (data.success) {
            if (typeof toastr !== 'undefined') {
                toastr.success(data.message || transCart.remove_success || 'Item removed from cart');
            }
            // Reload to update totals (simple & reliable)
            setTimeout(() => location.reload(), 1000);
        } else {
             if (typeof toastr !== 'undefined') {
                toastr.error(data.message || transCart.remove_failed || 'Error removing item');
             }
        }
    })
    .catch((error) => {
        console.error('Error:', error);
        if (typeof toastr !== 'undefined') {
            toastr.error(transCommon.error_generic || 'Something went wrong.');
        }
    });
});

// Remove from Wishlist Logic
document.addEventListener('submit', function (e) {
    if (!e.target.matches('.remove-from-wishlist-form')) return;

    e.preventDefault();
    const form = e.target;
    const row = form.closest('tr');
    const transCommon = window.translations?.common || {};

    fetch(form.action, {
        method: 'POST',
        headers: { 
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: new FormData(form)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            if (typeof toastr !== 'undefined') {
                toastr.success(data.message || transCommon.wishlist_remove_success || 'Item removed from wishlist');
            }
            if (row) {
                row.remove();
                // Check if empty
                const tbody = document.querySelector('.wishlist-table tbody');
                if (tbody && tbody.children.length === 0) {
                    location.reload();
                }
            }
        } else {
             if (typeof toastr !== 'undefined') {
                toastr.error(data.message || transCommon.wishlist_remove_failed || 'Error');
             }
        }
    })
    .catch((error) => {
        console.error('Error:', error);
        if (typeof toastr !== 'undefined') {
            toastr.error(transCommon.error_generic || 'Something went wrong.');
        }
    });
});

// Update Cart Logic
document.addEventListener('submit', function (e) {
    if (!e.target.matches('.update-cart-form')) return;

    e.preventDefault();
    const form = e.target;
    const transCart = window.translations?.cart || {};
    const transCommon = window.translations?.common || {};
    
    fetch(form.action, {
        method: 'POST',
        headers: { 
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: new FormData(form)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            if (typeof toastr !== 'undefined') {
                toastr.success(data.message || transCart.update_success || 'Cart updated');
            }
            setTimeout(() => location.reload(), 1000);
        } else {
             if (typeof toastr !== 'undefined') {
                toastr.error(data.message || transCart.update_failed || 'Error updating cart');
             }
        }
    })
    .catch((error) => {
        console.error('Error:', error);
        if (typeof toastr !== 'undefined') {
            toastr.error(transCommon.error_generic || 'Something went wrong.');
        }
    });
});
