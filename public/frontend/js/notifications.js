(function() {
    'use strict';

    // Configuration
    const CONFIG = {
        routes: {
            index: '/notifications',
            markAllRead: '/notifications/mark-read',
            read: (id) => `/notifications/${id}/read`
        },
        selectors: {
            badge: '#customer-notification-badge',
            list: '#customer-notification-list',
            userIdMeta: 'meta[name="user-id"]'
        }
    };

    /**
     * Format date to "Time ago" style
     */
    function timeAgo(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const seconds = Math.floor((now - date) / 1000);

        const trans = window.translations?.common || {};

        let interval = seconds / 31536000;
        if (interval > 1) return Math.floor(interval) + " " + (trans.years_ago || "years ago");
        
        interval = seconds / 2592000;
        if (interval > 1) return Math.floor(interval) + " " + (trans.months_ago || "months ago");
        
        interval = seconds / 86400;
        if (interval > 1) return Math.floor(interval) + " " + (trans.days_ago || "days ago");
        
        interval = seconds / 3600;
        if (interval > 1) return Math.floor(interval) + " " + (trans.hours_ago || "hours ago");
        
        interval = seconds / 60;
        if (interval > 1) return Math.floor(interval) + " " + (trans.minutes_ago || "minutes ago");
        
        return trans.just_now || "Just now";
    }

    /**
     * Get icon based on notification type
     */
    function getIcon(type) {
        // Map types to FontAwesome icons
        const icons = {
            'order_status_updated': 'fas fa-truck-fast text-info',
            'new_order': 'fas fa-shopping-bag text-success',
            'payment_received': 'fas fa-money-check-alt text-success',
            'payment_failed': 'fas fa-exclamation-circle text-danger',
            'default': 'fas fa-bell text-primary'
        };
        return icons[type] || icons['default'];
    }

    /**
     * Notification Manager
     */
    const NotificationManager = {
        init() {
            this.badge = document.querySelector(CONFIG.selectors.badge);
            this.list = document.querySelector(CONFIG.selectors.list);
            this.userId = document.querySelector(CONFIG.selectors.userIdMeta)?.content;

            if (!this.userId) return; // Not logged in

            this.bindEvents();
            this.fetchNotifications();
            this.initEcho();
        },

        bindEvents() {
            // Expose global functions for onclick handlers
            window.markAllRead = (e) => {
                if(e) e.preventDefault();
                this.markAllRead();
            };
            
            window.markAsRead = (id, link) => {
                this.markAsRead(id, link);
            };
        },

        initEcho() {
            if (window.Echo) {
                window.Echo.private(`App.Models.User.${this.userId}`)
                    .notification((notification) => {
                        this.handleNewNotification(notification);
                    });
            }
        },

        fetchNotifications() {
            fetch(CONFIG.routes.index)
                .then(res => res.json())
                .then(data => this.renderList(data))
                .catch(err => console.error('Error fetching notifications:', err));
        },

        handleNewNotification(notification) {
            // Determine data structure (Broadcast vs Database)
            const data = notification.data || notification;
            const id = notification.id;
            const trans = window.translations?.common || {};

            // Update Badge
            this.updateBadgeCount(1, true);

            // Add to list (prepend)
            const html = this.buildItemHtml({
                id: id,
                data: data,
                created_at: new Date().toISOString()
            });
            
            // Remove empty state if present
            const emptyState = this.list.querySelector('.empty-state');
            if (emptyState) emptyState.remove();

            this.list.insertAdjacentHTML('afterbegin', html);

            // Show Toast
            if (typeof toastr !== 'undefined') {
                toastr.info(data.message, trans.new_notification || 'New Notification', {
                    onclick: () => this.markAsRead(id, data.link)
                });
            }
        },

        renderList(notifications) {
            const trans = window.translations?.common || {};
            if (notifications.length === 0) {
                this.list.innerHTML = `
                    <li class="p-4 text-center text-muted empty-state">
                        <i class="far fa-bell-slash fa-2x mb-2"></i>
                        <p class="mb-0 small">${trans.no_new_notifications || 'No new notifications'}</p>
                    </li>
                `;
                this.updateBadgeCount(0);
                return;
            }

            this.updateBadgeCount(notifications.length);
            this.list.innerHTML = notifications.map(n => this.buildItemHtml(n)).join('');
        },

        buildItemHtml(notification) {
            const data = notification.data || notification;
            const link = data.link || '#';
            const iconClass = getIcon(data.type);
            const time = timeAgo(notification.created_at || new Date());

            return `
                <li class="notification-item border-bottom">
                    <a href="javascript:void(0)" onclick="markAsRead('${notification.id}', '${link}')" class="dropdown-item p-3 d-flex align-items-start gap-3 text-wrap" style="white-space: normal;">
                        <div class="flex-shrink-0 mt-1">
                            <i class="${iconClass} fs-5"></i>
                        </div>
                        <div class="flex-grow-1">
                            <p class="mb-1 text-dark small">${data.message}</p>
                            <small class="text-muted" style="font-size: 0.75rem;">
                                <i class="far fa-clock me-1"></i>${time}
                            </small>
                        </div>
                    </a>
                </li>
            `;
        },

        updateBadgeCount(count, increment = false) {
            if (!this.badge) return;
            
            let current = parseInt(this.badge.textContent) || 0;
            let newCount = increment ? current + count : count;
            
            this.badge.textContent = newCount;
            if (newCount > 0) {
                this.badge.classList.remove('d-none');
            } else {
                this.badge.classList.add('d-none');
            }
        },

        markAllRead() {
            const trans = window.translations?.common || {};
            fetch(CONFIG.routes.markAllRead, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                    'Content-Type': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    this.renderList([]); // Clear list
                    if (typeof toastr !== 'undefined') {
                        toastr.success(trans.notifications_marked_read || 'All notifications marked as read');
                    }
                }
            });
        },

        markAsRead(id, link) {
            fetch(CONFIG.routes.read(id), {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                    'Content-Type': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success && link && link !== '#') {
                    window.location.href = link;
                } else {
                    // Remove item from DOM if not redirecting
                    const item = document.querySelector(`li a[onclick*="${id}"]`)?.closest('li');
                    if (item) {
                        item.remove();
                        this.updateBadgeCount(-1, true);
                        
                        if (this.list.children.length === 0) {
                            this.renderList([]);
                        }
                    }
                }
            });
        }
    };

    // Initialize
    document.addEventListener('DOMContentLoaded', () => NotificationManager.init());

})();
