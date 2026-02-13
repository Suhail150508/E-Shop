document.addEventListener('DOMContentLoaded', function() {
    // Helper function to update cart badges
    window.updateCartBadges = function(count) {
        const cartBadges = document.querySelectorAll('.badge-count');
        cartBadges.forEach(badge => {
            // Check if this badge belongs to a cart icon/link
            const parentLink = badge.closest('a');
            if (parentLink && (parentLink.href.includes('cart') || parentLink.querySelector('.fa-shopping-cart'))) {
                badge.textContent = count;
                // Animation effect
                badge.style.transform = 'scale(1.5)';
                badge.style.transition = 'transform 0.2s';
                setTimeout(() => badge.style.transform = 'scale(1)', 200);
            }
        });

        // Also update floating cart count specifically if it exists
        const floatingCount = document.getElementById('floating-cart-count');
        if (floatingCount) {
            floatingCount.textContent = count;
            floatingCount.style.transform = 'scale(1.5)';
            setTimeout(() => floatingCount.style.transform = 'scale(1)', 200);
        }
    };
});

document.addEventListener('submit', function (e) {
    if (!e.target.matches('.add-to-cart-form')) return;

    e.preventDefault();
    const form = e.target;
    const submitBtn = form.querySelector('button[type="submit"]');
    let originalBtnContent = '';
    const transCart = window.translations?.cart || {};
    const transCommon = window.translations?.common || {};

    if (submitBtn) {
        originalBtnContent = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="loader-inline" role="status" aria-hidden="true"><span class="block"></span><span class="block"></span><span class="block"></span><span class="block"></span></span>';
    }

    // Flying Animation Logic
    const productCard = form.closest('.product-card');
    if (productCard) {
        const img = productCard.querySelector('.product-img');
        
        // Find the visible cart target (Priority: Floating -> Desktop/Mobile Header)
        let cartTarget = null;
        const floatingCart = document.getElementById('floating-cart');
        
        // Check if floating cart exists and is visible
        if (floatingCart) {
            const style = window.getComputedStyle(floatingCart);
            const rect = floatingCart.getBoundingClientRect();
            if (style.display !== 'none' && style.visibility !== 'hidden' && rect.width > 0 && rect.height > 0) {
                cartTarget = floatingCart;
            }
        }
        
        if (!cartTarget) {
            // Find all potential cart icons in the header
            const icons = document.querySelectorAll('.action-btn .fa-shopping-cart, .header-cart-icon');
            for (const icon of icons) {
                if (icon.offsetParent !== null) {
                    cartTarget = icon;
                    break;
                }
            }
        }
        
        if (img && cartTarget) {
            const imgClone = img.cloneNode(true);
            const imgRect = img.getBoundingClientRect();
            const cartRect = cartTarget.getBoundingClientRect();
            
            imgClone.style.position = 'fixed';
            imgClone.style.top = imgRect.top + 'px';
            imgClone.style.left = imgRect.left + 'px';
            imgClone.style.width = imgRect.width + 'px';
            imgClone.style.height = imgRect.height + 'px';
            imgClone.style.zIndex = '9999';
            imgClone.style.transition = 'all 1.5s cubic-bezier(0.19, 1, 0.22, 1)';
            imgClone.style.borderRadius = '12px';
            imgClone.style.pointerEvents = 'none';
            imgClone.style.boxShadow = '0 10px 25px rgba(0,0,0,0.2)';
            
            document.body.appendChild(imgClone);
            
            requestAnimationFrame(() => {
                imgClone.style.top = (cartRect.top + cartRect.height / 2 - 15) + 'px';
                imgClone.style.left = (cartRect.left + cartRect.width / 2 - 15) + 'px';
                imgClone.style.width = '30px';
                imgClone.style.height = '30px';
                imgClone.style.opacity = '0.7';
                imgClone.style.transform = 'scale(0.1) rotate(360deg)';
            });
            
            setTimeout(() => {
                imgClone.remove();
                cartTarget.classList.add('animate__animated', 'animate__headShake');
                setTimeout(() => cartTarget.classList.remove('animate__animated', 'animate__headShake'), 1000);
            }, 1500);
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
            if (window.updateCartBadges) {
                window.updateCartBadges(data.cartCount);
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
            if (icon) {
                if (data.added) {
                     icon.className = 'fas fa-heart text-danger';
                } else {
                     icon.className = 'far fa-heart';
                }
            }
            
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
        if (typeof toastr !== 'undefined') {
            toastr.error(transCommon.error_generic || 'Something went wrong. Please try again.');
        }
        if (icon) icon.className = originalIconClass;
    });
});

// Remove from Cart Logic (No Reload)
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
            // Remove row from DOM
            if (row) {
                row.remove();
                // Check if table is empty
                const tbody = document.querySelector('.cart-items-table tbody');
                if (tbody && tbody.children.length === 0) {
                     location.reload(); // Reload to show empty cart state if all items removed
                }
            }
            
            if (window.updateCartBadges) {
                window.updateCartBadges(data.cartCount);
            }

            // Update Totals
            if (data.subtotal) {
                const subtotalEl = document.getElementById('cart-summary-subtotal');
                if (subtotalEl) subtotalEl.textContent = data.subtotal;
            }
            if (data.total) {
                const totalEl = document.getElementById('cart-summary-total');
                if (totalEl) totalEl.textContent = data.total;
            }
        } else {
             if (typeof toastr !== 'undefined') {
                toastr.error(data.message || transCart.remove_failed || 'Error removing item');
             }
        }
    })
    .catch((error) => {
        if (typeof toastr !== 'undefined') {
            toastr.error(transCommon.error_generic || 'Something went wrong.');
        }
    });
});

