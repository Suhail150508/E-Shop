/**
 * Admin layout: sidebar toggle, menu search, resize handling, and notifications.
 * Expects window.AdminLayoutConfig (csrfToken, userId, routes, labels) when notifications are used.
 */
(function () {
    'use strict';

    function initSidebar() {
        var sidebar = document.getElementById('sidebar');
        var mainWrapper = document.querySelector('.main-wrapper');
        var sidebarToggle = document.getElementById('sidebarToggle');
        var sidebarOverlay = document.getElementById('sidebarOverlay');
        var menuSearch = document.getElementById('menuSearch');

        if (!sidebar || !mainWrapper) return;

        function toggleSidebar() {
            if (window.innerWidth >= 992) {
                sidebar.classList.toggle('collapsed');
                mainWrapper.classList.toggle('collapsed');
            } else {
                sidebar.classList.toggle('show');
                if (sidebarOverlay) sidebarOverlay.classList.toggle('show');
            }
        }

        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', toggleSidebar);
        }

        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', function () {
                sidebar.classList.remove('show');
                sidebarOverlay.classList.remove('show');
            });
        }

        if (menuSearch) {
            menuSearch.addEventListener('keyup', function () {
                var filter = this.value.toLowerCase();
                var allLinks = document.querySelectorAll('.sidebar-menu .nav-link');
                var headers = document.querySelectorAll('.sidebar-menu .menu-header');
                var collapses = document.querySelectorAll('.sidebar-menu .collapse');

                if (filter === '') {
                    allLinks.forEach(function (link) { link.style.display = ''; });
                    headers.forEach(function (header) { header.style.display = ''; });
                    collapses.forEach(function (c) {
                        var hasActive = c.querySelector('.nav-link.active');
                        c.classList.toggle('show', !!hasActive);
                        var toggle = document.querySelector('[href="#' + c.id + '"]');
                        if (toggle) {
                            toggle.classList.toggle('collapsed', !hasActive);
                            toggle.setAttribute('aria-expanded', hasActive ? 'true' : 'false');
                        }
                    });
                    return;
                }

                headers.forEach(function (h) { h.style.display = 'none'; });
                allLinks.forEach(function (l) { l.style.display = 'none'; });
                allLinks.forEach(function (link) {
                    var text = link.textContent.toLowerCase();
                    if (text.indexOf(filter) === -1) return;
                    link.style.display = '';
                    if (link.getAttribute('data-bs-toggle') === 'collapse') {
                        var targetId = (link.getAttribute('href') || '').substring(1);
                        var targetCollapse = document.getElementById(targetId);
                        if (targetCollapse) {
                            targetCollapse.classList.add('show');
                            targetCollapse.querySelectorAll('.nav-link').forEach(function (c) { c.style.display = ''; });
                        }
                    }
                    var parentCollapse = link.closest('.collapse');
                    if (parentCollapse) {
                        parentCollapse.classList.add('show');
                        var parentToggle = document.querySelector('[href="#' + parentCollapse.id + '"]');
                        if (parentToggle) {
                            parentToggle.style.display = '';
                            parentToggle.classList.remove('collapsed');
                            parentToggle.setAttribute('aria-expanded', 'true');
                        }
                    }
                });
            });
        }

        window.addEventListener('resize', function () {
            if (window.innerWidth >= 992) {
                sidebar.classList.remove('show');
                if (sidebarOverlay) sidebarOverlay.classList.remove('show');
            } else {
                sidebar.classList.remove('collapsed');
                mainWrapper.classList.remove('collapsed');
            }
        });
    }

    function escapeHtml(text) {
        if (text == null) return '';
        var div = document.createElement('div');
        div.textContent = String(text);
        return div.innerHTML;
    }

    function initNotifications() {
        var config = window.AdminLayoutConfig;
        if (!config || !config.routes) return;

        var notificationList = document.getElementById('notification-list');
        var notificationCount = document.getElementById('notification-count');
        var noNotifications = document.getElementById('no-notifications');
        if (!notificationList) return;

        var csrfToken = config.csrfToken || '';
        var newLabel = config.labels && config.labels.newNotification ? config.labels.newNotification : 'New Notification';
        var markAsReadUrlTemplate = config.routes.markAsRead || '';

        function updateCount(count) {
            if (!notificationCount) return;
            notificationCount.textContent = count;
            notificationCount.classList.toggle('d-none', !(count > 0));
        }

        function addNotificationItem(notification, prepend) {
            if (noNotifications) noNotifications.classList.add('d-none');
            var data = notification.data || notification;
            var message = escapeHtml(data.message || newLabel);
            var link = (data.link && data.link !== '#') ? data.link : '#';
            var date = notification.created_at ? new Date(notification.created_at).toLocaleString() : new Date().toLocaleString();
            var item = document.createElement('a');
            item.href = link;
            item.className = 'dropdown-item p-3 border-bottom notification-item unread';
            item.dataset.id = notification.id;
            item.innerHTML = '<div class="d-flex align-items-start">' +
                '<div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2 me-3"><i class="bi bi-bag-check-fill" aria-hidden="true"></i></div>' +
                '<div><p class="mb-1 small fw-bold text-dark">' + message + '</p><small class="text-muted">' + escapeHtml(date) + '</small></div></div>';
            item.addEventListener('click', function (e) {
                e.preventDefault();
                window.markAsRead(notification.id, link);
            });
            if (prepend && notificationList.firstChild) {
                notificationList.insertBefore(item, notificationList.firstChild);
            } else {
                notificationList.appendChild(item);
            }
        }

        fetch(config.routes.unread, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                var list = Array.isArray(data) ? data : [];
                updateCount(list.length);
                if (list.length > 0) {
                    if (noNotifications) noNotifications.classList.add('d-none');
                    list.forEach(function (n) { addNotificationItem(n, false); });
                }
            })
            .catch(function () { updateCount(0); });

        if (window.Echo && config.userId) {
            window.Echo.private('App.Models.User.' + config.userId)
                .notification(function (notification) {
                    var count = parseInt(notificationCount && notificationCount.textContent ? notificationCount.textContent : 0, 10) || 0;
                    updateCount(count + 1);
                    addNotificationItem({
                        id: notification.id,
                        data: notification,
                        created_at: new Date().toISOString()
                    }, true);
                    if (typeof toastr !== 'undefined' && notification.message) {
                        toastr.info(escapeHtml(notification.message), newLabel);
                    }
                });
        }

        window.markAllRead = function () {
            fetch(config.routes.readAll, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            }).then(function () {
                var items = notificationList.querySelectorAll('.notification-item');
                items.forEach(function (item) { item.remove(); });
                if (noNotifications) noNotifications.classList.remove('d-none');
                updateCount(0);
            });
        };

        window.markAsRead = function (id, link) {
            var url = markAsReadUrlTemplate.replace('__ID__', encodeURIComponent(id));
            fetch(url, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            }).then(function () {
                if (link && link !== '#') {
                    window.location.href = link;
                } else {
                    notificationList.querySelectorAll('.notification-item').forEach(function (el) {
                        if (String(el.dataset.id) === String(id)) el.remove();
                    });
                    var count = parseInt(notificationCount && notificationCount.textContent ? notificationCount.textContent : 0, 10) || 0;
                    updateCount(count > 0 ? count - 1 : 0);
                    if (notificationList.querySelectorAll('.notification-item').length === 0 && noNotifications) {
                        noNotifications.classList.remove('d-none');
                    }
                }
            });
        };

        var markAllBtn = document.getElementById('markAllNotificationsRead');
        if (markAllBtn) markAllBtn.addEventListener('click', window.markAllRead);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            initSidebar();
            initNotifications();
        });
    } else {
        initSidebar();
        initNotifications();
    }
})();