// Clear Cart Logic (No Reload)
document.addEventListener('submit', function (e) {
    if (!e.target.matches('.clear-cart-form')) return;

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
    .then(res => {
        if (!res.ok) throw new Error('Network response was not ok');
        return res.json();
    })
    .then(data => {
        if (data.success) {
            if (typeof toastr !== 'undefined') {
                toastr.success(data.message || transCart.clear_success || 'Cart cleared');
            }
            
            if (window.updateCartBadges) {
                window.updateCartBadges(0);
            }
            
            // Reload to show empty cart state
            setTimeout(() => location.reload(), 500);
        } else {
             if (typeof toastr !== 'undefined') {
                toastr.error(data.message || transCart.clear_failed || 'Error clearing cart');
             }
        }
    })
    .catch((error) => {
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
        if (typeof toastr !== 'undefined') {
            toastr.error(transCommon.error_generic || 'Something went wrong.');
        }
    });
});

// Update Cart Logic (No Reload)
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
            
            if (window.updateCartBadges) {
                window.updateCartBadges(data.cartCount);
            }

            // Update Totals
            if (data.subtotal) {
                const subtotalEl = document.getElementById('cart-summary-subtotal');
                if (subtotalEl) subtotalEl.textContent = data.subtotal;
            }
            if (data.total) {
                const totalEl = document.getElementById('cart-summary-total');
                if (totalEl) totalEl.textContent = data.total;
            }
            if (data.item_total) {
                const actionParts = form.action.split('/');
                const rowId = actionParts[actionParts.length - 1];
                const itemSubtotalEl = document.getElementById('item-subtotal-' + rowId);
                if (itemSubtotalEl) itemSubtotalEl.textContent = data.item_total;
            }
        } else {
             if (typeof toastr !== 'undefined') {
                toastr.error(data.message || transCart.update_failed || 'Error updating cart');
             }
        }
    })
    .catch((error) => {
        if (typeof toastr !== 'undefined') {
            toastr.error(transCommon.error_generic || 'Something went wrong.');
        }
    });
});

// Coupon Logic (Apply)
document.addEventListener('click', function(e) {
    if (!e.target.matches('#apply_coupon_btn')) return;
    
    e.preventDefault();
    const btn = e.target;
    const input = document.getElementById('coupon_code');
    const messageDiv = document.getElementById('coupon_message');
    const code = input ? input.value : '';
    
    if (!code) {
        if (messageDiv) {
            messageDiv.className = 'small mt-1 text-danger';
            messageDiv.textContent = window.translations?.common?.enter_coupon_code || 'Please enter coupon code';
        }
        return;
    }
    
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    
    // We need the CSRF token
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    fetch('/checkout/coupon/apply', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ code: code })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            if (typeof toastr !== 'undefined') {
                toastr.success(data.message);
            }
            if (messageDiv) {
                messageDiv.className = 'small mt-1 text-success';
                messageDiv.textContent = data.message;
            }
            // Reload to update totals
            setTimeout(() => location.reload(), 1000);
        } else {
            if (typeof toastr !== 'undefined') {
                toastr.error(data.message);
            }
            if (messageDiv) {
                messageDiv.className = 'small mt-1 text-danger';
                messageDiv.textContent = data.message;
            }
        }
    })
    .catch(err => {
        if (typeof toastr !== 'undefined') {
            toastr.error('Something went wrong');
        }
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = originalText;
    });
});

// Coupon Logic (Remove)
document.addEventListener('click', function(e) {
    if (!e.target.matches('#remove_coupon_btn')) return;
    
    e.preventDefault();
    const btn = e.target;
    
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    fetch('/checkout/coupon/remove', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            if (typeof toastr !== 'undefined') {
                toastr.success(data.message);
            }
            // Reload to update totals
            setTimeout(() => location.reload(), 1000);
        } else {
            if (typeof toastr !== 'undefined') {
                toastr.error(data.message);
            }
        }
    })
    .catch(err => {
        if (typeof toastr !== 'undefined') {
            toastr.error('Something went wrong');
        }
    });
});
